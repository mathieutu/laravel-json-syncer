<?php

namespace MathieuTu\JsonSyncer\Tests;

use MathieuTu\JsonSyncer\Exceptions\JsonDecodingException;
use MathieuTu\JsonSyncer\Exceptions\UnknownAttributeException;
use MathieuTu\JsonSyncer\Tests\Stubs\Bar;
use MathieuTu\JsonSyncer\Tests\Stubs\Baz;
use MathieuTu\JsonSyncer\Tests\Stubs\DoNotExport;
use MathieuTu\JsonSyncer\Tests\Stubs\Foo;

class JsonImporterTest extends TestCase
{
    public function testImportFromJson()
    {
        Foo::importFromJson(file_get_contents(__DIR__ . '/Stubs/import.json'));
        $this->assertAllImportedInDatabase();
    }

    protected function assertAllImportedInDatabase()
    {
        $this->assertFooIsImported();
        $this->assertBarsAreImported();
        $this->assertBazsAreImported();
        $this->assertEquals(0, DoNotExport::count());
    }

    protected function assertFooIsImported()
    {
        $this->assertEquals(1, Foo::count());
        $this->assertEquals(
            ['id' => 1, 'author' => 'Mathieu TUDISCO', 'username' => '@mathieutu'],
            Foo::first()->toArray()
        );
    }

    protected function assertBarsAreImported()
    {
        $this->assertEquals(2, Bar::count());
        $bars = Foo::first()->bars;
        $this->assertEquals([
            ['id' => 1, 'name' => 'bar1', 'foo_id' => 1],
            ['id' => 2, 'name' => 'bar2', 'foo_id' => 1],
        ], $bars->toArray());
    }

    protected function assertBazsAreImported()
    {
        $this->assertEquals(2, Baz::count());
        foreach (Foo::with('bars.baz')->first()->bars as $bar) {
            $baz = $bar->baz;
            $this->assertEquals(['id' => $baz->id, 'name' => $bar->name . '_baz', 'bar_id' => $bar->id], $baz->toArray());
        }
    }

    public function testImportWithoutRelation()
    {
        $import = $this->getImportArray();

        unset($import['bars'][1]['baz']);

        Foo::importFromJson($import);

        $this->assertFooIsImported();
        $this->assertBarsAreImported();

        $this->assertEquals(1, Baz::count());
        $baz = Foo::with('bars.baz')->first()->bars->pluck('baz');
        $this->assertEquals([['id' => 1, 'name' => 'bar1_baz', 'bar_id' => '1'], null], $baz->toArray());
    }

    private function getImportArray(): array
    {
        return json_decode(file_get_contents(__DIR__ . '/Stubs/import.json'), true);
    }

    public function testImportWithAnEmptyRelation()
    {
        $import = $this->getImportArray();

        $import['bars'] = [];

        $import = json_encode($import);

        Foo::importFromJson($import);

        $this->assertEquals(1, Foo::count());
        $this->assertEquals(0, Bar::count());
        $this->assertEquals(0, Baz::count());
        $this->assertEquals(0, DoNotExport::count());
    }

    public function testImportSeveralTimesWillJustAdd()
    {
        $import = $this->getImportArray();

        Foo::importFromJson($import);

        $fooCount = Foo::count();
        $barCount = Bar::count();
        $bazCount = Baz::count();
        $doNotExportCount = DoNotExport::count();

        foreach (range(1, 3) as $time) {
            $this->assertEquals($time * $fooCount, Foo::count());
            $this->assertEquals($time * $barCount, Bar::count());
            $this->assertEquals($time * $bazCount, Baz::count());
            $this->assertEquals($time * $doNotExportCount, DoNotExport::count());

            Foo::importFromJson($import);
        }
    }

    public function testImportFromArray()
    {
        Foo::importFromJson($this->getImportArray());

        $this->assertAllImportedInDatabase();
    }

