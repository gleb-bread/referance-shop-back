<?php

namespace API\promo;

use \Error_\Error_;

class promo extends \API\AController {
	protected static $_SPLIT;
	protected static \Model\User $user;
	protected static $method;
	protected static $supportedMethods = ['GET', 'POST'];

	protected static function _main() {
		//if(self::$method == "PATCH") self::patch();
        if(self::$method == "POST") self::post();
        if(self::$method == "GET") self::get();
		
		self::unsuported();
	}

    protected static function get(){
        $promos = \Model\Promo::getAll();
        if($promos instanceof Error_) self::internalServerError();

        echo json_encode(array_values($promos));exit;
    }
	
    protected static function patch(){
        if(!self::checkParam(self::$_SPLIT[2])){
            self::unsuported();
        }

        $data = self::getParams();
        $data = self::getParamsWithoutUserToken($data);	

        $promo = \Model\Promo::get(self::$_SPLIT[2]);
        if($promo instanceof Error_) self::internalServerError();
        $promo->update($data);
        if($promo instanceof Error_) self::badRequest();

        $promo = \Model\Promo::get(self::$_SPLIT[2]);
        echo json_encode($promo);exit;
    }

    protected static function post(){
        switch(self::$_SPLIT[2]){
            case 'code': {
                $data = self::getParams();
                $data = self::getParamsWithoutUserToken($data);	

                $filterPromo = ['promo_code' => $data['promo_code']];
                $countCheck = \Model\Promo::count($filterPromo);
                if(self::checkParam($countCheck)){
                    echo json_encode(['success' => false]);exit;
                }

                $idPromo = \Model\Promo::create($data);

                if($idPromo instanceof Error_) self::badRequest();
                $promo = \Model\Promo::get($idPromo);

                if($promo instanceof Error_) self::internalServerError();
                echo json_encode($promo);exit;
            }

            default: {
                $data = self::getParams();
                $data = self::getParamsWithoutUserToken($data);	
                $idUser = self::$user->user_id;

                if($data['method'] === 'patch'){
                    self::patch();
                }
        
                $promos = \Model\Promo::getAll($data);
        
                if(empty($promos)){
                    echo json_encode(['success' => false]);exit;
                }
        
                $indexFirstPromo = array_key_first($promos);
                $IdPromo = $promos[$indexFirstPromo]->promo_id;
        
                $filterArr = [
                    'connexion_user_id' => $idUser,
                    'connexion_promo_id' => $IdPromo,
                ];
        
                $checkCountPromoActive = \Model\PromoUsers::count($filterArr);
        
                if($checkCountPromoActive != 0){
                    echo json_encode(['success' => false]);exit;
                }

                $newWrite = \Model\PromoUsers::create($filterArr);
                if($newWrite instanceof Error_) self::internalServerError();
        
                echo json_encode(['success' => true]);exit;
            }
        }
       
    }
}