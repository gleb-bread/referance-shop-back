<?php
namespace API;

try {
	$path = $_SPLIT[1];
	eval("API\\$path\\$path::_main(\$_SPLIT);");
	exit;
} catch (\Error $e) {
	http_response_code(404);
	exit;
}


?>