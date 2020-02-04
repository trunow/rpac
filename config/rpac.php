<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Package Connection
    |--------------------------------------------------------------------------
    |
    | You can set a different database connection for this package. It will set
    | new connection for models Role and Permission. When this option is null,
    | it will connect to the main database, which is set up in database.php
    |
    */

    'connection' => null,

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | If you want, you can replace default models from this package by models
    | you created. Have a look at `Trunow\Rpac\\Role` model and
    | `Trunow\Rpac\\Permission` model.
    |
    */

    'models' => [
        'role' => Trunow\Rpac\Role::class,
        'permission' => Trunow\Rpac\Permission::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Roles, Permissions and Allowed "Pretend"
    |--------------------------------------------------------------------------
    |
    | You can pretend or simulate package behavior no matter what is in your
    | database. It is really useful when you are testing you application.
    | Set up what will methods is(), can() and allowed() return.
    |
    */

    'pretend' => [

        'enabled' => false,

        'options' => [
            'is' => true,
            'can' => true,
            'allowed' => true,
        ],

    ],

];
