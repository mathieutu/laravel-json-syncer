<?php

namespace MathieuTu\JsonSyncer\Tests\Stubs;

use MathieuTu\JsonSyncer\Contracts\JsonExportable;
use MathieuTu\JsonSyncer\Contracts\JsonImportable;
use MathieuTu\JsonSyncer\Traits\JsonExporter;
use MathieuTu\JsonSyncer\Traits\JsonImporter;

class Bar extends Model implements JsonExportable, JsonImportable
{
    use JsonExporter, JsonImporter;

    protected $fillable = ['name', 'foo_id'];

    public function baz()
    {
        return $this->hasOne(Baz::class);
    }

}
