<?php

require __DIR__ . '/PcdModel/Pcd.php';

/*require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

// This assumes that you have placed the Firebase credentials in the same directory
// as this PHP file.

$serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/angular-firebase-92892-8c2cfdfabc3e.json');

$firebase = (new Factory)
    ->withServiceAccount($serviceAccount)
    // The following line is optional if the project id in your credentials file
    // is identical to the subdomain of your Firebase project. If you need it,
    // make sure to replace the URL with the URL of your project.
    //->withDatabaseUri('https://angular-firebase-92892.firebaseio.com/')
    ->create();

$database = $firebase->getDatabase();

var_dump($database);

die("Terminou\n");*/

/*$newPost = $database
    ->getReference('blog/posts')
    ->push([
        'title' => 'Post title',
        'body' => 'This should probably be longer.'
    ]);

$newPost->getKey(); // => -KVr5eu8gcTv7_AHb-3-
$newPost->getUri(); // => https://my-project.firebaseio.com/blog/posts/-KVr5eu8gcTv7_AHb-3-

$newPost->getChild('title')->set('Changed post title');
$newPost->getValue(); // Fetches the data from the realtime database
$newPost->remove();*/

$pcd = new Pcd();
/*$result = $pcd->insert([
    12 => [
        'nome' => 'Fortaleza (Funceme)',
        'dados' => [
            1 => 26.3,
            2 => 27.2,
            3 => 28.4
        ]
    ]
]);*/
$result = $pcd->update([
    12 => [
        'nome' => 'Fortaleza (Funceme)',
        'dados' => [
            '1h' => 26.3,
            '2h' => 27.2,
            '3h' => 28.4
        ]  
    ]
]);
//$result = $pcd->getAll();
var_dump($result);


