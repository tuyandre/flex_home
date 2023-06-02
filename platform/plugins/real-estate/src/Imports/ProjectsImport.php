<?php

namespace Botble\RealEstate\Imports;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\Location\Repositories\Interfaces\CountryInterface;
use Botble\Location\Repositories\Interfaces\StateInterface;
use Botble\RealEstate\Contracts\OnSuccesses;
use Botble\RealEstate\Contracts\Typeable;
use Botble\RealEstate\Contracts\Validatable;
use Botble\RealEstate\Models\CustomFieldValue;
use Botble\RealEstate\Models\Project;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\RealEstate\Repositories\Interfaces\CategoryInterface;
use Botble\RealEstate\Repositories\Interfaces\CurrencyInterface;
use Botble\RealEstate\Repositories\Interfaces\FacilityInterface;
use Botble\RealEstate\Repositories\Interfaces\FeatureInterface;
use Botble\RealEstate\Repositories\Interfaces\InvestorInterface;
use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithValidation;
use Mimey\MimeTypes;
use Botble\Media\Facades\RvMedia;

class ProjectsImport implements ToModel, WithValidation, WithChunkReading, WithHeadingRow, WithMapping
{
    use Importable;
    use SkipsFailures;
    use SkipsErrors;
    use Validatable;
    use OnSuccesses;
    use Typeable;

    public function __construct(
        protected Request $request,
        protected CountryInterface $countryRepository,
        protected StateInterface $stateRepository,
        protected CityInterface $cityRepository,
        protected CurrencyInterface $currencyRepository,
        protected AccountInterface $accountRepository,
        protected CategoryInterface $categoryRepository,
        protected FacilityInterface $facilityRepository,
        protected FeatureInterface $featureRepository,
        protected CustomFieldValue $customFieldValue,
        protected InvestorInterface $investorRepository,
    ) {
    }

    public function model(array $row)
    {
        $project = new Project();
        $project->forceFill(Arr::except($row, ['categories', 'facilities', 'features', 'custom_fields']));
        $project->save();

        $project->categories()->sync(Arr::get($row, 'categories', []));
        $project->features()->sync(Arr::get($row, 'features', []));

        foreach (Arr::get($row, 'facilities', []) as $facilityId => $facilityValue) {
            $project->facilities()->attach($facilityId, ['distance' => $facilityValue]);
        }

        if ($customFields = Arr::get($row, 'custom_fields')) {
            $project->customFields()->save($customFields);
        }

        $this->request->merge([
            'slug' => Str::slug($project->name),
            'is_slug_editable' => true,
        ]);

        event(new CreatedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $this->request, $project));

        $this->onSuccess($project);
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function map($row): array
    {
        if (! empty($row['facilities'])) {
            $facilities = explode(',', Arr::get($row, 'facilities', ''));
            foreach ($facilities as $facility) {
                $facilityExplode = explode(':', $facility);
                $dataFacilities[Arr::first($this->getIdsFromString($facilityExplode[0], $this->facilityRepository))] = $facilityExplode[1];
            }
        } else {
            $dataFacilities = [];
        }

        $images = explode(',', Arr::get($row, 'images', ''));

        $project = [
            'name' => Arr::get($row, 'name'),
            'description' => Arr::get($row, 'description'),
            'content' => Arr::get($row, 'content'),
            'images' => $this->getImageURLs($images),
            'location' => Arr::get($row, 'location'),
            'number_block' => Arr::get($row, 'number_block'),
            'number_floor' => Arr::get($row, 'number_floor'),
            'number_flat' => Arr::get($row, 'number_flat'),
            'is_featured' => $this->yesNoToBoolean(Arr::get($row, 'is_featured', false)),
            'date_finish' => Arr::get($row, 'date_finish'),
            'date_sell' => Arr::get($row, 'date_sell'),
            'price_from' => Arr::get($row, 'price_from'),
            'price_to' => Arr::get($row, 'price_to'),
            'author_type' => Arr::get($row, 'author_type'),
            'longitude' => Arr::get($row, 'longitude'),
            'latitude' => Arr::get($row, 'latitude'),
            'status' => Arr::get($row, 'status'),
            'facilities' => $dataFacilities,
        ];

        return $this->mapRelationships($row, $project);
    }

    public function mapRelationships(mixed $row, array $project): array
    {
        $project['country_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'country'), $this->countryRepository));
        $project['state_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'state'), $this->stateRepository));
        $project['city_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'city'), $this->cityRepository));
        $project['author_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'author_id'), $this->accountRepository, ));
        $project['currency_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'currency'), $this->currencyRepository, 'title'));
        $project['investor_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'investor_id'), $this->investorRepository));
        $project['categories'] = $this->getIdsFromString(Arr::get($row, 'categories'), $this->categoryRepository);
        $project['features'] = $this->getIdsFromString(Arr::get($row, 'features'), $this->featureRepository);

        if ($customFields = Arr::get($row, 'custom_fields')) {
            $project['custom_fields'] = $this->customFieldValue->find($customFields);
        }

        return $project;
    }

    protected function getIdsFromString(string|null $value, RepositoryInterface $repository, string $column = 'name'): array|null
    {
        if (! $value) {
            return null;
        }

        $items = $this->stringToArray($value);

        $ids = [];

        foreach ($items as $index => $item) {
            if (is_numeric($item)) {
                $column = 'id';
            }

            $ids[$index] = $repository->getModel()->where($column, $item)->value('id');
        }

        return $ids;
    }

    protected function getImageURLs(array $images): array
    {
        $images = array_values(array_filter($images));

        foreach ($images as $key => $image) {
            $images[$key] = str_replace(RvMedia::getUploadURL() . '/', '', trim($image));
            if (Str::contains($images[$key], 'http://') || Str::contains($images[$key], 'https://')) {
                $images[$key] = $this->uploadImageFromURL($images[$key]);
            }
        }

        return $images;
    }

    protected function uploadImageFromURL(?string $url): ?string
    {
        if (empty($url)) {
            return $url;
        }

        $info = pathinfo($url);

        try {
            $contents = file_get_contents($url);
        } catch (Exception) {
            return $url;
        }

        if (empty($contents)) {
            return $url;
        }

        $path = '/tmp';

        if (! File::isDirectory($path)) {
            File::makeDirectory($path, 0755);
        }

        $path = $path . '/' . $info['basename'];

        file_put_contents($path, $contents);

        $mimeType = (new MimeTypes())->getMimeType(File::extension($url));

        $fileUpload = new UploadedFile($path, $info['basename'], $mimeType, null, true);

        $result = RvMedia::handleUpload($fileUpload, 0, 'projects');

        File::delete($path);

        if (! $result['error']) {
            $url = $result['data']->url;
        }

        return $url;
    }
}
