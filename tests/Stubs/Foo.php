<?php
namespace MathieuTu\JsonImport\Tests\Stubs;

use MathieuTu\JsonImport\Contracts\JsonExportable;
use MathieuTu\JsonImport\Contracts\JsonImportable;
use MathieuTu\JsonImport\Traits\JsonExporter;
use MathieuTu\JsonImport\Traits\JsonImporter;

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
