<?php

namespace MathieuTu\JsonSyncer\Tests;

use MathieuTu\JsonSyncer\Tests\Stubs\Bar;
use MathieuTu\JsonSyncer\Tests\Stubs\Foo;

class JsonExporterTest extends TestCase
{
    public function testExportToJson()
    {
        $this->setDatabase();

        $this->assertEquals(
            json_decode(file_get_contents(__DIR__ . '/Stubs/import.json')),
            json_decode(Foo::query()->first()->exportToJson())
        );
    }

    public function testExportToCollection()
    {
        $this->setDatabase();

        $this->assertEquals(
            json_decode(file_get_contents(__DIR__ . '/Stubs/import.json'), true),
            Foo::query()->first()->exportToCollection()->toArray()
        );
    }

    public function testIsExportingMethod()
    {
        $this->setDatabase();

        $foo = $this->testingFooModel()->firstOrFail();

        $this->assertEquals('Mathieu TUDISCO', $foo->author);

        $this->expectExceptionMessage('Is exporting ok !');
        $foo->exportToJson();
    }

    protected function setDatabase()
    {
        (new Foo)->create(['author' => 'Mathieu TUDISCO', 'username' => '@mathieutu'])
            ->bars()->createMany([
                ['name' => 'bar1'],
                ['name' => 'bar2'],
            ])->each(function (Bar $bar) {
                $bar->baz()->create(['name' => $bar->name . '_baz'])
                    ->doNots()->createMany([
                        ['name' => 'do not 1'],
                        ['name' => 'do not 2'],
                        ['name' => 'do not 3'],
                    ]);
            });
    }

    protected function testingFooModel()
    {
        return new class extends Foo
        {
            protected $fillable = ['author'];
            protected $table = 'foos';

            public function getAuthorAttribute($value)
            {
                if ($this->isExporting()) {
                    throw new \Exception('Is exporting ok !');
                }

                return $value;
            }
        };
    }
}
