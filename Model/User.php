<?php

namespace Model
;

class User extends Model {

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

	public $user_id;
	public $user_name;
	public $user_surname;
	public $user_email;
	public $user_phone;
	public $user_birthday;
	public $user_ban;
	public $user_login;
	public $user_password;
    public $user_token;
    public $user_date;

	/**
	 * Right of the user on current focus command
	 *
	 * @var Right[]
	 */
	private array $userRights;
	private array $allRights = [];
	private array $flows = [];
	

	public static function create($data) {
		return null;
	}

    public static function handlerEnterUser($user_token=''){
       echo parent::get($user_token);
    }

	/**
	 * [Description for get]
	 *
	 * @param mixed $id
	 * 
	 * @return \Error_\Error_|User
	 * 
	 */
	public static function get($id) {
		return parent::get($id);
	}

	public static function getAll($filters=[]) {
		$filters['order_clause'] = '';
		$filters['limit'] = 100;
		return parent::getAll($filters);
	}

	// public static function getFrom($data, $check=false) {
	// 	$user = parent::getFrom($data, $check);
	// 	if($user instanceof self) $user->setFioAbr();
	// 	return $user;
	// }

	public static function getFio($id) {
		$query = "SELECT `user_fio` from `users` WHERE `user_id`=".mysqli_real_escape_string(self::$link, $id).";";

		if($sql_query = mysqli_query(self::$link,$query)) {
			$sql_fetch = mysqli_fetch_assoc($sql_query);
			if ($sql_fetch) {
				return $sql_fetch["user_fio"];
			}
			return false;
		}
	}

	// public static function getFioAbr($fio) {
	// 	$fioParts = explode(' ', $fio);
	
	// 	$surname = mb_convert_case(mb_strtolower($fioParts[0]), MB_CASE_TITLE);

	// 	$initials = '';

	// 	if (isset($fioParts[1])) {
	// 		$name = mb_substr($fioParts[1], 0, 1) . '.';
	// 		$initials .= mb_convert_case(mb_strtolower($name), MB_CASE_TITLE);
	// 	}

	// 	if (isset($fioParts[2])) {
	// 		$patronymic = mb_substr($fioParts[2], 0, 1) . '.';
	// 		$initials .= mb_convert_case(mb_strtolower($patronymic), MB_CASE_TITLE);
	// 	}

	// 	return $surname . ' ' . $initials;
	// }

	// public function setFioAbr() {
	// 	$this->user_fio_abr = self::getFioAbr($this->user_fio);
	// 	return $this;
	// }

	// public static function getUserFioAbr($id) {
	// 	$fio = self::getFio($id);
	// 	return $fio?self::getFioAbr($fio):'#'.$id;
	// }

	// public static function getFioAbrArray($id_array) {
	// 	$stringOfIds = implode(",", $id_array);
		
	// 	$query = "SELECT user_id, user_fio FROM users
	// 		WHERE user_id IN (".
	// 		mysqli_real_escape_string(self::$link, $stringOfIds).");";

	// 	$sql_query = mysqli_query(self::$link, $query);
	// 	if($sql_query) {
	// 		$result = [];
	// 		while($row=mysqli_fetch_assoc($sql_query)) {
	// 			$result[$row['user_id']] = $row['user_fio'];
	// 		}
	// 		$result = array_map('self::getFioAbr', $result);
	// 		return $result;
	// 	}

	// 	return false;
	// }

	// public static function getAllIn(array $userIds) {
	// 	if(empty($userIds)) return [];

	// 	$query = "SELECT 
	// 			user_focus_command,
	// 			user_id,
	// 			user_email,
	// 			user_fio,
	// 			user_phone
	// 		FROM ".self::$table." WHERE user_id IN (";
	// 	$placeholders = str_repeat('?, ', count($userIds) - 1) . '?';
	// 	$query .= $placeholders . ")";

	// 	$types = str_repeat('i', count($userIds));
	// 	$values = $userIds;

	// 	$result = self::getStatementResult($query, $types, $values, "getAllIn", "SELECT");
	// 	if($result instanceof \Error_\Error_) return $result;

	// 	$users = [];
	// 	while($row=$result->fetch_assoc()) {
	// 		$users[$row['user_id']] = self::getFrom($row);
	// 	}

	// 	return $users;
	// }

	// public function belongsToProject() {
	// 	$query = "SELECT
	// 			u.user_id,
	// 			u.user_fio,
	// 			r.right_role,
	// 			r.right_parent,
	// 			r.right_project
	// 		FROM `users` u
	// 		LEFT JOIN `rights` r ON r.right_uid = u.user_id
	// 		WHERE r.right_project = '".
	// 		mysqli_real_escape_string(self::$link, $this->user_focus_command)."'
	// 		AND r.right_role IN ('MHR', 'RHR');";

