<?php

namespace Model;

use \Error_\Error_;

/**
 * Base class for Model

 */
class ModelParser implements IModel{
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
	protected static $json_fields;
	// ============

	const CURRENT_TIMESTAMP = -1000000000;

	public $error;
	protected static \mysqli $link;

	public static function get($id) {
		$query = "SELECT * FROM `".static::$table."` WHERE `".static::identifier()."`=?";

		$result = static::getStatementResult($query, 'i', [$id], 'get');
		if($result instanceof Error_) return $result;
		
		$row = $result->fetch_assoc();
		return static::getFrom($row);
	}

	public static function getByField(string $field, array $values) {
		if(!in_array($field, static::$fields, true))
			return new Error_("Field '$field' is not in the list of fields", static::class."@getAllWhereIn");

		$inStatementArr = self::getAllForInStatement($values, static::typeOfField($field));
		$inStatement = $inStatementArr['placeholders'];
		$types = $inStatementArr['types'];
		$values = $inStatementArr['values'];

		$query = "SELECT * FROM ".static::$table. " WHERE  $field = $inStatement;";

		$result = self::getStatementResultSelect($query, $types, $values, "getByField");
		if($result instanceof Error_) return $result;

		$objects = [];
		while($row=$result->fetch_assoc())
			$objects[] = static::getFrom($row);

		return $objects;
	}

	protected static function checkNotEmpty($request, $val) {
		return isset($request[$val]) AND $request[$val] !== '' AND !is_null($request[$val]);
	}

	public static function getFrom($data, $check=false) {
		$obj = new static();

		foreach(static::$fields as $field) {
			if(is_null($data[$field])) continue;
			$value = $data[$field];
			if(in_array($field, static::$json_fields, true)) $value = self::getJson($value);
			$obj->$field = $value;
		}

		return $obj;
	}

	public static function create(array $data) {
		if(!empty(array_diff(static::$required, array_keys($data))))
			return new Error_("Required parameters are not gotten", static::class."@create", 500);

		$types = "";
		$values = [];

		$query = "INSERT INTO `".static::$table."` SET ";
		$place_holders = [];
		foreach(static::$fields as $field) {
			if(!isset($data[$field])) continue;
			$place_holders[] = " `".$field."`=? ";
			$types .= self::typeOfField($field);

			$value = $data[$field];
			if(in_array($field, static::$json_fields, true)) $value = self::setJson($value);

			$values[] = $value;
		}
		$query .= implode(",\n ", $place_holders);

		$result = static::getStatementResultInsert($query, $types, $values, "create");

		return $result;
	}

	/**
	 * $params["getWhereClause"] should be a function that returns an arrar with keys: 'query', 'values', 'types'
	 * $params["filters"] should be an array with keys named as fields of the model
	 *
	 * @param mixed $params=[] Use "getWhereClause" and "filters" keys to get where clause from getWhereClause()
	 * 
	 * @return static[]|Error_
	 * 
	 */
	public static function getAll(array $params=[]) {
		$query = "SELECT self.* FROM `".static::$table."` self ";
		$types = '';
		$values = [];

		if(in_array('filters', array_keys($params), true) AND in_array('getWhereClause', array_keys($params), true)) {
			$getWhereClause = $params['getWhereClause'];
			$filters = $params['filters'];
			$where_clause = $getWhereClause($filters);
			$where = $where_clause['query'];
			$values = $where_clause['values'];
			$types = $where_clause['types'];
		} else {
			$where_clause = self::getWhereClause($params);
			$where = $where_clause['query'];
			$values = $where_clause['values'];
			$types = $where_clause['types'];
		}

		$order_clause = $params['order_clause'];
		$limit = $params['limit'];
		if(isset($params['page'])) $page = intval($params['page']);
		if($limit===false) {
			$limitClause = "";
		} else if(isset($page)) {
			$page = ($page>0)?$page:1;
			$limit = $limit?$limit:100;
			$limitClause = " LIMIT ?, " . $limit;
			$types .= 'i';
			$values = array_merge($values, [($page-1)*$limit]);
		} else {
			$limit = $limit?$limit:100;
			$limitClause = " LIMIT " . $limit;
		}

		$query = $query . $where . ($order_clause?$order_clause:'') . $limitClause;
		$result = static::getStatementResult($query, $types, $values, 'getAll');
		if($result instanceof Error_) return $result;

		$objects = [];
		if(static::$identifier) while($row=$result->fetch_assoc())
			$objects[$row[static::identifier()]] = static::getFrom($row);
		else while($row=$result->fetch_assoc())
			$objects[] = static::getFrom($row);
		
		return $objects;
	}

