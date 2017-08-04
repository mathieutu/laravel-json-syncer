<?php

namespace MathieuTu\JsonSyncer\Tests\Stubs;

use MathieuTu\JsonSyncer\Contracts\JsonExportable;
use MathieuTu\JsonSyncer\Contracts\JsonImportable;
use MathieuTu\JsonSyncer\Traits\JsonExporter;
use MathieuTu\JsonSyncer\Traits\JsonImporter;

class Baz extends Model implements JsonExportable, JsonImportable
{
    use JsonExporter, JsonImporter;

    protected $fillable = ['name', 'bar_id'];

    public function doNots()
    {
        return $this->hasMany(DoNotExport::class);
    }

}