	// 	if($sql_query=mysqli_query(self::$link, $query)) {
	// 		$result = [];
	// 		while($row=mysqli_fetch_assoc($sql_query)) {
	// 			$result[] = self::getFrom($row);
	// 		}
	// 		return $result;
	// 	}
		
	// 	return false;
	// }

	// private static $RIGHTS = [
	// 	'GIP'			=> 'GD,DP,ROK,GIP',
	// 	'GI'			=> 'GD,DP,ROK,GIP,GI',
	// 	'ROS'			=> 'GD,DP,ROK,ROS',
	// 	'PR'			=> 'GD,DP,ROK,GIP,GI,NU,PR',
	// 	'SM'			=> 'GD,DP,ROK,SM',
	// 	'ASP'			=> 'ASP,RAO',
	// 	'complect'		=> 'GD,DP,ROK,RAO,ASS,ASP,GIP,GI,ROS', // Редактирование заявок и статусов
	// 	'master_list'	=> 'GD,DP,ROK,GIP,MHR,leader_master,teamlead_master,manager_master,RHR,MHR', // Просмотр мастров
	// 	'master_edit'	=> 'GD,DP,ROK,GIP,MHR,leader_master,teamlead_master,manager_master,RHR,MHR', // Редактирование
	// 	'master_coord'	=> 'GD,DP,ROK,GIP,leader_master,teamlead_master,manager_master', // Распределение / HR не может
	// 	'orders_list'	=> 'GD,DP,ROK,GIP,leader_master,teamlead_master,manager_master,teamlead_doors', // Видеть все заявки, даже где не менеджер
	// 	'supply'		=> 'GD,DP,ROK,GIP,RSN,MSN',
	// 	'MCH'			=> 'GD,DP,ROK,GIP,RCH,MCH',
	// 	'info_all'		=> 'GD,DP,ROK,GIP,NU,PR,SM,RAO,ASP,ASS,RSN,MSN,RCH,MCH', // Видеть все заявки, даже где не менеджер
	// 	'orders_view'	=> 'GD,DP,ROK,GIP,GI,NU,RSN,MSN,RCH,MCH',
	// 	'RHR'			=> 'GD,DP,ROK,RHR', // HR руководитель
	// 	'MHR'			=> 'GD,DP,ROK,RHR,MHR', // HR менеджер
	// 	'only_HR'		=> 'RHR,MHR', // HR только
	// 	'sell'			=> 'GD,DP,ROK,ROS,SM,ASS,RDS,RDZ,DZ,LDV,RDV,MDV,LCM,RCM,RSN,MSN,RCH,MCH', // Показывать лиды воронки продажи
	// 	'prod'			=> 'GD,DP,ROK,GIP,GI,NU,PR,ASP,RAO,RPM,MPM,RSN,MSN,RCH,MCH', // Показывать лиды воронки производство
	// 	'create_act'	=> 'GD,DP,ROK,GIP,GI,PR', // Создание акта
	// ];

	// public function getRight(string $right) {
	// 	if(!array_key_exists($right, self::$RIGHTS)) return false;

	// 	$flows = $this->getFlows();
	// 	$isOwner = false;
	// 	foreach($flows as $flow) {
	// 		if($flow->flow_uid === $this->user_id) {
	// 			$isOwner = true;
	// 			break;
	// 		}
	// 	}

	// 	if($isOwner OR $this->user_admin) return  true;

	// 	$rights = $this->getRights();
	// 	return $rights[$right];
	// }

	// public function getFocusCommand() {
	// 	$flows = $this->getFlows();

	// 	foreach($flows as $flow) {
	// 		if($flow->flow_hash === $this->user_focus_command) {
	// 			return $flow;
	// 		}
	// 	}

	// 	return null;
	// }

	// public function myRights() {
	// 	self::setUserRights();

	// 	$roles = array_column($this->userRights, 'right_role');

	// 	return $roles;
	// }

