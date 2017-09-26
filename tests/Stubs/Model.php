<?php

namespace MathieuTu\JsonSyncer\Tests\Stubs;

use MathieuTu\JsonSyncer\Helpers\JsonImporter;

class Model extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;

    public function setJsonImportableRelationsForTests($relations)
    {
        $this->jsonImportableRelations = $relations;

        return $this;
    }

    public function setJsonImportableAttributesForTests($attributes)
    {
        $this->jsonImportableAttributes = $attributes;

        return $this;
    }

    public function instanceImportForTests($objects)
    {
        JsonImporter::importFromJson($this, $objects);
    }
}
