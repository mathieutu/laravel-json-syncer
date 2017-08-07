<?php

namespace MathieuTu\JsonSyncer\Tests\Stubs;

class DoNotExport extends Model
{
    // This one isn't JsonExportable
    protected $fillable = ['name', 'baz_id'];
}
