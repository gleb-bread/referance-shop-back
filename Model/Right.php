<?php

namespace Model;

use Error_\Error_;

class Right extends Model {

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

	public $right_id;
	public $right_title;
	

	public static function create($data) {
		return parent::create($data);
	}

	public static function get($id) {
		return parent::get($id);
	}

	public static function __init__() {
		self::$table = "rights";
		self::$prefix = "right_";
		self::$identifier = "right_id";
		self::$numbers_fields = [
			'right_id',
		];
		self::$fields = self::setFields();
	}

}

try {
	Right::__init__([]);
} catch (\Exception $e) {
	new \Error_\Error_("Could not initialize Right. sql error:".mysqli_error(Right::getLink()), "\Model\Right", 500, true);
	exit;
}
