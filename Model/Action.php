<?php

namespace Model;

use Error_\Error_;

class Action extends Model {

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

	public $action_id;
	public $action_name;
    public $action_short_name;
	

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
		self::$table = "action";
		self::$prefix = "action_";
		self::$identifier = "action_id";
		self::$numbers_fields = [
			'action_id',
		];
		self::$fields = self::setFields();
	}

}

try {
	Action::__init__([]);
} catch (\Exception $e) {
	new \Error_\Error_("Could not initialize Action. sql error:".mysqli_error(Action::getLink()), "\Model\Action", 500, true);
	exit;
}
