<?php

declare(strict_types=1);

$dbConnection = (string)(getenv('DB_CONNECTION') ?: 'sqlite');
$projectRoot = __DIR__;

if ($dbConnection === 'pgsql') {
    $adapterConfig = [
        'adapter'  => 'pgsql',
        'host'     => (string)(getenv('DB_HOST') ?: '127.0.0.1'),
        'name'     => (string)(getenv('DB_DATABASE') ?: 'app'),
        'user'     => (string)(getenv('DB_USERNAME') ?: 'postgres'),
        'pass'     => (string)(getenv('DB_PASSWORD') ?: ''),
        'port'     => (int)(getenv('DB_PORT') ?: 5432),
        'charset'  => 'utf8',
    ];
} else {
    $sqlitePath = (string)(getenv('DB_DATABASE') ?: $projectRoot . '/database/database.sqlite3');
    if (str_ends_with($sqlitePath, '.sqlite3')) {
        $sqlitePath = substr($sqlitePath, 0, -strlen('.sqlite3'));
    } elseif (str_ends_with($sqlitePath, '.sqlite')) {
        $sqlitePath = substr($sqlitePath, 0, -strlen('.sqlite'));
    }

    $adapterConfig = [
        'adapter' => 'sqlite',
        'name'    => $sqlitePath,
    ];
}

return [
    'paths' => [
        'migrations' => $projectRoot . '/database/migrations',
        'seeds'      => $projectRoot . '/database/seeders',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment'     => 'default',
        'default'                 => $adapterConfig,
        'testing' => [
            'adapter' => 'sqlite',
            'name'    => ':memory:',
        ],
    ],
    'version_order' => 'creation',
];
