<?php

namespace MathieuTu\JsonSyncer\Helpers;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Support\Collection;
use MathieuTu\JsonSyncer\Contracts\JsonExportable;

class JsonExporter
{
    private JsonExportable $exportable;

    public function __construct(JsonExportable $exportable)
    {
        $this->exportable = $exportable;
    }

    public static function exportToJson(JsonExportable $exportable, $options = 0): string
    {
        return self::exportToCollection($exportable)->toJson($options | JSON_THROW_ON_ERROR);
    }

    public static function exportToCollection(JsonExportable $exportable): Collection
    {
        $helper = new static($exportable);

        return $helper->exportAttributes()->merge($helper->exportRelations());
    }

    public function exportAttributes(): Collection
    {
        return collect($this->exportable->getJsonExportableAttributes())
            ->mapWithKeys(fn($attribute) => [$attribute => $this->exportable->$attribute]);
    }

    public function exportRelations(): Collection
    {
        return collect($this->exportable->getJsonExportableRelations())
            ->mapWithKeys(fn($relationName) => [$relationName => $this->exportable->$relationName()])
            ->filter(fn($relationObject) => $relationObject instanceof HasOneOrMany && $relationObject->getRelated() instanceof JsonExportable)
            ->map(function (HasOneOrMany $relationObject) {
                $export = $relationObject->get()->map(fn($object) => self::exportToCollection($object));

                return $relationObject instanceof HasOne ? $export->first() : $export;
            });
    }
}
