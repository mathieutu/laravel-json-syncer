<?php

namespace MathieuTu\JsonSyncer\Tests\Stubs;

use MathieuTu\JsonSyncer\Contracts\JsonExportable;
use MathieuTu\JsonSyncer\Contracts\JsonImportable;
use MathieuTu\JsonSyncer\Traits\JsonExporter;
use MathieuTu\JsonSyncer\Traits\JsonImporter;

class Foo extends Model implements JsonExportable, JsonImportable
{
    use JsonExporter, JsonImporter;

    protected $fillable = ['author', 'username'];

    public function bars()
    {
        return $this->hasMany(Bar::class);
    }

    public function otherMethod()
    {
        return 'Hello World!';
    }
}
