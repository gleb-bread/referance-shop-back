<?php

namespace API\rights;

use \Error_\Error_;

class actions extends rights {

	protected static function get() {
        if(!self::checkParam(self::$_SPLIT[2])){
		    echo self::$action::getAll();
        } else {
            echo self::$action::get(self::$_SPLIT[2]);
        }
	}

    protected static function post(){

    }

    protected static function patch(){

    }
}