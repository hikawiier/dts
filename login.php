<?php

define('CURSCRIPT', 'login');
define('LOAD_CORE_ONLY', TRUE);

require './include/common.inc.php';



if($mode == 'quit') {

	gsetcookie('user','');
	gsetcookie('pass','');
	header("Location: index.php");
	exit();

}
include './gamedata/banlist.list';

$name_check = name_check($username);
$pass_check = pass_check($password,$password);
if($name_check!='name_ok'){
	gexit($_ERROR[$name_check],__file__,__line__);
}elseif($pass_check!='pass_ok'){
	gexit($_ERROR[$pass_check],__file__,__line__);
}

$onlineip = real_ip();

if(preg_match($iplimit,$onlineip)){
	gexit($_ERROR['ip_banned'],__file__,__line__);
}

$password = create_cookiepass($password);
$groupid = 1;
$credits = 0;
$gender = 0;

$userdata = fetch_udata_by_username($username);
if(empty($userdata)) {
	gexit($_ERROR['user_not_exists'],__file__,__line__);
} else {
	if($userdata['groupid'] <= 0){
		gexit($_ERROR['user_ban'],__file__,__line__);
	} elseif(!pass_compare($userdata['username'], $password, $userdata['password'])) {
		gexit($_ERROR['wrong_pw'],__file__,__line__);
	}
}
//重设IP
update_udata_by_username(array('ip' => $onlineip), $username);

gsetcookie('user',$userdata['username'],86400*15);
gsetcookie('pass',$password,86400*15);
//重新登陆之后房间设为0
set_current_roomid(0);

Header("Location: index.php");

exit();

/* End of file login.php */
/* Location: /login.php */