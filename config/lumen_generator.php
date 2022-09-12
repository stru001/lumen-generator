<?php

/**
 * Created by PhpStorm.
 * User: stru
 * Date: 2022/09/11
 * Time: 10:13
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    |
    */

    'path' => [

        'migration' => database_path('migrations/'),

        'model' => app_path().DIRECTORY_SEPARATOR.'Models/',

        'datatables' => app_path().DIRECTORY_SEPARATOR.'DataTables/',

        'repository' => app_path().DIRECTORY_SEPARATOR.'Repositories/',

        'routes' => base_path('routes/web.php'),
        'controller' => app_path().DIRECTORY_SEPARATOR.'Http/Controllers/',

        'api_routes' => base_path('routes/api.php'),
        'api_service' => app_path().DIRECTORY_SEPARATOR.'Services/Api/',
        'api_controller' => app_path().DIRECTORY_SEPARATOR.'Http/Controllers/Api/',

        'admin_routes' => base_path('routes/admin.php'),
        'admin_service' => app_path().DIRECTORY_SEPARATOR.'Services/Admin/',
        'admin_controller' => app_path().DIRECTORY_SEPARATOR.'Http/Controllers/Admin/',


    ],

    /*
    |--------------------------------------------------------------------------
    | Namespaces
    |--------------------------------------------------------------------------
    |
    */

    'namespace' => [

        'model' => 'App\Models',
        'datatables' => 'App\DataTables',

        'controller' => 'App\Http\Controllers',

        'api_controller' => 'App\Http\Controllers\Api',

        'admin_controller' => 'App\Http\Controllers\Admin',

    ],

    /*
    |--------------------------------------------------------------------------
    | Templates
    |--------------------------------------------------------------------------
    |
    */

    'templates' => 'adminlte-templates',

    /*
    |--------------------------------------------------------------------------
    | Model extend class
    |--------------------------------------------------------------------------
    |
    */

    'model_extend_class' => 'App\Models\BaseModel',

    /*
    |--------------------------------------------------------------------------
    | API routes prefix & version
    |--------------------------------------------------------------------------
    |
    */

    'api_prefix' => 'api',
    'admin_prefix' => 'admin',

    'api_version' => 'v1',

    /*
    |--------------------------------------------------------------------------
    | Options
    |--------------------------------------------------------------------------
    |
    */

    'options' => [

        'softDelete' => false,

        'save_schema_file' => false,

        'localized' => false,

        'tables_searchable_default' => true,

        'repository_pattern' => true,

        'excluded_fields' => ['id'], // Array of columns that doesn't required while creating module
    ],

    /*
    |--------------------------------------------------------------------------
    | Prefixes
    |--------------------------------------------------------------------------
    |
    */

    'prefixes' => [

        'route' => '',  // using admin will create route('admin.?.index') type routes

        'path' => '',

        'view' => '',  // using backend will create return view('backend.?.index') type the backend views directory

        'public' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Add-Ons
    |--------------------------------------------------------------------------
    |
    */

    'add_on' => [

        'swagger' => false,

        'tests' => true,

        'datatables' => false,

    ],

    /*
    |--------------------------------------------------------------------------
    | Timestamp Fields
    |--------------------------------------------------------------------------
    |
    */

    'timestamps' => [

        'enabled' => true,

        'created_at' => 'created_at',

        'updated_at' => 'updated_at',
    ],

    /*
    |--------------------------------------------------------------------------
    | Save model files to `App/Models` when use `--prefix`. see #208
    |--------------------------------------------------------------------------
    |
    */
    'ignore_model_prefix' => false,

    /*
    |--------------------------------------------------------------------------
    | Specify custom doctrine mappings as per your need
    |--------------------------------------------------------------------------
    |
    */
    'from_table' => [

        'doctrine_mappings' => [],
    ],

];
