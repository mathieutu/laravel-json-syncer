<?php

namespace MathieuTu\JsonSyncer\Helpers;

use BadMethodCallException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Support\Str;
use MathieuTu\JsonSyncer\Contracts\JsonImportable;
use MathieuTu\JsonSyncer\Exceptions\JsonDecodingException;
use MathieuTu\JsonSyncer\Exceptions\UnknownAttributeException;

class JsonImporter
{
    private $importable;

    public function __construct(JsonImportable $importable)
    {
        $this->importable = $importable;
    }

    public static function importFromJson(JsonImportable $importable, $objects)
    {
        (new static($importable))->import($objects);
    }

    public function import($objects)
    {
        $objects = $this->convertObjectsToArray($objects);

        $objects = $this->wrap($objects);

        foreach ($objects as $attributes) {
            $object = $this->importAttributes($attributes);

            $this->importRelations($object, $attributes);
        }
    }

    protected function convertObjectsToArray($objects): array
    {
        if (is_string($objects)) {
            $objects = json_decode($objects, true);
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonDecodingException('Invalid json format.');
        }

        if ($objects instanceof \Illuminate\Contracts\Support\Arrayable) {
            $objects = $objects->toArray();
        }

        return (array) $objects;
    }

    protected function wrap(array $objects): array
    {
        return empty($objects) || is_array(reset($objects)) ? $objects : [$objects];
    }

    protected function importAttributes($attributes): JsonImportable
    {
        $attributes = array_only($attributes, $this->importable->getJsonImportableAttributes());

        return $this->importable instanceof Model ? $object = $this->importable->create($attributes) : $this->importable;
    }

    protected function importRelations($object, $attributes)
    {
        $relationsNames = array_intersect(array_keys($attributes), $this->importable->getJsonImportableRelations());

        foreach ($relationsNames as $relationName) {
            $this->importChildrenIfImportable($this->getRelationObject($object, $relationName), $this->wrap($attributes[$relationName]));
        }
    }

    protected function importChildrenIfImportable(HasOneOrMany $relation, array $children)
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
            throw new UnknownAttributeException('Unknown attribute or HasOneorMany relation "' . $relationName . '" in "' . get_class($object) . '".');
        }
    }
}
