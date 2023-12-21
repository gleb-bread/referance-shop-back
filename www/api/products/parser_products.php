<?php

namespace API\products;
use \Error_\Error_;

class products extends \API\AController {
	protected static $_SPLIT;
	protected static \Model\User $user;
	protected static $method;
	protected static $supportedMethods = ['GET', 'POST'];

	protected static function _main() {
		if(self::$method == "GET") self::get();
		if(self::$method == "POST") self::post();
		
		self::unsuported();
	}
	
	protected static function get() {
		
		$method = self::$method;
		
		if(self::checkParam(self::$_SPLIT[2])){
			switch(self::$_SPLIT[2]) {
				case 'categories': {
					categories::$method();
					exit;
				}

				case 'images': {
					images::$method();
					exit;
				}

				default: {
					$product = \Model\ParsingProduct::get(self::$_SPLIT[2]);
					if($product instanceof Error_) self::badRequest();
					echo json_encode($product);
					exit;
				}
			}
		} else {
			$data = self::getParams();
			$data = self::getParamsWithoutUserToken($data);	
			
			$products = \Model\ParsingProduct::getAll($data);
			if($products instanceof Error_) self::internalServerError();
			$products = array_values($products);
			echo json_encode($products);
			exit;
		}
	}

	protected static function post(){
		$data = self::getParams();
		$data = self::getParamsWithoutUserToken($data);	
		
		$productId = \Model\Products::create($data);
		if($productId instanceof Error_) self::internalServerError();
		$product = \Model\Products::get($productId);
		echo json_encode($product);
		exit;
	}
}