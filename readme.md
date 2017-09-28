## DebugHub.com PHP client

Debughub helps your development process by logging all requests to your application and showing them in debughub.com application.
It helps you find issues in your code faster and find performance issues while still in developemnt.  

### Installation:
This package is for plain PHP. For Laravel installation go to debughubs laravel repo
1. add this to composer.json
`"debughub/php-client": "0.1.*"`

2. create new config file in config dir with content:
```
<?php
return [
    'api_key' => '', // Your API key
    'project_key' => '', // Your project key
    'git_root' => '', // (Optional) Path to your git root folder. Used to track git branches
    'enabled' => true, // If debughub.com client is enabled.
    'send_query_data' => true, // If you want to send the data insterted into DB queries to debughub.com.
    'blacklist_params' => [ // post and get params, that should never be sent to debughub.com
        'password'
    ]  
];
```

3. Somewhere in your code init the debughub client:
`$debughub = new \Debughub\PhpClient\Debughub('path/to/config');`
Be sure to import the composer's autoload as well.
From now on debughub client will send request and response info to debughub.com


### Available extra methods
You can use these methods to add extra information about your application. You can log queries and logs using debughub. All your request and response information will be sent automatically.

#### DB Queries
To log query use the `$debughub->query(string $query, array $data, string $duration, string $connection)` method.
For example if you use a class for all your Database querying, you can simply add this method to the method, that calls the query. Or you can just add it after the query you want to log.

`$query` - query to execute. You can add a `?` instead of any query params, you want to replace in the query. For example `SELECT * FROM Users where id = ?;` and send the user's ID in data variable.

`$data` -  array of data, that should replace the `?` in your code. This will not be send if you disable `send_query_data` in the config.

`$duration` - seconds it took to execute the query.

`$connection` - name of the DB connection. Usefull if your application uses multiple connections

Example:
```
$db = DB::select("SELECT * FROM USERS"); // some imagined DB class
debughub->query($db->getQuery(), $db->getData(), $db->getDuration(), 'some connection'); // note - the DB class and its methods are just as a example. the DB class is not part of debughub
```
Or you can have debughub calculate the duration as well like this:
```
$query = "SELECT * FROM USERS";
$debughubQueryIndex = $debughub->startQuery($query, [], 0, 'some connection'); // note - the DB class and its methods are just as a example. the DB class is not part of debughub
$db = DB::select(query); // some imagined DB class
$debughub->endQuery($debughubQueryIndex); // you dont have to get or pass the query index, if it is not passed, the debughub will assume it was the last query you want to update with endQuery() method.
```

#### Logging
To add a simple log use `$debughub->log(string $data, string $type)` method
Logs work just like queries. You can use log() method to log just one action for example.

`$data` - data to log. It can be any data type, arrays and objects will be serialized.

`$type` ('info' by default) - Name of the log. This will change the way log shows up in the debughub.com application. It should be `info`, `error` or `warning`.
```
$debughub->log('something just happened', 'info');
```
By default the log does not have a duration and in debughub's timeline will show up just as a small line. If you want to measure duration of something, for example API call to external API, you can use:
```
$debughubLogIndex = $debughub->startLog('something just happened', 'info');
//do some API call or something
$debughub->endLog(debughubLogIndex); // you dont have to get or pass the log index, if it is not passed, the debughub will assume it was the last log you want to update with endLog() method.
```

#### Routes
If you want to add route to your log, you can do it using `$debughub->route(string $route)` method.
For example:
```
$debughub->route('users/view/{id}');
```
