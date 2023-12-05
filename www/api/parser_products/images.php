<?php

namespace API\parser_products;

use \Error_\Error_;
use Exception;

class images extends parser_products {

	protected static function get() {
        $listCategory = \Model\ParsingProduct::getUnicFieldValues(['category']);
        if($listCategory instanceof Error_) return self::internalServerError();

        $currectArray = array_keys($listCategory);

        foreach($currectArray as $category){
            $currectProductList = \Model\ParsingProduct::getAll(self::getFilterList($category));
            $currectProductList = array_values($currectProductList);

            if(is_null($currectProductList[0]->images) || empty($currectProductList[0]->images)){
                $result[$category] = self::getImg($currectProductList[0]['id'] + 1)['img1'];
            } else {
                try{
                    $images = json_decode($currectProductList[0]->images);
                    $result[$category] = $images->img1;
                } catch(Exception $e) {
                    $result[$category] = $currectProductList[0]->images;
                }
            }

        }

        
        if(empty($result) || is_null($result)) return self::internalServerError();

        echo json_encode($result);
        exit;
    }

    private function getImg($id){
        $product = \Model\ParsingProduct::get($id);

        if(!is_null($product->images) && !empty($product->images)){
            return $product->images;
        } else {
            self::getImg($product->id + 1);
        }
    }

    private function getFilterList($categoryName){
        $data = [
            'limit'     => 1,
            'category'  => $categoryName,
        ];

        return $data;
    }
}

?>