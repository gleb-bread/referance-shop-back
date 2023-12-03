<?php

namespace API\rights;

use \Error_\Error_;

class name extends rights {
    
	protected static function get() {
		if(!self::checkParam(self::$_SPLIT[2])){
		    echo self::$action::getAll();
        } else {
            echo self::$action::get(self::$_SPLIT[2]);
        }
	}

    protected static function post(){

        $data = parent::getParams();

        if(!parent::checkParam($data['right_title'])) return self::badRequest();

        $data = parent::getParamsWithoutUserToken($data);

        $idNewRightName = \Model\Right::create($data);

        self::getRightName($idNewRightName);
    }

    private function getRightName($id){
        $right = \Model\Right::get($id);

        $data = [
            'right_id'      => $right->right_id,
            'right_title'   => $right->right_title,
        ];

        $data = json_encode($data);
        echo $data;
        exit;
    }



    protected static function patch(){

    }
}