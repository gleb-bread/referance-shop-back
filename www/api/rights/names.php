<?php

namespace API\rights;

use \Error_\Error_;

class names extends rights {

	protected static function get() {
		if(!self::checkParam(self::$_SPLIT[3])){
		    echo json_encode(\Model\Right::getAll());
            exit;
        } else {
            echo json_encode(\Model\Right::get(self::$_SPLIT[3]));
            exit;
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