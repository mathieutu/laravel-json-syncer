<?php

namespace MathieuTu\JsonSyncer\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RelationsInModelFinder
{
    private $model;
    private $relationsType;

    public function __construct(Model $model, array $relationsType)
    {
        $this->model = $model;
        $this->relationsType = $relationsType;
    }

    public static function hasOneOrMany(Model $model)
    {
        return (new static($model, ['hasMany', 'hasOne']))->find();
    }

    protected function find()
    {
        return collect(get_class_methods($this->model))->sort()
            ->reject(function ($method) {
                return $this->isAnEloquentMethod($method);
            })->filter(function ($method) {
                $code = $this->getMethodCode($method);

                return collect($this->relationsType)->contains(function ($relation) use ($code) {
                    return Str::contains($code, '$this->' . $relation . '(');
                });
            })->toArray();
    }

    protected function isAnEloquentMethod($method): bool
    {
        return Str::startsWith($method, 'get') ||
            Str::startsWith($method, 'set') ||
            Str::startsWith($method, 'scope') ||
            method_exists(Model::class, $method);
    }

    protected function getMethodCode($method): string
    {
        $reflection = new \ReflectionMethod($this->model, $method);

        $file = new \SplFileObject($reflection->getFileName());
        $file->seek($reflection->getStartLine() - 1);

        $code = '';
        while ($file->key() < $reflection->getEndLine()) {
            $code .= $file->current();
            $file->next();
        }
        $code = trim(preg_replace('/\s\s+/', '', $code));

        return $code;
    }

}
