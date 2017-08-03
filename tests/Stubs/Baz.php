<?php

namespace MathieuTu\JsonImport\Tests\Stubs;

use MathieuTu\JsonImport\Contracts\JsonExportable;
use MathieuTu\JsonImport\Contracts\JsonImportable;
use MathieuTu\JsonImport\Traits\JsonExporter;
use MathieuTu\JsonImport\Traits\JsonImporter;

class Baz extends Model implements JsonExportable, JsonImportable
{
    use JsonExporter, JsonImporter;

    protected $fillable = ['name', 'bar_id'];

    public function doNots()
    {
        return $this->hasMany(DoNotExport::class);
    }

}

