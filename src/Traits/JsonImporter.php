<?php

namespace MathieuTu\JsonSyncer\Traits;

use MathieuTu\JsonSyncer\Helpers\JsonImporter as ImporterHelper;
use MathieuTu\JsonSyncer\Helpers\RelationsInModelFinder;

trait JsonImporter
{
    protected $jsonImportableAttributes;
    protected $jsonImportableRelations;

    public static function importFromJson($objects): void
    {
        ImporterHelper::importFromJson(new static, $objects);
    }

    public function getJsonImportableAttributes(): array
    {
        return $this->jsonImportableAttributes
            ?? $this->jsonExportableAttributes
            ?? $this->getFillable();
    }

    public function getJsonImportableRelations(): array
    {
        return $this->jsonImportableRelations
            ?? $this->jsonExportableRelations
            ?? RelationsInModelFinder::hasOneOrMany($this);
    }
}
