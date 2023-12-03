<?php

namespace API\rights;

use \Error_\Error_;

class actions extends rights {

	protected static function get() {
        if(!self::checkParam(self::$_SPLIT[3])){
            $actions = \Model\Action::getAll();
            if($actions instanceof Error_) self::internalServerError();
            $actions = array_values($actions);
		    echo json_encode($actions);
        } else {
            $action = \Model\Action::get(self::$_SPLIT[3]);
            if($action instanceof Error_) self::badRequest();

		    echo json_encode($action);
        }
	}

    protected static function post(){

        $data = parent::getParams();

        if(!parent::checkParam($data['action_title'])) return self::badRequest();

        $data = parent::getParamsWithoutUserToken($data);

        $idNewAction = \Model\Action::create($data);

        self::getAction($idNewAction);
    }

    protected static function patch(){

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