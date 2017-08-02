<?php

namespace MathieuTu\JsonImport\Contracts;

interface JsonImportable
{
    public static function importFromJson($objectsToCreate);

    public static function getJsonImportableRelations();
}
