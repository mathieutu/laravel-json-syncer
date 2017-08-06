<?php

namespace MathieuTu\JsonSyncer\Contracts;

interface JsonExportable
{
    public function getJsonExportableAttributes(): array;

    public function getJsonExportableRelations(): array;

    public function exportToJson($jsonOptions = 0): string;
}
