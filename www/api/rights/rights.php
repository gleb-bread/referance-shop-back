<?php

namespace API\rights;

use \Error_\Error_;

class rights extends \API\AController {
	protected static $_SPLIT;
	protected static \Model\User $user;
    protected static \Model\Right $right;
    protected static \Model\RightsActions $rightAction;
    protected static \Model\Action $action;
	protected static $method;
	protected static $supportedMethods = ['GET', 'PATCH', 'POST'];

	protected static function _main() {
		$method = self::$method;

        switch(self::$_SPLIT[2]) {
			case "names":
				names::$method();
				exit;
			case "actions":
				actions::$method();
				exit;
			default:
				self::totalActions();
		}
	}

	protected static function get() {
		echo self::getRights();
	}

    protected static function post(){

    }

    protected static function patch(){

    }

    private function totalActions(){
        if(self::$method == "GET") self::get();
		if(self::$method == "PATCH") self::patch();
        if(self::$method == "POST") self::post();

        self::unsuported();
    }

    private static function getRights(){
        $rights = self::$right::getAll();
        $actions = self::$action::getAll();
        $rightAction = self::$rightAction::getAll();
        
        $rightAction = [];


    }
}