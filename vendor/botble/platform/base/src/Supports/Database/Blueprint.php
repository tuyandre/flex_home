<?php

namespace Botble\Base\Supports\Database;

use Botble\Base\Models\BaseModel as Model;
use Illuminate\Database\Schema\Blueprint as IlluminateBlueprint;
use Illuminate\Database\Schema\ColumnDefinition;

class Blueprint extends IlluminateBlueprint
{
    public function id($column = 'id'): ColumnDefinition
    {
        if (Model::determineIfUsingUuidsForId()) {
            return $this->uuid($column)->primary();
        }

        return $this->bigIncrements($column);
    }

    public function foreignId($column): ColumnDefinition
    {
        if (Model::determineIfUsingUuidsForId()) {
            return $this->foreignUuid($column);
        }

        return parent::foreignId($column);
    }

    public function morphs($name, $indexName = null): void
    {
        if (Model::determineIfUsingUuidsForId()) {
            $this->uuidMorphs($name, $indexName);

            return;
        }

        parent::morphs($name, $indexName);
    }

    public function nullableMorphs($name, $indexName = null): void
    {
        if (Model::determineIfUsingUuidsForId()) {
            $this->nullableUuidMorphs($name, $indexName);

            return;
        }

        parent::nullableMorphs($name, $indexName);
    }
}
