<?php

namespace API\rights;

use \Error_\Error_;

class rights_action extends \API\AController {
	protected static $_SPLIT;
	protected static \Model\User $user;
    protected static \Model\Right $right;
    protected static \Model\RightsActions $rightAction;
    protected static \Model\Action $action;
	protected static $method;
	protected static $supportedMethods = ['GET', 'PATCH', 'POST'];

	protected static function _main() {
		if(self::$method == "GET") self::get();
		if(self::$method == "PATCH") self::patch();
        if(self::$method == "POST") self::post();
		
		self::unsuported();
	}

	protected static function get() {
		echo self::getRights();
	}

    protected static function post(){

    }

    protected static function patch(){

    }

    private static function getRights(){
        $rights = self::$right::getAll();
        $actions = self::$action::getAll();
        $rightAction = [];
    }
}