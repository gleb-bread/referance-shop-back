<?php

namespace API\parser_products;
use \Error_\Error_;

class parser_products extends \API\AController {
	protected static $_SPLIT;
	protected static \Model\User $user;
	protected static $method;
	protected static $supportedMethods = ['GET'];

	protected static function _main() {
		if(self::$method == "GET") self::get();
		
		self::unsuported();
	}
	//TODO почему то не работает вторая модель
	protected static function get() {
		$data = self::getParams();
		$data = self::getParamsWithoutUserToken($data);
        if(!boolval(self::checkParam(self::$_SPLIT[2]))){
			$products = \Model\ParsingProduct::getAll($data);
			if($products instanceof Error_) self::internalServerError();
			$products = array_values($products);
			echo json_encode($products);
		}
		if(boolval(self::checkParam(self::$_SPLIT[2]))) {
			$product = \Model\ParsingProduct::get(self::$_SPLIT[2]);
            if($product instanceof Error_) self::badRequest();
            echo json_encode($product);
		}
	}
}