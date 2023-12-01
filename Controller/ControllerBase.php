<?php

namespace Controller
;

class ControllerBase {

	public ?string $ver;
	public ?string $site_url;
	public $_SPLIT;
	public array $params;

	public function __construct() {
		$this->ver = \Env\Env::$ver;
		$this->_SPLIT = $this->getSplit();
		$this->params = self::getParams();
	}

	function view($page, $args=null) {
		$absolute = ($page[0]===".");

		$page = explode('.', $page);

		$dir = getcwd()."/".$this->ver."/".$page[0];
		if(!$absolute) $dir .= "/view/";
		
		$tpl = $dir.(implode("/", array_slice($page, 1))).".php";

		if(file_exists($tpl)) {
			if (!$args) {
				$args = [];
			}
			$grantedArgs = $this->getGrantedArgs();
			$args = $grantedArgs + $args;
			extract($args);
			ob_start();
			include $tpl;
			$content = ob_get_clean();

			return $content;
		} else {
			return new \Error_\Error_("Template does not exist: `$tpl`", ControllerBase::class."::view()", 500);
		}
	}

	public function linkPath($path) {
		return $this->site_url."/".$this->ver.$path;
	}

	public function getGrantedArgs() {
        $user =  \Model\User::handlerEnterUser('');
        echo $user;
        exit();
		//$user = \Model\User::get($_SESSION['user_id']);
		if($user instanceof \Error_\Error_) $user = null;

		$args = [
			'user'			=> $user,
			'hash'			=> \Env\Env::$hash,
			'ver'			=> $this->ver,
			'site_url'		=> $this->site_url,
			'_SPLIT'		=> $this->_SPLIT,
			'params'		=> $this->params,
			'view'			=> fn($page, $args=null) => $this->view($page, $args),
			'linkPath' 		=> fn($path) => $this->linkPath($path),
			'getHeader' 	=> fn(array $args_=[]) => $this->getHeader($args_),
			'getFooter' 	=> fn(array $args_=[]) => $this->getFooter($args_),
		];

		return $args;
	}

	public function getAll($page, array $additionalArgs = []) {
		$args = $this->getGrantedArgs();
		$args['_BAG'] = $additionalArgs;

		$content = $this->view($page, $args);
		if(!is_string($content)) $content = '';

		return $content;
	}

	public function getHeader(array $additionalArgs = []) {
		$content = $this->getAll('.head_v2', $additionalArgs);
		return $content;
	}

	public function getFooter(array $additionalArgs = []) {
		$content = $this->getAll('.footer_v2', $additionalArgs);
		return $content;
	}

	private function getSplit() {
		$REQUEST_URI = $_SERVER['REQUEST_URI'];
		if($REQUEST_URI[0] === '/') $REQUEST_URI = substr($REQUEST_URI, 1);
		$_SPLIT = explode('/', $REQUEST_URI);
		return $_SPLIT;
	}

	protected static function getParams() {
		$request = [];

		$queryString = @file_get_contents('php://input');
		if (!empty($queryString)) {
			$jsonData = json_decode($queryString, true);
			if (is_array($jsonData)) {
				$request = $jsonData;
			}
		}
		if (empty($request) || !is_array($request)) {
			$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
			parse_str($queryString, $request);
		}
		return $request;
	}
}
