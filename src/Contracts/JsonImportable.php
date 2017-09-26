<?php

namespace MathieuTu\JsonSyncer\Contracts;

interface JsonImportable
{
    public function getJsonImportableRelations(): array;

    public function getJsonImportableAttributes(): array;

    public static function importFromJson($objectsToCreate);

    public function isImporting(): bool;
}
