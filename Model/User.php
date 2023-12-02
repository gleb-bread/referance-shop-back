<?php

namespace Model
;

use API\users\users;
use Error_\Error_;

class User extends Model {

	// ===STATIC===
	protected static $table;
	protected static $prefix;
	protected static $fields;
	protected static $update_ignore;
	protected static $numbers_fields;
	protected static $time_field;
	protected static $relations;
	protected static $one_to_many;
	protected static $required;
	protected static $identifier;
	// ============

	public $user_id;
	public $user_name;
	public $user_surname;
	public $user_email;
	public $user_phone;
	public $user_birthday;
	public $user_ban;
	public $user_login;
	public $user_password;
    public $user_token;
    public $user_date;
	

	public static function create($data) {
		echo parent::create($data);
	}

    public static function handlerEnterUser($user_token){
       echo parent::get($user_token);
    }

	public static function get($id) {
		return parent::get($id);
	}

	public static function getByField(string $field, array $values){

		$user = parent::getByField($field, $values);
		
		if(!count($user)){
			return false;
		} else {
			return $user[0];
		}

		return $user;
	}

	public static function getAll($filters=[]) {
		$filters['order_clause'] = '';
		$filters['limit'] = 100;
		return parent::getAll($filters);
	}



	private static function getUpdateParams(array $request) {
		$data = [];
		if(parent::checkNotEmpty($request, 'dir_status'))					$data['dir_status'] = $request['dir_status'];
		if(parent::checkNotEmpty($request, 'dir_file'))						$data['dir_file'] = $request['dir_file'];

		return $data;
	}

	public static function __init__() {
		self::$table = "users";
		self::$prefix = "user_";
		self::$identifier = "user_id";
		self::$numbers_fields = [
			'user_id','user_ban'
		];
		self::$fields = self::setFields();
	}

}

try {
	User::__init__([]);
} catch (\Exception $e) {
	new \Error_\Error_("Could not initialize User. sql error:".mysqli_error(User::getLink()), "\Model\User", 500, true);
	exit;
}
