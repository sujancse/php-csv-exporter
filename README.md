![Packagist Version](https://img.shields.io/packagist/v/sujan/php-csv-exporter)
![Packagist](https://img.shields.io/packagist/dt/sujan/php-csv-exporter?color=green)
![GitHub](https://img.shields.io/github/license/sujancse/php-csv-exporter?color=yellow)

## Overview
A fast and tiny PHP library to export data to CSV. The library is based on a PHP generator.

## Why Use
It took me 5 seconds to export 5M data so you can call it fast enough. And because of the use of 
[Generator](https://www.php.net/manual/en/language.generators.overview.php) it uses less memory 
and never get caught by memory exception.

### Installation
```$xslt
composer require sujan/php-csv-exporter
```

## Basic Usage
```$xslt
$columns = [ 'id', 'name', 'email' ];

$queryBuilder = User::limit(10); // Query Builder

$exporter = new Exporter();
$exporter->build($queryBuilder, $columns, 'users.csv')
         ->export();
```

Build and export, that much simple.

## Documentation

 - [Build CSV](#build-csv)
 - [Export CSV](#export-csv)
 - [Usage Examples](#usage-examples)
    - [Laravel](#laravel) 
        - [From Eloquent Query Builder (RECOMMENDED)](#from-eloquent-query-builder-recommended) 
        - [From Collection](#from-collection) 
        - [From Array](#from-array) 
        - [Eloquent Relation](#eloquent-relation) 
    - [RAW PHP](#raw-php) 
        - [From Array](#from-plain-array) 
        - [From PDOStatement (RECOMMENDED)](#from-pdostatement-recommended)

### Build CSV
CSV build takes three parameters. First one is the model which could be `Array`, `PDOStatement`, `Eloquent Query Builder` and 
`Collection`, seconds one takes the field names you want to export, third one is CSV filename.

```$xslt
$exporter->build($queryBuilder, $columns, 'users.csv');
```

### Export CSV
```$xslt
$exporter->export();
```

## Usage Examples
### Laravel 
You can export data from `Eloquent Query Builder`, `Collection` and `Array` whereas `Eloquent Query Builder` is highly recommended.
#### From Eloquent Query Builder (RECOMMENDED)
```$xslt
$columns = [ 'id', 'name', 'email' ];

$queryBuilder = User::latest()->whereNotNull('email_verified_at'); // Query Builder

$exporter = new Exporter();
$exporter->build($queryBuilder, $columns, 'users.csv')
         ->export();
```

#### From Collection
```$xslt
$columns = [ 'id', 'name', 'email' ];

$collection = User::latest()->get(); // Collection

$exporter = new Exporter();
$exporter->build($collection, $columns, 'users.csv')
         ->export();
```

#### From Array
```$xslt
$columns = [ 'id', 'name', 'email' ];

$usersArray = User::latest()->get()->toArray(); // Array of Users

$exporter = new Exporter();
$exporter->build($usersArray, $columns, 'users.csv')
         ->export();
```

#### Eloquent Relation
```$xslt
$columns = [
    'id',
    'title',
    'user' => [ // user is a relation
        'name'
    ]
];

$queryBuilder = Post::with('user'); // Query builder

$exporter = new Exporter();
$exporter->build($queryBuilder, $columns, 'users.csv')
         ->export();
```

### Raw PHP
The library supports Laravel as well as raw PHP. You can easily export data from `PDOStatement` and `Array`.

#### From Plain Array
```$xslt
$array = [
    ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
    ['id' => 2, 'name' => 'Jane Doe', 'email' => 'jane@example.com']
];

$columns = ['id', 'name', 'email'];

$exporter = new Exporter();
$exporter->build($array, $columns, 'users.csv')
         ->export();
```

#### From PDOStatement (RECOMMENDED)
```$xslt
    $servername = "localhost";
    $username = "root";
    $password = "password";
    $dbname = "laravel";

    $columns = [
        'id',
        'name',
        'email'
    ];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT id, name, email FROM users");
        $stmt->execute();

        // set the resulting array to associative
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $exporter = new Exporter();
        $exporter->build($stmt, $columns, 'users.csv)
                 ->export();
    }
    catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    $conn = null;
```

## You are always welcome to contribute
