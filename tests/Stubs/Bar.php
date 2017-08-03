<?php

namespace MathieuTu\JsonImport\Tests\Stubs;

use MathieuTu\JsonImport\Contracts\JsonExportable;
use MathieuTu\JsonImport\Contracts\JsonImportable;
use MathieuTu\JsonImport\Traits\JsonExporter;
use MathieuTu\JsonImport\Traits\JsonImporter;

class Bar extends Model implements JsonExportable, JsonImportable
{
    use JsonExporter, JsonImporter;

    protected $fillable = ['name', 'foo_id'];

    public function baz()
    {
        return $this->hasOne(Baz::class);
    }

}
