<?php

namespace MathieuTu\JsonImport\Contracts;

interface JsonExportable
{
    public function getJsonExportableAttributes(): array;

    public function getJsonExportableRelations(): array;

    public function exportToJson();
}
