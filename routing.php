<?php

include __DIR__ . '/vendor/autoload.php';

 
include 'init.php';


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if(Env\Env::$modeDevelopen){
    include 'error.php';
}

$fn = ($_SPLIT[0]) ? (file_exists($ver.'/'.$_SPLIT[0].'/index.php') ? $ver.'/'.$_SPLIT[0].'/index.php' : $ver.'/'.$_SPLIT[0].'.php') : $ver.'/index.php';

if(file_exists($fn)){
	require $fn;
} else {
	header('HTTP/1.0 404 Not Found');
    header('Status: 404 Not Found');
    
    echo 'Not exist file';
}

?>