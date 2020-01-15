## Overview
A fast and tiny PHP library to export data to csv, excel etc. 
The library is based of PHP generator. It used only 20MB memory
to download 5 Million data. Tested on Laravel 6.

### Installation
```$xslt
composer require sujan/exporter
```

## Usage
`use Sujan\Exporter\Export
`
```$xslt
$columns = [
    'id',
    'name',
    'email'
];

$exporter = new Export(
    User::query(),
    $columns
);

$exporter->export();
```

**For eloquent relation**
```$xslt
$columns = [
    'id',
    'title',
    'user' => [ // user is a relation
        'name'
    ]
];

$exporter = new Export(
    Post::with('user'),
    $columns
);

$exporter->export();
```

Where `user` is the relation name, which is same as is in the `$columns` variable.
