<?php

namespace Model;

use Error_\Error_;

class Cart extends Model {

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

	public $cart_id;
	public $cart_uid;
    public $cart_product_id;
    public $cart_is_parsing;
    public $cart_count;
    public $cart_date;
	

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
		self::$table = "cart";
		self::$prefix = "cart_";
		self::$identifier = "cart_id";
		self::$float_fields = [];
		self::$json_fields = [];
		self::$numbers_fields = [
			'cart_id', 'cart_uid', 'cart_product_id', 
            'cart_is_parsing', 'cart_count'
		];
		self::$fields = self::setFields();
	}

}

try {
	Cart::__init__([]);
} catch (\Exception $e) {
	new \Error_\Error_("Could not initialize Cart. sql error:".mysqli_error(Cart::getLink()), "\Model\Cart", 500, true);
	exit;
}
