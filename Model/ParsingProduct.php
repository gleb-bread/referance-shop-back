<?php
namespace Model;

use \Error_\Error_;


class ParsingProduct extends ModelParser {
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
    public $good_id_from_provider;
    public $articul;
    public $status;
    public $check_invalide_links_views;
    public $category;
    public $subcategory;
    public $provider_category;
    public $provider_subcategory;
    public $link;
    public $price;
    public $edizm;
    public $stock;
    public $country;
    public $producer;
    public $brand;
    public $collection;
    public $provider;
    public $length;
    public $width;
    public $height;
    public $depth;
    public $thickness;
    public $format;
    public $material;
    public $images;
    public $variants;
    public $characteristics;
    public $product_usages;
    public $complectation;
    public $type;
    public $form;
    public $design;
    public $color;
    public $orientation;
    public $surface;
    public $pattern;
    public $montage;
    public $facture;
    public $dilution;
    public $consumption;
    public $usable_area;
    public $method;
    public $count_layers;
    public $blending;
    public $volume;
    public $date_add;
    public $date_edit;
    public $bitrix_views;
    //adding at request fields
    public $count_cart;

    public static function get($id) {
        return parent::get($id);
    }

    public static function getAll(array $params=[]) {
        return parent::getAll($params);
    }

    public static function __init__() {
        self::$table = "all_products";
        self::$prefix = "";
        self::$identifier = "id";
		self::$json_fields = [];
        self::$numbers_fields = [
            'id', 'check_invalide_links_views', 'price',
            'bitrix_views'
        ];
        self::$float_fields = [
            'length', 'width', 'height', 'depth',
            'thickness'
        ];
        self::$fields = self::setFields();
    }

}

try {
    ParsingProduct::__init__([]);
} catch (\Exception $e) {
    new \Error_\Error_("Could not initialize ParsingProduct. sql error:".mysqli_error(ParsingProduct::getLink()), "\Model\ParsingProduct", 500, true);
    exit;
}