1. Clone https://github.com/humhub/humhub
2. Clone tracker module files to `protected/modules/tracker/`
3. Create `tracker\tests\config-local.php` and configure `db` and `urlManager` components.
For example:
```php
<?php
return [
    'components' => [
        'urlManager' => [            
            'baseUrl' => 'http://localhost/humhub',
            'hostInfo' => 'http://localhost/humhub',
        ],
        'db' => [
            'dsn' => 'mysql:host=localhost;dbname=humhub_test',
            'username' => 'root',
            'password' => '123',
            'charset' => 'utf8',
        ],

    ],
];
```
4. cd to `protected/modules/tracker/`
5. run `php yii_test migrate/up --includeModuleMigrations=1 --interactive=0`
6. run `php yii_test installer/auto`
7. run `php yii_test migrate/up -p=migrations --interactive=0`
8. run `codecept run`
