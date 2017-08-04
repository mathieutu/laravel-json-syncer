<?php

namespace MathieuTu\JsonSyncer\Tests\Stubs;

class Model extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;
    protected static function boot()
    {
        parent::boot();
        self::saving(function (Model $model) {
            foreach ((new static)->getFillable() as $attribute) {
                if (empty($model->$attribute)) {
                    $model->$attribute = str_random();
                }
            }
        });
    }
}
