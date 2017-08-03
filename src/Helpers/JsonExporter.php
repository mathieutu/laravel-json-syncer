<?php

namespace MathieuTu\JsonImport\Helpers;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Support\Collection;
use MathieuTu\JsonImport\Contracts\JsonExportable;

class JsonExporter
{
    private $exportable;

    public function __construct(JsonExportable $exportable)
    {
        $this->exportable = $exportable;
    }

    public static function exportToJson(JsonExportable $exportable, $options = 0)
    {
        return self::exportToCollection($exportable)->toJson($options);
    }

    public static function exportToCollection(JsonExportable $exportable): Collection
    {
        $helper = new static($exportable);

        return $helper->exportAttributes()->merge($helper->exportRelations());
    }

    public function exportAttributes(): Collection
    {
        return collect($this->exportable->getJsonExportableAttributes())
            ->mapWithKeys(function ($attribute) {
                return [$attribute => $this->exportable->$attribute];
            });
    }

    public function exportRelations(): Collection
    {
        return collect($this->exportable->getJsonExportableRelations())
            ->mapWithKeys(function ($relationName) {
                return [$relationName => $this->exportable->$relationName()];
            })->filter(function ($relationObject) {
                return $relationObject instanceof HasOneOrMany
                    && $relationObject->getRelated() instanceof JsonExportable;
            })->map(function (HasOneOrMany $relationObject) {
                $export = $relationObject->get()->map(function ($object) {
                    return self::exportToCollection($object);
                });

                return $relationObject instanceof HasOne ? $export->first() : $export;
            });
    }
}
