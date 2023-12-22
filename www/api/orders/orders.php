<?php

namespace API\orders;

use Error;
use \Error_\Error_;

class orders extends \API\AController {
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

	}

	protected static function get() {
        $userId = self::$user->user_id;
        if($userId instanceof Error_) self::internalServerError();
        $data = self::getParams();
        $data = self::getParamsWithoutUserToken($data);

        $filter = [
            'order_uid' => $userId,
            'order_archive' => false,
        ];  
        
        $orders = \Model\Order::getAll($filter);
        if($orders instanceof Error_) self::badRequest();

        $orders = self::getCurrectOrdersList($orders);

        echo json_encode($orders);exit;
        
    }

    protected static function post(){
         //TODO оптимизировать код
         $userId = self::$user->user_id;
         if($userId instanceof Error_) self::internalServerError();
         $data = self::getParams();
         $data = self::getParamsWithoutUserToken($data);
         $promo_id = self::checkParam($data['promo_id']) ? $data['promo_id'] : false;
 
         $filter = [
             'cart_uid'      => $userId,
             'cart_archive'  => 0,
         ];
 
         $cartsProducts = \Model\Cart::getAll($filter);
         $totalPrice = 0;
         $totalPriceView = 0;
 
         if(empty($cartsProducts)){
             http_response_code(400);
             $error = ['message' => 'cart_empty'];
             echo json_encode($error);exit;
         }
 
         foreach($cartsProducts as $key => $value) {
             if($value->cart_is_parsing){
                 $product = \Model\ParsingProduct::get($value->cart_product_id);
             }
             if($product instanceof Error_) self::internalServerError();
 
             $totalPrice +=  (int)$value->cart_count * (int)$product->price;
         }
 
         if($promo_id){
            $promo = \Model\Promo::get($promo_id);
            $totalPriceView = $totalPrice - ($totalPrice * ((int)$promo->promo_discount / 100));

            $filterArr = [
                'connexion_user_id' => self::$user->user_id,
                'connexion_promo_id' => $promo->promo_id,
            ];
    
            $newWrite = \Model\PromoUsers::create($filterArr);
            if($newWrite instanceof Error_) self::internalServerError();

         } else {
            $totalPriceView = $totalPrice;
         }
 
         $createData = [
            "order_uid" => $userId,
            "order_price" => $totalPrice,
            "order_price_view" => $totalPriceView,
            "order_discount" => $promo_id ? $promo->promo_discount : 0,
        ];

        $createData = self::getCurrectDataToCreate($createData);
 
        $newOrderId  = \Model\Order::create($createData);
        if($newOrderId instanceof Error_) self::internalServerError();
        $order = \Model\Order::get($newOrderId);
        if($order instanceof Error_) self::internalServerError();

        foreach($cartsProducts as $key => $value) {
            $updateList = [
                'cart_archive' => true,
                'cart_order_id' => (int)$newOrderId,
                'cart_date_update_archive' => date('Y-m-d H:i:s'),
            ];
            
            $value->update($updateList);
        }
 
        echo json_encode($order);exit;
    }

    protected static function getCurrectOrdersList($orders){
        $data = array();

        foreach($orders as $key => $order){
            $currectOrder = self::getCurrectDataToCreate($order);
            $filter = [
                'cart_order_id' => $order->order_id
            ];

            $carts = \Model\Cart::getAll($filter);

            $currectProducts = array();
            
            foreach($carts as $key => $value){
                $item = self::getCurrectDataToCreate(\Model\ParsingProduct::get($value->cart_product_id));
                $item['count_cart'] = $value->cart_count;
                array_push($currectProducts,  $item);
            }

            $currectOrder['products'] = $currectProducts;
            array_push($data, $currectOrder);
        }

        return $data;
    }
}   