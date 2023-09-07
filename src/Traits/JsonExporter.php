<?php

namespace MathieuTu\JsonSyncer\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MathieuTu\JsonSyncer\Helpers\JsonExporter as ExporterHelper;
use MathieuTu\JsonSyncer\Helpers\RelationsInModelFinder;

trait JsonExporter
{
    protected array $jsonExportableRelations;
    protected array $jsonExportableAttributes;

    public function exportToJson($jsonOptions = 0): string
    {
        return ExporterHelper::exportToJson($this, $jsonOptions);
    }

    public function exportToCollection(): Collection
    {
        return ExporterHelper::exportToCollection($this);
    }

    public function getJsonExportableAttributes(): array
    {
        return $this->jsonExportableAttributes
            ?? array_filter(
                $this->getFillable(),
                fn(string $attribute) => ! Str::endsWith($attribute, '_id')
            );
    }

    public function getJsonExportableRelations(): array
    {
        return $this->jsonExportableRelations
            ?? RelationsInModelFinder::hasOneOrMany($this);
    }

    public function isExporting(): bool
    {
        return collect(debug_backtrace())->contains('class', ExporterHelper::class);
    }
}
