<?php

namespace API\cart;

use \Error_\Error_;

class cart extends \API\AController {
	protected static $_SPLIT;
	protected static \Model\User $user;
	protected static $method;
	protected static $supportedMethods = ['GET', 'PATCH', 'POST'];

	protected static function _main() {
		if(self::$method == "GET") self::get();
		if(self::$method == "PATCH") self::patch();
        if(self::$method == "POST") self::post();
		
		self::unsuported();
	}

	protected static function get() {
		echo 1; exit;
	}

    protected static function post(){
        $userId = self::$user->user_id;
        $data = self::getParams();
        
        $data = self::getParamsWithoutUserToken($data);

        $data['cart_uid'] = $userId;

        if(!self::checkIsValidPostRequest($data)) self::badRequest();
        if(!self::checkParam($data['cart_uid'])) self::internalServerError();

        
        $result = \Model\Cart::create($data);

        $product = \Model\Cart::get($result);
        echo json_encode($product);exit;
    }

    protected static function checkIsValidPostRequest($data){
        return  self::checkParam($data['cart_product_id']) && 
                self::checkParam($data['cart_is_parsing']) && 
                self::checkParam($data['cart_count']);
    }
}