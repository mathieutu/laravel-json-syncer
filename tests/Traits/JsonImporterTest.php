<?php

namespace MathieuTu\JsonImport\Tests\Traits;

use MathieuTu\JsonImport\Exceptions\JsonDecodingException;
use MathieuTu\JsonImport\Exceptions\UnknownAttributeException;
use MathieuTu\JsonImport\Tests\Stubs\Bar;
use MathieuTu\JsonImport\Tests\Stubs\Baz;
use MathieuTu\JsonImport\Tests\Stubs\DoNotExport;
use MathieuTu\JsonImport\Tests\Stubs\Foo;
use MathieuTu\JsonImport\Tests\TestCase;

class JsonImporterTest extends TestCase
{
    public function testImportFromJson()
    {
        Foo::importFromJson(file_get_contents(__DIR__ . '/../Stubs/import.json'));

        $this->assertIsImportedInDatabase();

    }

    protected function assertIsImportedInDatabase()
    {
        $this->assertEquals(1, Foo::count());
        $this->assertEquals(2, Bar::count());
        $this->assertEquals(2, Baz::count());
        $this->assertEquals(0, DoNotExport::count());


        $foo = Foo::first();
        $this->assertEquals(['id' => 1, "author" => "Mathieu TUDISCO", "username" => "@mathieutu"], $foo->toArray());

        $bars = $foo->bars;
        $this->assertEquals([
            ['id' => 1, 'name' => 'bar1', 'foo_id' => 1],
            ['id' => 2, 'name' => 'bar2', 'foo_id' => 1],
        ], $bars->toArray());

        $bars->load('baz');
        foreach ($bars as $bar) {
            $baz = $bar->baz;
            $this->assertEquals(["id" => $baz->id, "name" => $bar->name . "_baz", "bar_id" => $bar->id], $baz->toArray());
        }
    }

    public function testImportBadData()
    {
        $this->expectException(UnknownAttributeException::class);
        $this->expectExceptionMessage('Unknown attribute or relation "bad" in "MathieuTu\\JsonImport\\Tests\\Stubs\\Foo".');

        Foo::importFromJson(['bad' => 'test']);
    }

    public function testImportMethodWhichIsNotARelation()
    {
        $this->expectException(UnknownAttributeException::class);
        $this->expectExceptionMessage('Unknown attribute or relation "otherMethod" in "MathieuTu\\JsonImport\\Tests\\Stubs\\Foo".');

        Foo::importFromJson(['otherMethod' => 'Hello you!']);
    }

    public function testImportFromArray()
    {
        Foo::importFromJson(json_decode(file_get_contents(__DIR__ . '/../Stubs/import.json'), true));

        $this->assertIsImportedInDatabase();
    }

    public function testImportBadJson()
    {
        $this->expectException(JsonDecodingException::class);
        Foo::importFromJson('badJson');
    }
}
