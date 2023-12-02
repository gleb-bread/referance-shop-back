<?php

namespace API;


class AController {
	protected static $_SPLIT;
	protected static \Model\User $user;
	protected static $method;
	protected static $supportedMethods = [];

	public static function main($_SPLIT) {
		static::$_SPLIT = $_SPLIT;
		$data = self::getParams();

		$user = \Model\User::getByField('user_token', [$data['user_token']]);
		if($user instanceof \Error_\Error_) self::unauthorized();
		if(!$user) $user = \Model\User::create($data);
		if($user instanceof \Error_\Error_) self::badRequest($user->stringReturn());

		if(is_int($user)) static::$user = \Model\User::get($user);
		if($user instanceof \Model\User) static::$user = $user;

		if(!static::$user->user_id) self::unauthorized();

		static::$method = $_SERVER["REQUEST_METHOD"];
		if(!in_array(static::$method, static::$supportedMethods)) self::unsuported();

		static::_main();
	}

	protected static function _main() {
		self::unsuported();
	}
	
	protected static function badRequest($message="Bad Request") {
		http_response_code(400);
		echo json_encode(["msg" => $message]);
		exit;
	}

	protected static function unauthorized($message="User is not logged in") {
		http_response_code(401);
		echo json_encode(["msg" => $message]);
		exit;
	}

	protected static function forbiden($message="User has no permission") {
		http_response_code(403);
		echo json_encode(["msg" => $message]);
		exit;
	}

	protected static function notFound($message="Resource not found") {
		http_response_code(404);
		echo json_encode(["msg" => $message]);
		exit;
	}

	protected static function unsuported($message="Method is not supported") {
		http_response_code(405);
		echo json_encode(["msg" => $message]);
		exit;
	}

	protected static function conflict($message="Data for this resource conflicts with existing") {
		http_response_code(409);
		echo json_encode(["msg" => $message]);
		exit;
	}

	protected static function internalServerError($message="Oops! Something went wrong") {
		http_response_code(500);
		echo json_encode(["msg" => $message]);
		exit;
	}

	protected static function get() {
		self::unsuported();
	}

	protected static function post() {
		self::unsuported();
	}

	protected static function patch() {
		self::unsuported();
	}

	protected static function delete() {
		self::unsuported();
	}

	protected static function put() {
		self::unsuported();
	}

	protected static function checkParam($val) {
		$x = empty($val) OR $val === '' OR is_null($val) OR $val === 0;
		return !$x;
	}

	protected static function getParams() {
		$request = [];

		$queryString = @file_get_contents('php://input');
		if (!empty($queryString)) {
			$jsonData = json_decode($queryString, true);
			if (is_array($jsonData)) {
				$request = $jsonData;
			}
		}
		if (empty($request) || !is_array($request)) {
			$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
			parse_str($queryString, $request);
		}
		return $request;
	}
	
}