    public function testImportNullRelation()
    {
        $objects = $this->getImportArray();
        $objects['bars'] = null;

        Foo::importFromJson($objects);

        $this->assertFooIsImported();

        $this->assertEquals(0, Bar::count());
        $bars = Foo::first()->bars;
        $this->assertEquals([], $bars->toArray());

        $this->assertEquals(0, Baz::count());
        $this->assertEquals(0, DoNotExport::count());
    }

    public function testImportNullAttribute()
    {
        $objects = $this->getImportArray();
        $objects['author'] = null;

        Foo::importFromJson($objects);

        $this->assertEquals(1, Foo::count());
        $this->assertEquals(['id' => 1, 'author' => null, 'username' => '@mathieutu'], Foo::first()->toArray());

        $this->assertBarsAreImported();
        $this->assertBazsAreImported();
        $this->assertEquals(0, DoNotExport::count());
    }

    public function testImportBadJson()
    {
        $this->expectException(JsonDecodingException::class);

        Foo::importFromJson('badJson');
    }

    public function testImportUnWantedData()
    {
        $import = $this->getImportArray();
        $import['bad'] = 'attribute';

        Foo::importFromJson($import);

        $this->assertAllImportedInDatabase();
    }

    public function testImportNonRelationMethodWithDefaultRelations()
    {
        $import = $this->getImportArray();
        $import['otherMethod'] = ['Hello you!'];

        Foo::importFromJson($import);

        $this->assertAllImportedInDatabase();
    }

    public function testImportANonRelationMethodWithCustomRelations()
    {
        $this->expectException(UnknownAttributeException::class);
        $this->expectExceptionMessage("Unknown attribute or HasOneorMany relation 'otherMethod' in 'MathieuTu\\JsonSyncer\\Tests\\Stubs\\Foo'");

        $import = $this->getImportArray();
        $import['otherMethod'] = [];

        (new Foo)->setJsonImportableRelationsForTests(['bars', 'otherMethod'])
            ->instanceImportForTests($import);
    }

    public function testImportUnknownRelationWithCustomRelations()
    {
        $this->expectException(UnknownAttributeException::class);
        $this->expectExceptionMessage("Unknown attribute or HasOneorMany relation 'test' in 'MathieuTu\\JsonSyncer\\Tests\\Stubs\\Foo'.");

        $import = $this->getImportArray();
        $import['test'] = [];

        (new Foo)->setJsonImportableRelationsForTests(['bars', 'test'])
            ->instanceImportForTests($import);
    }

    public function testImportWithCustomRelationsAndAttributes()
    {
        $import = $this->getImportArray();

        (new Foo)->setJsonImportableRelationsForTests(['bars'])
            ->setJsonImportableAttributesForTests(['author'])
            ->instanceImportForTests($import);

        $this->assertEquals(['id' => 1, 'author' => 'Mathieu TUDISCO', 'username' => null], Foo::first()->toArray());
        $this->assertBarsAreImported();
        $this->assertBazsAreImported();
    }

    public function testIsImportingMethod()
    {
        $import = [
            'author' => 'Mathieu',
        ];

        $foo = new class extends Foo
        {
            protected $fillable = ['author'];
            protected $table = 'foos';

            public function setAuthorAttribute()
            {
                if ($this->isImporting()) {
                    throw new \Exception('Is importing ok !');
                }

                $this->attributes['author'] = 'not importing';
            }
        };

        $foo->author = 'test';
        $this->assertEquals('not importing', $foo->author);

        $this->expectExceptionMessage('Is importing ok !');

        $foo->setJsonImportableAttributesForTests(array_keys($import))
            ->instanceImportForTests($import);
    }

    public function testImportNonImportableObjects()
    {
        $baz = [
            'name' => 'baz_test',
            'doNots' => [
                'name' => 'Do not import !',
            ],
        ];

        Baz::importFromJson($baz);
        $this->assertEquals(1, Baz::count());
        $this->assertEquals(0, DoNotExport::count());
    }
}
