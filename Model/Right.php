<?php

namespace Model
;

use \Error_\Error_;

class Right extends Model {
	// fields
	public $right_id;
	public $right_project;
	public $right_uid;
	public $right_account;
	public $right_role;
	public $right_group;
	public $right_author;
	public $right_parent;
	public $right_status;
	public $right_phone;
	public $right_date;
	public $right_date_edit;

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
	protected static $float_fields;
	protected static $identifier;
	// ============

	public static function create($data) {
		return parent::create($data);
	}

	public static function exists($id) {
		return parent::exists($id);
	}

	public static function getAll($params=[]) {
		return parent::getAll($params);
	}

	public static function get($id) {
		return parent::get($id);
	}

	public static function getFrom($data, $check=false) {
		return parent::getFrom($data, $check);
	}

	public function update(array $data) {
		return parent::update($data);
	}

	public static function __init__() {
		self::$table = 'rights';
		self::$prefix = "";
		self::$update_ignore = ['right_id','right_date_edit','right_date','right_project'];
		self::$float_fields = [

		];
		self::$numbers_fields = [
			'right_id', 'right_uid', 'right_author', 'right_parent',
			'right_account',
		];
		self::$identifier = 'right_id';
		self::$required = [];
		self::$fields = self::setFields();
	}
}

Right::__init__();
