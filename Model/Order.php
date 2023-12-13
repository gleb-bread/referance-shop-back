<?php

namespace Model;

use Error_\Error_;

class Order extends Model {

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

	public $order_id;
	public $order_address;
    public $order_uid;
    public $order_status_id;
    public $order_substatus_id;
    public $order_comment;
    public $order_comment_disabled;
    public $order_type_disabled_id;
    public $order_date;
    public $order_price;
    public $order_discount;
    public $order_price_view;
	

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
		self::$table = "orders";
		self::$prefix = "order_";
		self::$identifier = "order_id";
		self::$float_fields = [
            'order_discount', 'order_price_view'
        ];
		self::$json_fields = [];
		self::$numbers_fields = [
			'order_id', 'order_uid', 'order_price',
            'order_status_id', 'order_substatus_id', 'order_type_disabled_id'
		];
		self::$fields = self::setFields();
	}

}

try {
	Order::__init__([]);
} catch (\Exception $e) {
	new \Error_\Error_("Could not initialize Order. sql error:".mysqli_error(Order::getLink()), "\Model\Order", 500, true);
	exit;
}
