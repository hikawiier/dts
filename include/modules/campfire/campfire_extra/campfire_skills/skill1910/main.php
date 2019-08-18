<?php

namespace skill1910
{
	function init() 
	{
		define('MOD_SKILL1910_INFO','card;unique;locked;');
		eval(import_module('clubbase'));
		$clubskillname[1910] = '贻梦';
	}
	
	function acquire1910(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys'));
		if(checkitm1910($pa)) \skillbase\skill_setvalue(1910,'flag','1',$pa);
	}
	
	function lost1910(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function check_unlocked1910(&$pa=NULL)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}
	
	function cleanitm1910(){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$file = GAME_ROOT.'./gamedata/cache/skill1910.itm.dat';
		file_put_contents($file,'');
		chmod($file, 0777);
	}
	
	function readitm1910(){
	//解包所有储存道具的信息
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$file = GAME_ROOT.'./gamedata/cache/skill1910.itm.dat';
		if(file_exists($file)) 
			$td_list = json_decode(file_get_contents($file),true);
		return $td_list;
	}
	
	function writeitm1910($td){
	//打包并写入所有储存道具的信息
		if (eval(__MAGIC__)) return $___RET_VALUE;
		cleanitm1910();
		$file = GAME_ROOT.'./gamedata/cache/skill1910.itm.dat';
		$td = json_encode($td,JSON_UNESCAPED_UNICODE);
		writeover($file, $td);
	}
	
	function checkitm1910(&$pa){
	//检查技能持有者有没有储存道具
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$td_flag = false;
		$td_list = readitm1910();
		foreach($td_list as $tdn => $tdnm){
			if($pa['name'] == $tdnm['name']) {
				$td_flag = true;
				break;
			}
		}
		return $td_flag;
	}
	
	function loaditm1910(){
	//取出道具
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('player','logger','itemmain'));
		$td = readitm1910();
		foreach($td as $tn => $tdnm){
			if($name == $tdnm['name']) {
				$log.="你闭上双眼，在支离破碎的精神世界中徐徐前行……<br>
				在无法分清天空与地平的灰色世界里，一抹兀自出现的黯淡光点在此刻却比任何绚烂的色彩都要明亮。<br>
				不知为何而出现的悸动催促着你走上前去，紧握住那快要消散的光点，<br>
				而在下一个瞬间，属于已逝之人的记忆再次浮现——<br>
				当你再次睁开眼时，你发现手中多了些东西。<br>";
				$tt = $tdnm;
				$itm0= $tt['itm'];
				$itmk0= $tt['itmk'];
				$itme0= $tt['itme'];
				$itms0= $tt['itms'];
				$itmsk0= $tt['itmsk'];
				\itemmain\itemget();
				$td = array_diff_key($td,[$tn => $tdnm]);
				writeitm1910($td);
				\skillbase\skill_setvalue(1910,'flag','0');
				break;
			}
		}
	}
	
	function saveitm1910($tditm){
	//存入道具
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','logger','itemmain'));
		$log.="你闭上双眼，在支离破碎的精神世界中徐徐前行……<br>
		在无法分清天空与地平的灰色世界里，一抹兀自出现的黯淡光点在此刻却比任何绚烂的色彩都要明亮。<br>
		心中的悸动催动你走上前去，握住了这份超越了时间的遗赠。<br>
		而在下一个瞬间，属于已逝之人的记忆再次浮现——<br>
		当你再次睁开眼时，你手中的{$tditm['itm']}已经消失不见。<br>";
		\skillbase\skill_setvalue(1910,'flag','1');
		$td = readitm1910();
		$td[] = $tditm;
		writeitm1910($td);
	}
	
	function skill1910_traveldream_page()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','input','logger','itemmain'));
		include template(MOD_SKILL1910_TRAVELDREAM_PAGE);
		$cmd=ob_get_contents();
		ob_clean();
	}
	
	function act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;		
		eval(import_module('sys','player','input','logger'));
	
		if ($mode == 'special' && $command == 'skill1910_special') 
		{
			if (!\skillbase\skill_query(1910)) 
			{
				$log.='你没有这个技能。';
				$mode = 'command';$command = '';
				return;
			}
			if(!isset($subcmd)){
				$mode = 'command';$command = '';
				return;
			}elseif($subcmd == 'load_tditm') {
				if(!\skillbase\skill_getvalue(1910,'flag')){
					$log .= '你并没有寄存过道具。';
					$mode = 'command';$command = '';
					return;
				}
				loaditm1910();
				return;
			}elseif($subcmd == 'traveldream_page') {
				skill1910_traveldream_page();
				return;
			}elseif($subcmd == 'td_itm'){
				if(\skillbase\skill_getvalue(1910,'flag')){
					$log .= '你已经寄存过一次道具了。';
					$mode = 'command';$command = '';
					return;
				}
				if(!${'itms'.$td_itm_id}) {
					$log .= '所选择的道具不存在。';
					$mode = 'command';$command = '';
					return;
				}
				$td_itm = Array(
					'name' => $name,
					'itm' => ${'itm'.$td_itm_id},
					'itmk' => ${'itmk'.$td_itm_id},
					'itme' => ${'itme'.$td_itm_id},
					'itms' => ${'itms'.$td_itm_id},
					'itmsk' => ${'itmsk'.$td_itm_id},
				);
				${'itm'.$td_itm_id}='';
				${'itmk'.$td_itm_id}='';
				${'itme'.$td_itm_id}=0;
				${'itms'.$td_itm_id}=0;
				${'itmsk'.$td_itm_id}='';
				saveitm1910($td_itm);
				return;
			}else{
				$log .= '命令参数错误。';
				$mode = 'command';$command = '';
				return;
			}
		}
		$chprocess();
	}
}

?>