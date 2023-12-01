<?php

namespace Env;

class Env {
	private static $isSet = false;

	public static ?string $site_login;
	public static ?string $site_domain;
	public static ?string $site_name;
	public static ?string $site_url;
	public static ?string $site_desc;
	public static ?string $site_tlgrm_bot;
	public static ?string $title;
	public static ?string $description;
	public static ?string $keywords;
	public static ?string $smeta_dir;
	public static ?array $word;
	public static ?string $metrika;
	public static ?string $ver = 'v0020';
	public static ?string $hash;

	public static function setAll() {
		if(!self::$isSet) {
			require 'init.php';

			self::$site_login = $site_login;
			self::$site_domain = $site_domain;
			self::$site_name = $site_name;
			self::$site_url = $site_url;
			self::$site_desc = $site_desc;
			self::$site_tlgrm_bot = $site_tlgrm_bot;
			self::$title = $title;
			self::$description = $description;
			self::$keywords = $keywords;
			self::$smeta_dir = $smeta_dir;
			self::$word = $word;
			self::$metrika = $metrika;
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
