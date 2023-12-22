<?php

namespace Model;

use Error_\Error_;

class Promo extends Model {

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

	public $promo_id;
	public $promo_code;
    public $promo_discount;
    public $promo_date;
    public $promo_archive;
    public $promo_archive_date;
	

	public static function create($data) {
		return parent::create($data);
	}

	public static function get($id) {
		return parent::get($id);
	}

	public static function getAll(array $params=[]) {
		return parent::getAll($params);
	}

	public static function __init__() {
		self::$table = "promo";
		self::$prefix = "promo_";
		self::$identifier = "promo_id";
		self::$float_fields = [];
		self::$json_fields = [];
		self::$numbers_fields = [
			'promo_id', 'promo_discount', 'promo_archive'
		];
		self::$fields = self::setFields();
	}

}

try {
	Promo::__init__([]);
} catch (\Exception $e) {
	new \Error_\Error_("Could not initialize Promo. sql error:".mysqli_error(Promo::getLink()), "\Model\Promo", 500, true);
	exit;
}
