<?php

namespace MathieuTu\JsonSyncer\Contracts;

interface JsonImportable
{
    public static function importFromJson($objectsToCreate);

    public function getJsonImportableRelations($object = []);
}
