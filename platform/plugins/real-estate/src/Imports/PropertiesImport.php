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
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\RealEstate\Repositories\Interfaces\CategoryInterface;
use Botble\RealEstate\Repositories\Interfaces\CurrencyInterface;
use Botble\RealEstate\Repositories\Interfaces\FacilityInterface;
use Botble\RealEstate\Repositories\Interfaces\FeatureInterface;
use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Exception;
use Botble\Media\Facades\RvMedia;
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

class PropertiesImport implements
    ToModel,
    WithValidation,
    WithChunkReading,
    WithHeadingRow,
    WithMapping
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
    ) {
    }

    public function model(array $row)
    {
        $property = new Property();
        $property->forceFill(Arr::except($row, ['categories', 'facilities', 'features', 'custom_fields']));
        $property->save();

        $property->categories()->sync(Arr::get($row, 'categories', []));

        foreach (Arr::get($row, 'facilities', []) as $facilityId => $facilityValue) {
            $property->facilities()->attach($facilityId, ['distance' => $facilityValue]);
        }

        $property->features()->sync(Arr::get($row, 'features', []));

        if ($customFields = Arr::get($row, 'custom_fields')) {
            $property->customFields()->save($customFields);
        }

        $this->request->merge([
            'slug' => Str::slug($property->name),
            'is_slug_editable' => true,
        ]);

        event(new CreatedContentEvent(PROPERTY_MODULE_SCREEN_NAME, $this->request, $property));

        $this->onSuccess($property);
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

        $property = [
            'name' => Arr::get($row, 'name'),
            'type' => Arr::get($row, 'type'),
            'description' => Arr::get($row, 'description'),
            'price' => Arr::get($row, 'price'),
            'number_bedroom' => Arr::get($row, 'number_bedroom'),
            'number_bathroom' => Arr::get($row, 'number_bathroom'),
            'number_floor' => Arr::get($row, 'number_floor'),
            'square' => Arr::get($row, 'square'),
            'images' => $this->getImageURLs($images),
            'author_type' => Arr::get($row, 'author_type'),
            'is_featured' => $this->yesNoToBoolean(Arr::get($row, 'is_featured', false)),
            'content' => Arr::get($row, 'content'),
            'location' => Arr::get($row, 'location'),
            'longitude' => Arr::get($row, 'longitude'),
            'latitude' => Arr::get($row, 'latitude'),
            'auto_renew' => $this->yesNoToBoolean(Arr::get($row, 'auto_renew', false)),
            'expire_date' => Arr::get($row, 'expire_date'),
            'never_expired' => $this->yesNoToBoolean(Arr::get($row, 'never_expired', false)),
            'period' => Arr::get($row, 'period'),
            'moderation_status' => Arr::get($row, 'moderation_status'),
            'status' => Arr::get($row, 'status'),
            'project_id' => Arr::get($row, 'project'),
            'facilities' => $dataFacilities,
        ];

        return $this->mapRelationships($row, $property);
    }

    public function mapRelationships(mixed $row, array $property): array
    {
        $property['country_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'country'), $this->countryRepository));
        $property['state_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'state'), $this->stateRepository));
        $property['city_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'city'), $this->cityRepository));
        $property['author_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'author'), $this->accountRepository));
        $property['currency_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'currency'), $this->currencyRepository, 'title'));
        $property['categories'] = $this->getIdsFromString(Arr::get($row, 'categories'), $this->categoryRepository);
        $property['features'] = $this->getIdsFromString(Arr::get($row, 'features'), $this->featureRepository);

        if ($customFields = Arr::get($row, 'custom_fields')) {
            $property['custom_fields'] = $this->customFieldValue->find($customFields);
        }

        return $property;
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

        return array_filter($ids);
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

        $result = RvMedia::handleUpload($fileUpload, 0, 'properties');

        File::delete($path);

        if (! $result['error']) {
            $url = $result['data']->url;
        }

        return $url;
    }
}