	/**
	 * Get COUNT(*) with the given filters as $params
	 *
	 * @param array $params
	 * 
	 * @return int|Error_
	 * 
	 */
	public static function count(array $params) {
		$query = "SELECT COUNT(*) as `cnt` FROM `".static::$table."`";
		$types = '';
		$values = [];

		if(in_array('filters', array_keys($params), true) AND in_array('getWhereClause', array_keys($params), true)) {
			$getWhereClause = $params['getWhereClause'];
			$filters = $params['filters'];
			$where_clause = $getWhereClause($filters);
			$where = $where_clause['query'];
			$values = $where_clause['values'];
			$types = $where_clause['types'];
		} else {
			$where_clause = self::getWhereClause($params);
			$where = $where_clause['query'];
			$values = $where_clause['values'];
			$types = $where_clause['types'];
		}

		$query = $query . $where;

		$result = static::getStatementResult($query, $types, $values, 'count');
		if($result instanceof Error_) return $result;

		while($row=$result->fetch_assoc()) {
			$count = intval($row['cnt']);
		}
		
		return $count;
	}

	/**
	 * [Description for getAllIn]
	 *
	 * @param int[] $ids
	 * 
	 * @return static[]|Error_
	 * 
	 */
	public static function getAllIn(array $ids) {
		$query = "SELECT *
			FROM `".static::$table."`
			WHERE `".static::identifier()."`IN(";

		$placeholders = "";
		$placeholders = str_repeat('?, ', count($ids) - 1);
		if(!empty($ids)) $placeholders .= '?';
		$placeholders = $placeholders?$placeholders:"''";
		$query .= $placeholders . ")";

		$types = str_repeat('i', count($ids));
		$values = $ids;

		$result = static::getStatementResult($query, $types, $values, 'getAllIn');
		if($result instanceof Error_) return $result;

		$objects = [];
		if(static::identifier()) while($row=$result->fetch_assoc())
			$objects[$row[static::identifier()]] = static::getFrom($row);
		else while($row=$result->fetch_assoc())
			$objects[] = static::getFrom($row);
		
		return $objects;
	}

	public static function getAllWhereIn(string $field, array $values) {
		if(!in_array($field, static::$fields, true))
			return new Error_("Field '$field' is not in the list of fields", static::class."@getAllWhereIn");

		$inStatementArr = self::getAllForInStatement($values, static::typeOfField($field));
		$inStatement = $inStatementArr['placeholders'];
		$types = $inStatementArr['types'];
		$values = $inStatementArr['values'];

		$query = "SELECT * FROM ".static::$table. " WHERE  $field IN $inStatement;";

		$result = self::getStatementResultSelect($query, $types, $values, "getAllWhereIn");
		if($result instanceof Error_) return $result;

		$objects = [];
		while($row=$result->fetch_assoc())
			$objects[] = static::getFrom($row);
		
		return $objects;
	}

	public static function escapeString(string $string) {
		return mysqli_real_escape_string(self::$link, $string);
	}

	public static function setConnection(\mysqli $connection) {
		self::$link = $connection;
	}

	public static function getConnection() {
		return self::$link;
	}

	public static function table() {
		return static::$table;
	}

	public static function fields() {
		return static::$fields;
	}

	public static function numbers_fields() {
		return static::$numbers_fields;
	}

	/**
	 * Retuns the indefier of the table. If not defined then $prefix+'id' is used
	 *
	 * @return string
	 * 
	 */
	public static function identifier() {
		return static::$identifier?static::$identifier:static::$prefix.'id';
	}

	public function update(array $data) {
		$query = "UPDATE `".static::$table."` SET ";
		$types = '';
		$values = [];


		$updateFields = [];
		foreach ($data as $key => $val) {
			if(in_array($key, static::$fields, true) AND !in_array($key, static::$update_ignore, true))
				$updateFields[$key] = $val;
		}
		unset($updateFields[static::identifier()]);

		$placeHolders = [];
		foreach($updateFields as $key => $val) {
			if($val === self::CURRENT_TIMESTAMP) {
				$placeHolders[] = "$key=CURRENT_TIMESTAMP";
				continue;
			}
			$placeHolders[] = "$key=?";
			$values[] = $val;
			$types .= static::typeOfField($key);
		}
		$whereClause = " WHERE ".self::identifier()."=?; ";
		$values[] = $this->{static::identifier()};
		$types .= static::typeOfField(self::identifier());

		$query .= implode(", ", $placeHolders) . $whereClause;

		$result = self::getStatementResultUpdate($query, $types, $values, "update");

		return $result;
	}

	public static function currentTimestamp() {
		$query = "SELECT CURRENT_TIMESTAMP() AS `timestamp`;";

		if ($sql_query = mysqli_query(self::$link, $query)) {
			if ($result = mysqli_fetch_assoc($sql_query)) {
				return $result['timestamp'];
			}
		}
		return false;
	}

	public static function getAllProtected() {
		return [
			'table' => static::$table,
			'prefix' => static::$prefix,
			'fields_' => static::$fields,
			'update_ignore' => static::$update_ignore,
			'numbers_fields' => static::$numbers_fields,
			'time_field' => static::$time_field,
		];
	}

	public static function exists($id) {
		$query = "SELECT COUNT(*) AS count FROM `".
			mysqli_real_escape_string(self::$link, static::$table)."` WHERE ".
			mysqli_real_escape_string(self::$link, static::$prefix.'id')."=".
			mysqli_real_escape_string(self::$link, $id).";";

		if($sql_query=mysqli_query(self::$link, $query)) {
			if ($row=mysqli_fetch_assoc($sql_query)) {
				return $row['count'] > 0;
			}
		}
		
		return false;
	}

	public static function fourWeeksBeforeToday() {
		$query = "SELECT
			DATE(DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 MONTH)) AS date_from,
			DATE(CURRENT_TIMESTAMP) AS date_till;";
		
		$sql_query = mysqli_query(self::$link, $query);
		if($sql_query) {
			$row = mysqli_fetch_assoc($sql_query);
			if($row) return $row;
			return false;
		}

		return false;
	}
	
