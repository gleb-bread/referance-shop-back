<?php

namespace API\cart;

use Error;
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

    protected static function patch() {
        $data = self::getParams();
        $data = self::getParamsWithoutUserToken($data);
        $data = self::getParamsWithoutethod($data);
        $idCart = self::$_SPLIT[2];

        $cartProduct = \Model\Cart::get($idCart);
        if($cartProduct instanceof Error_) self::badRequest();
        
        if($data['cart_count'] != 0){
            $cartProduct->update($data);
            if($cartProduct instanceof Error_) self::internalServerError();
    
            $cartProduct = \Model\Cart::get($cartProduct->cart_id);
            $data = self::getCartItem($cartProduct);
        } else {
            $data['cart_archive'] = true;
            $data['cart_date_update_archive'] = date('Y-m-d H:i:s');
            $cartProduct->update($data);
            if($cartProduct instanceof Error_) self::internalServerError();

            $cartProduct = \Model\Cart::get($cartProduct->cart_id);
            $data = self::getCartItem($cartProduct);
        }
        
        echo json_encode($data);exit;

	}

	protected static function get() {
        switch(self::$_SPLIT[2]){
            case 'count': {
                $userId = self::$user->user_id;
                if($userId instanceof Error_) self::internalServerError();
        
                $data = self::getParams();
                $data = self::getParamsWithoutUserToken($data);
                $data['cart_uid'] = $userId;
                $data['cart_archive'] = false;
        
                $result = \Model\Cart::count($data);
                if($result instanceof Error_) self::badRequest();

                echo json_encode($result); exit;
            }

            default: {
                $userId = self::$user->user_id;
                if($userId instanceof Error_) self::internalServerError();
        
                $data = self::getParams();
                $data = self::getParamsWithoutUserToken($data);
                $data['cart_uid'] = $userId;
                $data['cart_archive'] = false;
        
                $result = \Model\Cart::getAll($data);
                if($result instanceof Error_) self::badRequest();
                
                $paramsList = array();
                foreach($result as $key => $value){
                    array_push($paramsList, [
                        'cart_product_id'   => $value->cart_product_id,
                        'cart_is_parsing'   => $value->cart_is_parsing,
                        'cart_count'        => $value->cart_count,
                        'cart_id'           => $value->cart_id
                    ]);
                }
        
                $products = self::getProductsCart($paramsList);
        
                echo json_encode($products);exit;
            }
        }	
    }

    protected static function post(){
        $userId = self::$user->user_id;
        $data = self::getParams();
        
        $data = self::getParamsWithoutUserToken($data);

        if($data['_method'] === 'patch'){
            self::patch();
        }

        $data['cart_uid'] = $userId;
        $idProduct = $data['cart_product_id'];

        if(!self::checkIsValidPostRequest($data)) self::badRequest();
        if(!self::checkParam($data['cart_uid'])) self::internalServerError();

        $checkArr = array(
            'cart_uid'          => $userId,
            'cart_product_id'   => $idProduct,
        );

        $checkCount = \Model\Cart::count($checkArr);

        if($checkCount === 0){
            $result = \Model\Cart::create($data);
            $cartInfo = \Model\Cart::get($result);
            $idProduct = $cartInfo->cart_product_id;
            $isParser = $cartInfo->cart_is_parsing;
            $countProduct = $cartInfo->cart_count;
            $cartId = $cartInfo->cart_id;

            if($isParser != 0){
                $product = \Model\ParsingProduct::get($idProduct);
            }

            $product = self::getProductWithParams($product, array(
                'count_cart'    => $countProduct,
                'cart_id'       => $cartId,
            ));

            echo json_encode($product);exit;
            
        } else {
            $elemCartList = \Model\Cart::getAll($checkArr);
            $indexFirstProducts = array_key_first($elemCartList);
            $idCartProduct = $elemCartList[$indexFirstProducts]->cart_id;
            $product = \Model\Cart::get($idCartProduct);
            $updateData['cart_count'] = $product->cart_count + $data['cart_count'];
            $product->update($updateData);

            $isParser = $product->cart_is_parsing;
            $countProduct = $product->cart_count;
            $cartId = $product->cart_id;

            if($isParser != 0){
                $product = \Model\ParsingProduct::get($idProduct);
            }

            $product = self::getProductWithParams($product, array(
                'count_cart'    => $countProduct,
                'cart_id'       => $cartId,
            ));

            echo json_encode($product);exit;
        }
    }

    protected static function checkIsValidPostRequest($data){
        return  self::checkParam($data['cart_product_id']) && 
                self::checkParam($data['cart_is_parsing']) && 
                self::checkParam($data['cart_count']);
    }

    protected static function getProductsCart($paramsList){
        $products = array();

        foreach($paramsList as $key => $value){
        
            $idProduct = $value['cart_product_id'];
            $isParser = $value['cart_is_parsing'];
            $countProduct = $value['cart_count'];
            $cartId = $value['cart_id'];

            if($isParser != 0){
                $product = \Model\ParsingProduct::get($idProduct);
            }

            $product = self::getProductWithParams($product, array(
                'cart_id' => $cartId,
                'count_cart' => $countProduct,
            ));

            array_push($products, $product);
        }

        return $products;
    }

    protected static function getProductWithParams($product, $params){
        $currectProduct = array();

        foreach($product as $key => $value){
            $currectProduct[$key] = $value;
        }

        foreach($params as $key => $value){
            $currectProduct[$key] = $value;
        }

        return $currectProduct;
    }

    protected static function getCartItem(\Model\Cart $data){
        $result = array();

        if(self::checkParam($data->cart_uid)) $result['cart_uid'] = $data->cart_uid;
        if(self::checkParam($data->cart_status_id)) $result['cart_status_id'] = $data->cart_status_id;
        if(self::checkParam($data->cart_product_id)) $result['cart_product_id'] = $data->cart_product_id;
        if(self::checkParam($data->cart_order_id)) $result['cart_order_id'] = $data->cart_order_id;
        $result['cart_is_parsing'] = $data->cart_is_parsing === 0 ? false : true;
        if(self::checkParam($data->cart_id)) $result['cart_id'] = $data->cart_id;
        if(self::checkParam($data->cart_date_update_archive)) $result['cart_date_update_archive'] = $data->cart_date_update_archive;
        if(self::checkParam($data->cart_date)) $result['cart_date'] = $data->cart_date;
        if(self::checkParam($data->cart_count)) $result['cart_count'] = $data->cart_count;
        if(self::checkParam($data->cart_comment)) $result['cart_comment'] = $data->cart_comment;
        $result['cart_archive'] = self::checkParam($data->cart_archive) ? true : false;

        return $result;
    }
}