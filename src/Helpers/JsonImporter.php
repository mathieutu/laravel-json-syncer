<?php

namespace MathieuTu\JsonSyncer\Helpers;

use BadMethodCallException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use MathieuTu\JsonSyncer\Contracts\JsonImportable;
use MathieuTu\JsonSyncer\Exceptions\UnknownAttributeException;

class JsonImporter
{
    private JsonImportable $importable;

    public function __construct(JsonImportable $importable)
    {
        $this->importable = $importable;
    }

    public static function importFromJson(JsonImportable $importable, $objects): void
    {
        (new static($importable))->import($objects);
    }

    public function import($objects): void
    {
        $objects = $this->convertObjectsToArray($objects);

        foreach ($objects as $attributes) {
            $object = $this->importAttributes($attributes);
            $this->importRelations($object, $attributes);
        }
    }

    protected function convertObjectsToArray($objects): array
    {
        if (is_string($objects)) {
            $objects = json_decode($objects, true, 512, JSON_THROW_ON_ERROR);
        }

        if (is_object($objects) && method_exists($objects, 'toArray')) {
            $objects = $objects->toArray();
        }

        return $this->wrap((array)$objects);
    }

    protected function wrap(array $objects): array
    {
        return (empty($objects) || is_array(reset($objects))) ? $objects : [$objects];
    }

    protected function importAttributes($attributes): JsonImportable
    {
        $attributes = Arr::only($attributes, $this->importable->getJsonImportableAttributes());

        return $this->importable instanceof Model ? $this->importable->create($attributes) : $this->importable;
    }

    protected function importRelations($object, $attributes): void
    {
        $relationsNames = array_intersect(array_keys($attributes), $this->importable->getJsonImportableRelations());

        foreach ($relationsNames as $relationName) {
            $children = $this->convertObjectsToArray($attributes[$relationName]);
            $relation = $this->getRelationObject($object, $relationName);

            $this->importChildrenIfImportable($relation, $children);
        }
    }

    protected function importChildrenIfImportable(HasOneOrMany $relation, array $children): void
    {
        $childClass = $relation->getRelated();
        if ($childClass instanceof JsonImportable) {
            $children = $this->addParentKeyToChildren($children, $relation);

            $childClass->importFromJson($children);
        }
    }

    protected function addParentKeyToChildren(array $children, HasOneOrMany $relation): array
    {
        return array_map(function ($object) use ($relation) {
            $object[$relation->getForeignKeyName()] = $relation->getParentKey();

            return $object;
        }, $children);
    }

    protected function getRelationObject($object, $relationName): HasOneOrMany
    {
        $relationName = Str::camel($relationName);

        try {
            $relation = $object->$relationName();

            if (!$relation instanceof HasOneOrMany) {
                throw new BadMethodCallException();
            }

            return $relation;
        } catch (BadMethodCallException $e) {
            $class = get_class($object);

            throw new UnknownAttributeException("Unknown attribute or HasOneorMany relation '$relationName' in '$class'.");
        }
    }
}
