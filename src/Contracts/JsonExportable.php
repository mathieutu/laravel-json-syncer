<?php

namespace MathieuTu\JsonSyncer\Contracts;

use Illuminate\Support\Collection;

interface JsonExportable
{
    public function getJsonExportableAttributes(): array;

    public function getJsonExportableRelations(): array;

    public function exportToJson($jsonOptions = 0): string;

    public function exportToCollection(): Collection;
}