	public static function init(\mysqli $link) {
		self::$link = $link;
	}

	/**
	 * returns mysqli link
	 *
	 * @return \mysqli
	 * 
	 */
	public static function getLink() {
		return self::$link;
	}

	public static function typeOfField($field) {
		if(in_array($field, static::$numbers_fields, true)) return 'i';
		elseif(in_array($field, static::$float_fields, true)) return 'd';
		else return 's';
	}

	public static function getWhereClause($filters) {
		$whereClause =" WHERE 1 ";
		$types = '';
		$values = [];

		foreach ($filters as $field=>$value) {
			$negativeFlag = self::negativeFlag($field);
			$nullFlag = self::nullFlag($field);

			if(in_array($field, static::$fields, true)) {
				$flags = [
					"negative"	=> $negativeFlag,
					"null"		=> $nullFlag,
				];
				$whereClause .= self::where_part($flags, $field, $value, $types, $values);
			}
		}

		$data = [
			"query"		=> $whereClause,
			"values"	=> $values,
			"types"		=> $types,
		];
		return $data;
	}

	protected static function negativeFlag(string &$field) {
		$negativeFlag = false;
		$pos = strpos($field, '!');
		if($pos!==false) {
			$negativeFlag = true;
			$field = substr($field, $pos+1);
		}

		return $negativeFlag;
	}

