<?php
namespace MathieuTu\JsonImport\Tests\Stubs;

use MathieuTu\JsonImport\Contracts\JsonExportable;
use MathieuTu\JsonImport\Traits\JsonExporter;

class DoNotExport extends Model
{
    // This one isn't JsonExportable
    protected $fillable = ['name', 'baz_id'];

}
