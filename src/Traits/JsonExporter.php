<?php

namespace MathieuTu\JsonImport\Traits;

use Illuminate\Support\Collection;
use MathieuTu\JsonImport\Helpers\RelationsInModelFinder;

trait JsonExporter
{
    protected $jsonExportableRelations = [];
    protected $jsonExportableAttributes = [];

    public function exportToJson($options = 0): string
    {
        return $this->exportToCollection()->toJson($options);
    }

    public function exportToCollection(): Collection
    {
        return $this->exportAttributes()->merge($this->exportRelations());
    }

    public function exportAttributes(): Collection
    {
        return collect($this->getJsonExportableAttributes())
            ->mapWithKeys(function ($attribute) {
                return [$attribute => $this->$attribute];
            });
    }

    public function getJsonExportableAttributes(): array
    {
        return $this->jsonExportableAttributes ?? array_filter($this->getFillable(), function ($attribute) {
                return !ends_with($attribute, '_id');
            });
    }

    abstract public function getFillable(): array;

    public function exportRelations(): Collection
    {
        return collect($this->getJsonExportableRelations())
            ->mapWithKeys(function ($relation) {
                return [$relation => $this->$relation->map->exportToCollection()];
            });
    }

    public function getJsonExportableRelations(): array
    {
        return $this->jsonExportableRelations ?? RelationsInModelFinder::children($this);
    }


}
