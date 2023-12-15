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
    public $cart_status_id;
    public $cart_comment;
    public $cart_order_id;
    public $cart_archive;
    public $cart_date_update_archive;
	

	public static function create($data) {
		return parent::create($data);
	}

	public static function get($id) {
		return parent::get($id);
	}

	public static function getAll(array $params=[]) {
		return parent::getAll($params);
	}

    public static function getCount(array $params){
        return parent::count($params);
    }

	public static function __init__() {
		self::$table = "cart";
		self::$prefix = "cart_";
		self::$identifier = "cart_id";
		self::$float_fields = [];
		self::$json_fields = [];
		self::$numbers_fields = [
			'cart_id', 'cart_uid', 'cart_product_id', 
            'cart_is_parsing', 'cart_count', 'cart_status_id',
            'cart_order_id', 'cart_archive',
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
