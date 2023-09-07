<?php

namespace MathieuTu\JsonSyncer\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

readonly class RelationsInModelFinder
{
    public function __construct(private Model $model, private array $relationsType)
    {
    }

    public static function hasOneOrMany(Model $model): array
    {
        return (new static($model, ['hasMany', 'hasOne']))->find();
    }

    protected function find(): array
    {
        return collect(get_class_methods($this->model))->sort()
            ->reject($this->isAnEloquentMethod(...))
            ->filter(function (string $method) {
                $code = $this->getMethodCode($method);

                return collect($this->relationsType)
                    ->contains(fn(string $relation) => Str::contains($code, "\$this->{$relation}("));
            })->toArray();
    }

    protected function isAnEloquentMethod(string $method): bool
    {
        return Str::startsWith($method, 'get') ||
            Str::startsWith($method, 'set') ||
            Str::startsWith($method, 'scope') ||
            method_exists(Model::class, $method);
    }

    protected function getMethodCode(string $method): string
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
