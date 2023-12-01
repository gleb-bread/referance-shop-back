<?php

namespace Error_;

class Error_ {
	public $error;
	public $where;
	public $status;
	private $deadEnd;

	public function __construct($error="Error", $where="", $status=500, $deadEnd=false) {
		$this->error = $error;
		$this->where = $where;
		$this->status = $status;
		$this->deadEnd = $deadEnd;
		return $this;
	}

	public function jsonReturn() {
		http_response_code($this->status);
		echo json_encode(["msg"=>"error: ".$this->error." at ".$this->where]);
		exit;
	}

	public function stringReturn($deadEnd=false) {
		if($this->deadEnd OR $deadEnd) {
			http_response_code($this->status);
			echo "error: ".$this->error." at ".$this->where;
			exit;
		}
		return "error: ".$this->error." at ".$this->where;
	}

	public function htmlRepresenation() {
		$contrroller = new \Controller\ControllerBase();
		$args = [
			'message'	=> $this->error." at ".$this->where,
			'status'	=> $this->status
		];
		$content = $contrroller->view('.errors.error', $args);
		echo $content;
		exit;
	}

	public function __toString() {
		return $this->stringReturn();
	}
}