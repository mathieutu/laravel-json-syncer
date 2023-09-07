<?php

namespace MathieuTu\JsonSyncer\Contracts;

interface JsonImportable
{
    public function getJsonImportableRelations(): array;

    public function getJsonImportableAttributes(): array;

    public static function importFromJson(mixed $objectsToCreate): void;

    public function isImporting(): bool;
}
