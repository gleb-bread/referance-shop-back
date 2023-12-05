<?php

namespace API\parser_products;

use \Error_\Error_;

class categories extends parser_products {

	protected static function get() {
        $categories = \Model\ParsingProduct::getUnicFieldValues(['category','subcategory']);
        if($categories instanceof Error_) self::internalServerError();
        echo json_encode($categories);
        exit;
	}

    private function getAction($id){
        $action = \Model\Action::get($id);

        $data = [
            'action_id'      => $action->action_id,
            'action_title'   => $action->action_title,
            'action_short_title' => $action->action_short_title,
        ];

        $data = json_encode($data);
        echo $data;
        exit;
    }

}

?>