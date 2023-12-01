<?php

namespace Env;

class Env {
	private static $isSet = false;

	public static ?string $ver = 'api';
	public static ?string $hash;

	public static function setAll() {
		if(!self::$isSet) {
			require 'init.php';
			
			self::$hash = self::generateCode();

			self::$isSet = true;
		}
	}

	private static function generateCode($length=6) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
		$code = "";
		$clen = strlen($chars) - 1;
		while (strlen($code) < $length) {
				$code .= $chars[mt_rand(0,$clen)];
		}
		return $code;
	}
}

Env::setAll();
