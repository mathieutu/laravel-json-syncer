<?php

namespace MathieuTu\JsonImport\Tests\Helpers;

use MathieuTu\JsonImport\Helpers\RelationsInModelFinder;

class RelationsInModelFinderTest extends \PHPUnit\Framework\TestCase
{
    public function testhasOneOrMany()
    {
        $this->assertEquals(['foos', 'bar'], RelationsInModelFinder::hasOneOrMany(new MyModel()));
    }
}

class MyModel extends \Illuminate\Database\Eloquent\Model
{
    public function foos()
    {
        return $this->hasMany('foo');
    }

    public function bar()
    {
        return $this->hasOne('bar');
    }

    public function baz()
    {
        return $this->hasManyThrough('baz', 'bar');
    }

    public function notARelation()
    {
        return 'this is not a relation!';
    }

    public function parent()
    {
        return $this->belongsTo('parent');
    }

}
