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
		$userId = self::$user->user_id;
        if($userId instanceof Error_) self::internalServerError();

        $data = self::getParams();
        $data = self::getParamsWithoutUserToken($data);
        $data['cart_uid'] = $userId;

        $result = \Model\Cart::getAll($data);
        if($result instanceof Error_) self::badRequest();

        $paramsList = array();
        foreach($result as $key => $value){
            array_push($paramsList, [
                'cart_product_id'   => $value->cart_product_id,
                'cart_is_parsing'   => $value->cart_is_parsing,
                'cart_count'        => $value->cart_count,
            ]);
        }

        $products = array();
        
        foreach($paramsList as $key => $value){

            $idProduct = $value['cart_product_id'];
            $isParser = $value['cart_is_parsing'];
            $countProduct = $value['cart_count'];

            if($isParser != 0){
                $product = \Model\ParsingProduct::get($idProduct);
            }

            $product->count_cart = $countProduct;

            array_push($products, $product);
        }

        echo json_encode($products);exit;

	}

    protected static function post(){
        $userId = self::$user->user_id;
        $data = self::getParams();
        
        $data = self::getParamsWithoutUserToken($data);

        $data['cart_uid'] = $userId;

        if(!self::checkIsValidPostRequest($data)) self::badRequest();
        if(!self::checkParam($data['cart_uid'])) self::internalServerError();

        
        $result = \Model\Cart::create($data);

        $cartInfo = \Model\Cart::get($result);
        $idProduct = $cartInfo->cart_product_id;
        $isParser = $cartInfo->cart_is_parsing;
        $countProduct = $cartInfo->cart_count;

        if($isParser != 0){
            $product = \Model\ParsingProduct::get($idProduct);
        }

        $product['count'] = $countProduct;


        echo json_encode($product);exit;
    }

    protected static function checkIsValidPostRequest($data){
        return  self::checkParam($data['cart_product_id']) && 
                self::checkParam($data['cart_is_parsing']) && 
                self::checkParam($data['cart_count']);
    }
}