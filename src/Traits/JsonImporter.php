<?php

namespace MathieuTu\JsonImport\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use MathieuTu\JsonImport\Contracts\JsonImportable;
use MathieuTu\JsonImport\Exceptions\JsonDecodingException;

trait JsonImporter
{
    protected $jsonImportableRelations = [];

    public function importFromJson($objects)
    {
        $objects = $this->convertObjectsToArray($objects);

        foreach ($objects as $attributes) {
            $object = $this->importAttributes($attributes);

            $this->importRelations($object, $attributes);
        }
    }

    protected function convertObjectsToArray($objects): mixed
    {
        if (is_string($objects)) {
            $objects = json_decode($objects, true);
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonDecodingException('Invalid json format.');
        }
        return $objects;
    }

    protected function importAttributes($attributes): Model
    {
        return $this instanceof Model ? $object = $this->create($attributes) : $this;
    }

    protected function importRelations($object, $attributes)
    {
        foreach ($this->getJsonImportableRelations($object) as $relationName) {
            $this->importChildrenIfImportable($object->$relationName, $attributes[$relationName]);
        }
    }

    public function getJsonImportableRelations($object): array
    {
        return $this->jsonImportableRelations
            ?? $this->jsonExportableRelations
            ?? array_diff(array_keys($object), $this->getFillable());
    }

    abstract public function getFillable(): array;

    protected function importChildrenIfImportable(HasOneOrMany $relation, array $children)
    {
        if (!$relation instanceof HasOneOrMany) {
            return;
        }

        $childClass = $relation->getRelated();

        if ($childClass instanceof JsonImportable) {
            $children = $this->addParentKeyToChildren($children, $relation);

            $childClass->importFromJson($children);
        }
    }

    protected function addParentKeyToChildren(array $children, HasOneOrMany $relation): array
    {
        foreach ($children as $object) {
            $object[$relation->getForeignKeyName()] = $relation->getParentKey();
        }

        return $children;
    }
}
