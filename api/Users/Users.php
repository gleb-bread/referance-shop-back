<?php

namespace API\Users;

use \Error_\Error_;

class Users extends \API\AController {
	protected static $_SPLIT;
	protected static \Model\User $user;
	protected static $method;
	protected static $supportedMethods = ['GET', 'PATCH'];

	protected static function _main() {
		self::{self::$method}();
	}

	protected static function get() {
		echo self::$user->handlerEnterUser();

		switch(self::$_SPLIT[2]) {
			case NULL: 
				self::getUser();
			default:
				self::unsuported();
		}
	}

    private function getRow(){
        $user = self::$user;

        $data = [
            
        ];

        echo json_encode($data);
		exit;
    }

	private function getUser() {
		$user = self::$user;

		$data = [
			"user_id"				=> $user->user_id,
			"user_phone"			=> $user->user_phone,
			"user_email"			=> $user->user_email,
		];

		echo json_encode($data);
		exit;
	}
}