	protected static function nullFlag(string &$field) {
		$nullFlag = false;
		$pos = strpos($field, '?');
		if($pos!==false) {
			$nullFlag = true;
			$field = substr($field, $pos+1);
		}

		return $nullFlag;
	}

	protected static function where_part(array $flags, string $field, $value, string &$types, array &$values) {
		$negativeFlag = false;
		$nullFlag = false;

		$flagKeys = array_keys($flags);
		if(in_array("negative", $flagKeys, true)) {
			$negativeFlag = $flags["negative"];
		}
		if(in_array("null", $flagKeys, true)) {
			$nullFlag = $flags["null"];
		}

		$wherePart = "";
		if($nullFlag) {
			if($negativeFlag) $wherePart .= " AND `$field` IS NOT NULL ";
			else $wherePart .= " AND `$field` IS NULL ";
		}
		else {
			if($negativeFlag) $wherePart .= " AND (`$field`!=? OR `$field` IS NULL) ";
			else $wherePart .= " AND `$field`=? ";

			$types .= static::typeOfField($field);
			$values[] = $value;
		}

		return $wherePart;
	}

	public static function setJson($value) {
		if(is_string($value)) return $value;
		$value = json_encode($value);
		if($value !== false) return $value;
		return null;
	}

	public static function getJson($value) {
		return ($tmp = json_decode($value, true))?$tmp : $value;
	}

	/**
	 * [Description for getStatementResult]
	 *
	 * @param string $query The query to get execution result from
	 * @param string $types The types `i for int` | `s for string` | `d for double` 
	 * @param array $values The values to be binded
	 * @param string $from='getStatementResult' For Error_ class
	 * @param string $type="SELECT" if `INSERT` returns an inserted id;
	 * 		if `UPDATE` returns number of rows affected;
	 * 		otherwise associated array
	 * 
	 * @return \mysqli_result|bool|int|Error_
	 * 
	 */
	public static function getStatementResult(string $query, string $types, array $values, string $from='getStatementResult', string $type="SELECT") {
		$stmt = mysqli_prepare(self::$link, $query);
		if(!$stmt)
			return new Error_('Prepare error: ' . mysqli_error(self::$link), static::class."::".$from, 500);

		$stmt->bind_param($types, ...$values);
		$exec_res = $stmt->execute();
		if($exec_res) {

			if ($type=='INSERT') {
				$inserted_id = mysqli_insert_id(self::$link);
				$rows_affected = $stmt->affected_rows;
				mysqli_stmt_close($stmt);
				return $inserted_id===0?($rows_affected!==0):$inserted_id;
			}

			if ($type=='UPDATE') {
				$rows_affected = mysqli_affected_rows(self::$link);
				mysqli_stmt_close($stmt);
				return $rows_affected;
			}

			$result = $stmt->get_result();
			mysqli_stmt_close($stmt);
			if($result) return $result;
			return new Error_('Result failure: ' . $result, static::class."::".$from, 500);
		}

		return new Error_('Execution failed: ' . $stmt->error, static::class."::".$from, 500);
	}

	/**
	 * Prepare, bind, execute and get result of a `SELECT` statement
	 *
	 * @param string $query The query to get execution result from
	 * @param string $types The types `i for int` | `s for string` | `d for double` 
	 * @param array $values The values to be binded
	 * @param string $from 'getStatementResultSelect' For Error_ class
	 * 
	 * @return Error_|\mysqli_result
	 * 
	 */
	public static function getStatementResultSelect(string $query, string $types, array $values, string $from='getStatementResultSelect') {
		$from = static::class."@".$from;

		$stmt = self::prepareBindExecute($query, $types, $values, $from);
		if($stmt instanceof Error_) return $stmt;

		$result = $stmt->get_result();
		mysqli_stmt_close($stmt);
		if(!$result) return new Error_('Result failure: ' . $result, $from, 500);

		return $result;
	}

