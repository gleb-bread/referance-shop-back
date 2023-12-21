<?php
namespace Model;

use \Error_\Error_;


class Products extends Model {
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

    public $id;
    public $title;
    public $characteristics;
    public $price;
    public $subcategory;
    public $date;
    public $archive;
    public $archive_date;

    public static function get($id) {
        return parent::get($id);
    }

    public static function getAll(array $params=[]) {
        return parent::getAll($params);
    }

    public static function create($data){
        return parent::create($data);
    }

    public static function __init__() {
        self::$table = "all_products";
        self::$prefix = "";
        self::$identifier = "id";
		self::$json_fields = [];
        self::$numbers_fields = [
            'id', 'price', 'archive',
        ];
        self::$float_fields = [
        ];
        self::$fields = self::setFields();
    }

}

try {
    Products::__init__([]);
} catch (\Exception $e) {
    new \Error_\Error_("Could not initialize Products. sql error:".mysqli_error(Products::getLink()), "\Model\Products", 500, true);
    exit;
}