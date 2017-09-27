## DebugHub.com PHP client

The project is under development, not ready for public eyes yet. If you are interested, shoot a email to info@debughub.com

Installation:
This package is for plain PHP. For Laravel installation go to debughubs laravel repo
1. add this to composer.json
`"debughub/client": "0.1.*"`

2. create new config file in config dir with content:
`<?php
return [
    'api_key' => '',
    'project_key' => '',
    'git_root' => '',
    'enabled' => true,
    'blacklist_params' => [
        'password'
    ]  
];`

3. Somewhere in your code init the debughub client:
`$debughub = new \Debughub\PhpClient\Debughub('path/to/config');`
Be sure to import the composer's autoload as well.

To log query use the `$debughub->query()` method.

To add a normal log use `$debughub->log()` method
