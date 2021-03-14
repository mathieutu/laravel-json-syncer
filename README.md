# Laravel Json Syncer: Json importer and exporter for Laravel

[![Github checks](https://img.shields.io/github/checks-status/mathieutu/laravel-json-syncer/main.svg?style=flat-square)](https://github.com/mathieutu/laravel-json-syncer/actions) 
[![Test coverage](https://img.shields.io/scrutinizer/coverage/g/mathieutu/laravel-json-syncer.svg?style=flat-square&label=Coverage)](https://scrutinizer-ci.com/g/mathieutu/laravel-json-syncer/?branch=main) 
[![Code quality](https://img.shields.io/scrutinizer/g/mathieutu/laravel-json-syncer.svg?style=flat-square&label=Quality)](https://scrutinizer-ci.com/g/mathieutu/laravel-json-syncer/?branch=main) 
[![Packagist downloads](https://img.shields.io/packagist/dt/mathieutu/laravel-json-syncer.svg?style=flat-square&label=Downloads)](https://packagist.org/packages/mathieutu/laravel-json-syncer)
[![Stable version](https://img.shields.io/packagist/v/mathieutu/laravel-json-syncer.svg?style=flat-square&label=Packagist)](https://packagist.org/packages/mathieutu/laravel-json-syncer)

## Installation

Require this package in your composer.json and update composer.
```bash
composer require mathieutu/laravel-json-syncer
```

Just add the `JsonExportable` and/or `JsonImportable` interfaces and `JsonExporter` and/or `JsonImporter` traits to your models.

No service providers required!

## Configuration

Out of the box, the Importer and Exporter will automatically guess what attributes and relations to handle, but you can customize everything:
 - JsonExporter: 
    By default, it will export all the attributes in the `$fillable` properties, except those with `*_id` pattern, and all the `HasOneOrMany` relations of your model.
    You can change that by setting `$jsonExportableAttributes` and `$jsonExportableRelations` properties or overwriting `getJsonExportableAttributes()` and `getJsonExportableRelations()` methods.
    
 - JsonImporter: 
    By default, it will import all the attributes which are in the `$fillable` properties and all the `HasOneOrMany` relations of your model.
    You can change that by setting `$jsonImportableAttributes` and `$jsonImportableRelations` properties or overwriting `getJsonImportableAttributes()` and `getJsonImportableRelations()` methods.

## Usage

Just use the `$model->exportToJson($jsonOptions = 0)` to export the object and all its attributes and children.

Use `Model::importFromJson($objectsToCreate)` to import a json string or its array version.

### Examples
_(You can find all this examples in package tests)_

#### How to export
If we consider this dataset in database :
```json
{
    "foos (Foo models)": [{
        "id": 1,
        "author": "Mathieu TUDISCO",
        "username": "@mathieutu",
        "bars (HasMany relation with Bar models)": [
            {
                "id": 1,
                "name": "bar1",
                "foo_id": "1",
                "baz (HasOne relation with Baz model)": {
                    "id": 1,
                    "name": "bar1_baz",
                    "bar_id": "1"
                }
            },
            {
                "id": 2,
                "name": "bar2",
                "foo_id": "1",
                "baz (HasOne relation with Baz model)": {
                    "id": 2,
                    "name": "bar2_baz",
                    "bar_id": "2"
                }
            }
        ]
    }]
}
```
We can export it by:
```php
Foo::first()->exportToJson(JSON_PRETTY_PRINT);
```
It will return:
```json
{
    "author": "Mathieu TUDISCO",
    "username": "@mathieutu",
    "bars": [
        {
            "name": "bar1",
            "baz": {
                "name": "bar1_baz"
            }
        },
        {
            "name": "bar2",
            "baz": {
                "name": "bar2_baz"
            }
        }
    ]
}
```

#### How to import
And exactly the same for the opposite. We can import the json returned by the previous method, or an other one.
For the exact same app If we want to import this new very simple set of data:
```json
{
    "username": "@mathieutu",
    "bars": {
        "name": "my unique simple bar!"
    }
}

```

We can import it with:
```php
Foo::importFromJson($json);
```
And it will create all the entities in database:
```php
dd(Foo::with('bars.baz')->first()->toArray());
/*
array:4 [
  "id" => 1
  "author" => null
  "username" => "@mathieutu"
  "bars" => array:1 [
    0 => array:4 [
      "id" => 1
      "name" => "my unique simple bar!"
      "foo_id" => "1"
      "baz" => null
    ]
  ]
]
*/
```

### License

This JSON Syncer for Laravel is an open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

### Contributing

Issues and PRs are obviously welcomed, as well for new features than documentation.
Each piece of code added should be fully tested, but we can do that all together, so please don't be afraid by that. 

