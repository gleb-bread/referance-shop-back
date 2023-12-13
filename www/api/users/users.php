<?php

namespace API\users;

use \Error_\Error_;

class users extends \API\AController {
	protected static $_SPLIT;
	protected static \Model\User $user;
	protected static $method;
	protected static $supportedMethods = ['GET', 'PATCH'];

	protected static function _main() {
		if(self::$method == "GET") self::get();
		if(self::$method == "PATCH") self::patch();
		
		self::unsuported();
	}

	protected static function get() {
		echo self::getUser();
	}
	
    private static function getRow(){
        $user = self::$user;

        $data = [
            
        ];

        echo json_encode($data);
		exit;
    }

	private static function getUser() {
		$user = self::$user;

		$data = [
			"user_id"				=> $user->user_id,
			"user_phone"			=> $user->user_phone,
			"user_email"			=> $user->user_email,
			"user_token"			=> $user->user_token,
		];

		echo json_encode($data);
		exit;
	}
}