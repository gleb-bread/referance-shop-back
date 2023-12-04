<?php
namespace Env;



class ParserLink {
	static \mysqli $link;
	static string $schema = '';

	public static function set(\mysqli $link) {
		if(!isset(self::$link)) self::$link = $link;
		self::setSchema();
	}

	private static function setSchema() {
		$query = "SELECT DATABASE() AS db_name; ";
		if($result = mysqli_query(self::$link, $query)) {
			$row = $result->fetch_assoc();
			self::$schema = $row['db_name'];
		}
	}

	public static function get() {
		if(!isset(self::$link)) {
			require_once 'init.php';
			self::set($link);
		}
		return self::$link;
	}

	public static function getSchema() {
		return self::$schema;
	}

}




?>