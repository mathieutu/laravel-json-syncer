<?php

namespace MathieuTu\JsonImport\Traits;
use \MathieuTu\JsonImport\Helpers\JsonImporter as ImporterHelper;
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
