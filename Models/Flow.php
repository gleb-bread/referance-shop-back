<?php

namespace Models;

use \Error_\Error_;

class Flow extends Model {
	// fields
	public $flow_id;
	public $flow_type;
	public $flow_private;
	public $flow_archive;
	public $flow_uid;
	public $flow_hash;
	public $flow_ref;
	public $flow_name;
	public $flow_logo;
	public $flow_styles;
	public $flow_api_key;
	public $flow_api_date;
	public $flow_api_rid;
	public $flow_api_rr_dt;
	public $flow_zadarma_key;
	public $flow_zadarma_secret;
	public $flow_date;
	public $flow_date_edit;

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

	public static function getWhereClause($filters) {
		$where_clause = " WHERE 1 ";
		$values = [];
		$types = '';

		foreach(self::$fields as $field) {
			if(!isset($filters[$field])) continue;

			$where_clause .= " AND self.`$field` = ? ";
			$values[] = $filters[$field];
			$types .= (in_array($field, self::$numbers_fields)?'i':'s');
		}

		return [
			'query' => $where_clause,
			'values' => $values,
			'types' => $types,
		];
	}

	public static function getAllFlowsOfUserOrWhereIn(int $userId, $flowHashes) {
		$query = "SELECT *
			FROM flows
			WHERE flow_uid = ? ";
		
		$types = 'i';
		$values = [$userId];

		$whereIn = self::getAllForInStatement($flowHashes, 's');
		if(isset($whereIn['placeholders'])) {
			$placeholders = $whereIn['placeholders'];
			$query .= " OR flow_hash IN $placeholders ";
			$types .= $whereIn['types'];
			$values = array_merge($values, $whereIn['values']);
		}

		$result = self::getStatementResultSelect($query, $types, $values, "getAllFlowsOfUserOrWhereIn");
		if($result instanceof Error_) return $result;

		$flows = [];
		while($row = $result->fetch_assoc()) {
			$flows[] = self::getFrom($row);
		}

		return $flows;
	}

	public static function __init__() {
		self::$table = 'flows';
		self::$prefix = "flow_";
		self::$update_ignore = ['',];
		self::$float_fields = [

		];
		self::$numbers_fields = [
			'flow_id','flow_archive','flow_uid',
		];
		self::$identifier = 'flow_id';
		self::$required = [];
		self::$fields = self::setFields();
	}
}

Flow::__init__();
