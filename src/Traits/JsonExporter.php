<?php

namespace MathieuTu\JsonImport\Traits;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Support\Collection;
use MathieuTu\JsonImport\Contracts\JsonExportable;
use MathieuTu\JsonImport\Helpers\RelationsInModelFinder;
use \MathieuTu\JsonImport\Helpers\JsonExporter as ExporterHelper;

trait JsonExporter
{
    protected $jsonExportableRelations;
    protected $jsonExportableAttributes;

    public function exportToJson($options = 0): string
    {
        return ExporterHelper::exportToJson($this, $options);
    }

    public function getJsonExportableAttributes(): array
    {
        return $this->jsonExportableAttributes ?? array_filter($this->getFillable(), function ($attribute) {
                return !ends_with($attribute, '_id');
            });
    }

    public function getJsonExportableRelations(): array
    {
        return $this->jsonExportableRelations ?? RelationsInModelFinder::hasOneOrMany($this);
    }


}
