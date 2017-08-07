<?php

namespace MathieuTu\JsonSyncer\Tests\Stubs;

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
        $importer = new \MathieuTu\JsonSyncer\Helpers\JsonImporter($this);

        $importer->importFromJson($objects);
    }
}
