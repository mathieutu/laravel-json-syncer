<?php

namespace MathieuTu\JsonSyncer\Traits;

use MathieuTu\JsonSyncer\Helpers\JsonExporter as ExporterHelper;
use MathieuTu\JsonSyncer\Helpers\RelationsInModelFinder;

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