	// private const ROLES = [
	// 	"GD"				=> ['GD','master_edit','GI','sell','complect','PR','MCH','orders_view','MHR','create_act','SM','RHR','orders_list','master_coord','ROS','GIP','info_all','supply','prod','master_list','lead_show'],
	// 	"DP"				=> ['master_edit','GI','sell','complect','PR','MCH','orders_view','MHR','create_act','SM','RHR','orders_list','master_coord','ROS','GIP','info_all','supply','prod','master_list','lead_show'],
	// 	"ROK"				=> ['master_edit','GI','sell','complect','PR','MCH','orders_view','MHR','create_act','SM','RHR','orders_list','master_coord','ROS','GIP','info_all','supply','prod','master_list','lead_show'],
	// 	"GIP"				=> ['master_edit','orders_list','GI','prod','complect','GIP','PR','info_all','MCH','orders_view','supply','master_coord','master_list','create_act','lead_show'],
	// 	"GI"				=> ['create_act','prod','orders_view','GI','complect','PR'],
	// 	"ROS"				=> ['sell','ROS','complect'],
	// 	"NU"				=> ['info_all','prod','orders_view','PR'],
	// 	"PR"				=> ['create_act','info_all','prod','PR'],
	// 	"SM"				=> ['sell','SM','info_all'],
	// 	"ASP"				=> ['ASP','complect','info_all','prod','lead_show'],
	// 	"RAO"				=> ['ASP','complect','info_all','prod','lead_show'],
	// 	"ASS"				=> ['sell','info_all','complect','lead_show'],
	// 	"MHR"				=> ['master_edit','MHR','master_list','only_HR'],
	// 	"leader_master"		=> ['master_edit','master_coord','master_list','orders_list'],
	// 	"teamlead_master"	=> ['master_edit','master_coord','master_list','orders_list'],
	// 	"manager_master"	=> ['master_edit','master_coord','master_list','orders_list'],
	// 	"RHR"				=> ['RHR','master_edit','MHR','master_list','only_HR'],
	// 	"teamlead_doors"	=> ['orders_list'],
	// 	"RSN"				=> ['info_all','orders_view','sell','supply','prod','lead_show'],
	// 	"MSN"				=> ['info_all','orders_view','sell','supply','prod','lead_show'],
	// 	"RCH"				=> ['info_all','MCH','orders_view','sell','prod','lead_show'],
	// 	"MCH"				=> ['info_all','MCH','orders_view','sell','prod','lead_show'],
	// 	"RDS"				=> ['sell','lead_show'],
	// 	"RDZ"				=> ['sell','lead_show'],
	// 	"DZ"				=> ['sell'],
	// 	"LDV"				=> ['sell','lead_show'],
	// 	"RDV"				=> ['sell'],
	// 	"MDV"				=> ['sell'],
	// 	"LCM"				=> ['sell','lead_show'],
	// 	"RCM"				=> ['sell'],
	// 	"RPM"				=> ['prod','lead_show'],
	// 	"MPM"				=> ['prod'],
	// ];

	// public function getRights() {
	// 	$myRoles = $this->myRights();

	// 	if(empty($this->allRights)) {
	// 		foreach ($myRoles as $role) {
	// 			if (array_key_exists($role, self::ROLES)) {
	// 				$rights = self::ROLES[$role];
	// 				foreach ($rights as $right) {
	// 					$this->allRights[$right] = true;
	// 				}
	// 			}
	// 		}
	// 	}

	// 	return $this->allRights;
	// }

	// /**
	//  * get flows of the user
	//  *
	//  * @return Flow[]
	//  * 
	//  */
	// public function getFlows() {
	// 	if(!empty($this->flows)) return $this->flows;

	// 	$rights = Right::getAll(['right_uid'=>$this->user_id, 'right_project'=>$this->user_focus_command]);
		
	// 	$mySet = [];

	// 	foreach($rights as $right) {
	// 		$mySet[$right->right_project] = true;
	// 	}

	// 	$flows_hashes = array_keys($mySet);
	// 	$flows = Flow::getAllFlowsOfUserOrWhereIn($this->user_id, $flows_hashes);
	// 	if($flows instanceof \Error_\Error_) $flows = [];

	// 	$this->flows = $flows;
	// 	return $flows;
	// }

	// /**
	//  * set and return userRights
	//  *
	//  * @return Right[]
	//  * 
	//  */
	// public function setUserRights() {
	// 	if(!isset($this->userRights)) {
	// 		$rights = Right::getAll(['right_uid'=>$this->user_id, 'right_project'=>$this->user_focus_command]);
	// 		if($rights instanceof \Error_\Error_) $this->userRights = [];
	// 		else $this->userRights = $rights;
	// 	}
	// 	return $this->userRights;
	// }

	public static function __init__() {
		self::$table = "users";
		self::$prefix = "user_";
		self::$identifier = "user_id";
		self::$numbers_fields = [
			'user_id','user_ban'
		];
		self::$fields = self::setFields();
	}

}

try {
	User::__init__([]);
} catch (\Exception $e) {
	new \Error_\Error_("Could not initialize User. sql error:".mysqli_error(User::getLink()), "\Model\User", 500, true);
	exit;
}
