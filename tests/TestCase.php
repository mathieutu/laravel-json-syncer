<?php

namespace MathieuTu\JSONImport\Tests;

use Illuminate\Database\Eloquent\Model;
use MathieuTu\JsonImport\Tests\Stubs\Bar;
use MathieuTu\JsonImport\Tests\Stubs\Baz;
use MathieuTu\JsonImport\Tests\Stubs\DoNotExport;
use MathieuTu\JsonImport\Tests\Stubs\Foo;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->createTable(new Foo());
        $this->createTable(new Bar());
        $this->createTable(new Baz());
        $this->createTable(new DoNotExport());
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'memory');
        $app['config']->set('database.connections.memory', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function createTable(Model $model)
    {
        $this->app['db']->connection()->getSchemaBuilder()->create(
            $model->getTable(),
            function (\Illuminate\Database\Schema\Blueprint $table) use ($model) {
                $table->increments('id');
                foreach ($model->getFillable() as $column) {
                    $table->string($column);
                }
            }
        );
    }
}
