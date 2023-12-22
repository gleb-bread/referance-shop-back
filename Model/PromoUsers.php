<?php

namespace Model;

use Error_\Error_;

class PromoUsers extends Model {

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

	public $connexion_id;
	public $connexion_user_id;
    public $connexion_promo_id;
	public $connexion_date;
	

	public static function create($data) {
		return parent::create($data);
	}

	public static function get($id) {
		return parent::get($id);
	}

	public static function getAll(array $params=[]) {
		return parent::getAll();
	}

	public static function __init__() {
		self::$table = "promo_users";
		self::$prefix = "connexion_";
		self::$identifier = "connexion_id";
		self::$float_fields = [];
		self::$json_fields = [];
		self::$numbers_fields = [
			'connexion_id', 'connexion_user_id', 'connexion_promo_id'
		];
		self::$fields = self::setFields();
	}

}

try {
	PromoUsers::__init__([]);
} catch (\Exception $e) {
	new \Error_\Error_("Could not initialize PromoUsers. sql error:".mysqli_error(PromoUsers::getLink()), "\Model\PromoUsers", 500, true);
	exit;
}
