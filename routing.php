<?php

include __DIR__ . '/vendor/autoload.php';

 
include 'init.php';

if(Env\Env::$modeDevelopen){
    include 'error.php';
}

$fn = ($_SPLIT[0]) ? (file_exists(Env\Env::$ver . '/'.$_SPLIT[0].'/index.php') ? Env\Env::$ver . '/'.$_SPLIT[0].'/index.php' : Env\Env::$ver . '/'.$_SPLIT[0].'.php') : Env\Env::$ver . '/index.php';

if(file_exists($fn)){
	require $fn;
} else {
	header('HTTP/1.0 404 Not Found');
    header('Status: 404 Not Found');
    
    echo 'Not exist file';
}

?>