	/**
	 * Prepare, bind, execute and get result of an `INSERT` statement
	 *
	 * @param string $query The query to get execution result from
	 * @param string $types The types `i for int` | `s for string` | `d for double` 
	 * @param array $values The values to be binded
	 * @param string $from 'getStatementResultInsert' For Error_ class
	 * 
	 * @return Error_|int|bool If inserted id returnd -> `int`, otherwise `bool`
	 * 
	 */
	public static function getStatementResultInsert(string $query, string $types, array $values, string $from='getStatementResultInsert') {
		$from = static::class."@".$from;

		$stmt = self::prepareBindExecute($query, $types, $values, $from);
		if($stmt instanceof Error_) return $stmt;

		$inserted_id = mysqli_insert_id(self::$link);
		$rows_affected = $stmt->affected_rows;
		if($rows_affected===0) return new Error_('Nothing was inserted '.mysqli_error(self::$link), static::class."::getStatementResultInsert", 500);

		mysqli_stmt_close($stmt);
		return $inserted_id===0?($rows_affected!==0):$inserted_id;
	}

	/**
	 * Prepare, bind, execute and get result of an `UPDATE` statement
	 *
	 * @param string $query The query to get execution result from
	 * @param string $types The types `i for int` | `s for string` | `d for double` 
	 * @param array $values The values to be binded
	 * @param string $from 'getStatementResultUpdate' For Error_ class
	 * 
	 * @return Error_|int number of affected rows returned on success
	 * 
	 */
	public static function getStatementResultUpdate(string $query, string $types, array $values, string $from='getStatementResultUpdate') {
		$from = static::class."@".$from;

		$stmt = self::prepareBindExecute($query, $types, $values, $from);
		if($stmt instanceof Error_) return $stmt;

		$rows_affected = mysqli_affected_rows(self::$link);
		mysqli_stmt_close($stmt);
		return $rows_affected;
	}

	/**
	 * Prepare, bind, execute statement
	 *
	 * @param string $query The query to get execution result from
	 * @param string $types The types `i for int` | `s for string` | `d for double` 
	 * @param array $values The values to be binded
	 * @param string $from 'prepareBindExecute' For Error_ class
	 * 
	 * @return Error_|\mysqli_stmt
	 * 
	 */
	private static function prepareBindExecute(string $query, string $types, array $values, string $from='prepareBindExecute') {
		$stmt = mysqli_prepare(self::$link, $query);
		if(!$stmt)
			return new Error_('Prepare error: ' . mysqli_error(self::$link), $from, 500);

		$stmt->bind_param($types, ...$values);
		$exec_res = $stmt->execute();
		if($exec_res) {
			return $stmt;
		}

		if($stmt->errno == 1062 OR $stmt->errno == 1061)
			return new Error_('Execution failed: ' . $stmt->error, $from, 409);
		return new Error_('Execution failed: ' . $stmt->error, $from, 500);
	}

	public static function getAllForInStatement(array $what, string $type) {
		$placeholders = "''";
		$types = '';
		$values = [];

		if(is_array($what) and !empty($what)) {
			$placeholders = str_repeat("?,", count($what)-1);
			$placeholders .= "?";
			$types = str_repeat($type, count($what));
			$values = $what;
		}

		$placeholders = "(".$placeholders.")";

		$data = [
			"placeholders"	=> $placeholders,
			"types"			=> $types,
			"values"		=> $values,
		];
		return $data;
	}

	public static function setFields($table=false) {
		$query = "DESCRIBE `".mysqli_real_escape_string(self::$link, ($table?$table:static::$table))."`;";
		if ($sql_query = mysqli_query(self::$link, $query)) {
			$fields = [];
			while ($row = mysqli_fetch_assoc($sql_query)) {
				$fields[] = $row['Field'];
			}
			return $fields;
		}

		$error = new Error_('Unable to set fields: '.mysqli_error(self::$link), static::class."::setFields", 500);
		$error->jsonReturn();
	}
}

ModelParser::init(\Env\ParserLink::get());
