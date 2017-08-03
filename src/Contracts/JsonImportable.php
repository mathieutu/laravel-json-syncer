<?php

namespace MathieuTu\JsonImport\Contracts;

interface JsonImportable
{
    public static function importFromJson($objectsToCreate);

    public function getJsonImportableRelations($object = []);
}
