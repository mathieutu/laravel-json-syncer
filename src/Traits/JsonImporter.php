<?php

namespace MathieuTu\JsonSyncer\Traits;

use MathieuTu\JsonSyncer\Helpers\JsonImporter as ImporterHelper;

trait JsonImporter
{
    protected $jsonImportableRelations;

    public static function importFromJson($objects)
    {
        $importer = new ImporterHelper(new static);

        $importer->importFromJson($objects);
    }

    public function getJsonImportableRelations($attributes = []): array
    {
        return $this->jsonImportableRelations
            ?? $this->jsonExportableRelations
            ?? array_diff(array_keys($attributes), $this->getFillable());
    }
}
