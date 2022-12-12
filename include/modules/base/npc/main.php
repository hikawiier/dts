<?php

namespace npc
{
	function init() 
	{
		eval(import_module('player'));
		
		global $npc_typeinfo;
		$typeinfo+=$npc_typeinfo;
		
		global $npc_killmsginfo;
		$killmsginfo+=$npc_killmsginfo;
		
		global $npc_lwinfo;
		$lwinfo+=$npc_lwinfo;
	}
	
	function check_initnpcadd($typ)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE; 
		return 1;
	}
	
	//把rs_game里一些能复用的功能放进来
	function init_npcdata($npc,$plslist=array(),$mzdata=array()){
		if (eval(__MAGIC__)) return $___RET_VALUE; 
		eval(import_module('sys','map','player','npc','lvlctl','c_mapzone'));
		//获得当前NPC能随机到的地图
		if(!$plslist) $plslist = \map\get_safe_plslist();
		//基本的一些数值
		$npc['endtime'] = $now;
		$npc['hp'] = $npc['mhp'];
		$npc['sp'] = $npc['msp'];
		$npc['ss'] = $npc['mss'];
		//经验值是刚好达到这个等级要求的
		$npc['exp'] = \lvlctl\calc_upexp($npc['lvl'] - 1);
		//熟练度，如果是整数则六系都是这个数值，如果是有键名的数组则直接merge，懒得作输入检查了，有问题请自行排查
		if(is_array($npc['skill'])) {$npc = array_merge($npc,$npc['skill']);}
		else { $npc['wp'] = $npc['wk'] = $npc['wg'] = $npc['wc'] = $npc['wd'] = $npc['wf'] = $npc['skill'];}						
		//性别，r为随机
		if($npc['gd'] == 'r'){$npc['gd'] = rand(0,1) ? 'm':'f';}
		//如果地点数据为随机，则根据输入的数组随机选地点
//		if($npc['pls'] == 99){
//			$plsnum = sizeof($plsinfo);
//			do{$rpls=rand(1,$plsnum-1);}while ($rpls==34);
//			$npc['pls'] = $rpls;
//		}
		if($npc['pls'] == 99){
			if(!empty($plslist)){
				shuffle($plslist);
				$npc['pls'] = $plslist[0];
			}else{
				$npc['pls'] = 0;
			}
		}	
		//调整有危险度要求的NPC的位置 
		//暂时不写防呆判定了，定义NPC危险度的时候注意点——如果是“非固定危险度”的地图，NPC会根据危险度要求跑到别的地方去！
		$tmp_np = $npc['pls']; $tmp_nz = $npc['pzone'];
		if(is_array($npc['intensity']))
		{
			$tmp_intensity = $mzdata[$tmp_np]['intensity'];
			while(!in_array($tmp_intensity,$npc['intensity']))
			{
				shuffle($plslist);
				$npc['pls'] = $plslist[0];
				$tmp_np = $npc['pls'];
				$tmp_intensity = $mzdata[$tmp_np]['intensity'];
			}
		}
		//初始化NPC所在区域格 
		//先看这个NPC是不是要生成在特殊格
		if(array_key_exists($tmp_nz,$mapzoneinfo))
		{ 
			$tmp_speclist = $mzdata[$tmp_np]['speclist'];
			//是特殊格，但是所在地没有，那就在符合危险度区间的地图里找一个有的
			if(!array_key_exists($tmp_nz,$tmp_speclist))
			{
				do{
					shuffle($plslist);
					$npc['pls'] = $plslist[0];
					$tmp_np = $npc['pls'];
					$tmp_speclist = $mzdata[$tmp_np]['speclist'];
					$tmp_intensity = $mzdata[$tmp_np]['intensity'];
				} while(!array_key_exists($tmp_nz,$tmp_speclist) || (is_array($npc['intensity']) && !in_array($tmp_intensity,$npc['intensity'])));
			}
			$npc['pzone'] = $tmp_speclist[$tmp_nz];
			//echo "生成了特殊种类NPC".$npc['name']."，位于地图".$npc['pls']."的区域".$npc['pzone']."，该地图危险度为：".$tmp_intensity."<br>";
		}
		//未定义过区域格的 或者类型非法的 通通按随机处理
		if($npc['pzone'] == 99 || !isset($npc['pzone']))
		{
			$tmp_zoneend = $mzdata[$tmp_np]['zoneend'];
			$pzonelist = range(0,$tmp_zoneend);
			if(!empty($pzonelist)){
				shuffle($pzonelist);
				$npc['pzone'] = $pzonelist[0];
			}else{
				$npc['pzone'] = 0;
			}
		}
		//npc初始状态默认为睡眠
		if(!isset($npc['state'])){$npc['state'] = 1;}
		//技能的获取
		init_npcdata_skills($npc);
		
		return $npc;
	}
	
	//非禁区域列表。
	//type=1:重要NPC（女主）额外回避雏菊、圣G、冰封
	function get_safe_plslist($no_dangerous_zone = true, $type = 0){
		if (eval(__MAGIC__)) return $___RET_VALUE; 
		$ret = $chprocess($no_dangerous_zone, $type);
		//if($no_dangerous_zone && 1 == $type)
			//$ret = array_diff($ret, array(21,26,33));
		return $ret;
	}
	
	function init_npcdata_skills(&$npc)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE; 
		if (isset($npc['skills']) && is_array($npc['skills'])){
			$npc['pid'] = -2;//0和-1都会出问题
			$npc['skills']['460']='0';
			$npc['nskill'] = $npc['nskillpara'] = '';
			\skillbase\skillbase_load($npc);
			foreach ($npc['skills'] as $key=>$value){
				if (defined('MOD_SKILL'.$key)){
					\skillbase\skill_acquire($key,$npc);
					if(is_array($value)){
						foreach($value as $vk => $vv){
							\skillbase\skill_setvalue($key,$vk,$vv,$npc);
						}
					}elseif ($value>0){
						\skillbase\skill_setvalue($key,'lvl',$value,$npc);
					}
				}	
			}
			
			\skillbase\skillbase_save($npc);
			unset($npc['pid']);
		}
	}
	
	function rs_game($xmode = 0) {
		if (eval(__MAGIC__)) return $___RET_VALUE; 
		
		$chprocess($xmode);
		
		eval(import_module('sys','map','player','npc','lvlctl','skillbase','c_mapzone'));
		if ($xmode & 8) {
			//echo " - NPC初始化 - ";
			$db->query("DELETE FROM {$tablepre}players WHERE type>0 ");
			//从数据库拉取区域表，只拉取一次传进init_npcdata()，而不是在里面套娃
			$tmp_mapzonedata = \c_mapzone\load_mapzonedata();
			//初始化miniboss计数器
			$miniboss_list = Array();
			$miniboss_intensity_list = Array();
			//$plsnum = sizeof($plsinfo);
			$npcqry = '';
			$ninfo = get_npclist();
			//生成非禁区列表（不含英灵殿）
			$pls_available = \map\get_safe_plslist();
			//女主等重要NPC的特殊禁区列表
			$pls_available2 = \map\get_safe_plslist(1, 1);
			//外循环：type，编号可以不连续
			foreach ($ninfo as $i => $npcs){
				if(!empty($npcs)) {
					//检查当前模式允许不允许这个type的NPC加入
					if (!check_initnpcadd($i)) continue;

					//如果type属于miniboss 暂时跳过生成 但用数组记录它们出现的时机
					if($npcs['pzone']=='miniboss')
					{
						//echo "检测到一个候选的miniboss：".$i; //插入miniboss列表
						$miniboss_list[$i]=$npcs;
						//初始化可能位于的危险度区间
						if(is_array($npcs['intensity']))
						{
							shuffle($npcs['intensity']);
							$spbi = $npcs['intensity'][0];
						}
						else
						{
							shuffle($map_spawn_miniboss_intensity);
							$spbi = $map_spawn_miniboss_intensity[0];
						}
						//echo "，它所在的危险度为：".$spbi."<br>";
						$miniboss_list[$i]['intensity'] = $spbi;
						//向对应危险度区间内插入该类type 然后随机化列表
						$miniboss_intensity_list[$spbi][] = $i;
						if(rand(0,99)>50)
						{ 
							//随机但不完全随机
							shuffle($miniboss_intensity_list[$spbi]);
						}
						//暂时跳过生成
						continue;
					}
					//得到此type的NPC的加入列表
					$subnum = sizeof($npcs['sub']);
					$jarr = $jarr0 = array_keys($npcs['sub']);
					//定义数或者加入数目是0，不加入
					if (!$subnum || !$npcs['num']) $jarr=array();
					//定义数目大于加入数目，作随机选取
					elseif ($subnum > $npcs['num']) {
						shuffle($jarr);
						$jarr=array_slice($jarr,0,$npcs['num']);
					//定义数目小于加入数目，补足到加入数目
					}elseif ($subnum < $npcs['num']) {
						while(sizeof($jarr) < $npcs['num']) {
							$jarr = array_merge($jarr,$jarr0);
						}
					}
					sort($jarr);
					//内循环，每个加入数目的npc，编号可以不连续
					foreach($jarr as $j){
						//载入npc初始化参数，打个底以免漏变量
						$npc = array_merge($npcinit,$npcs);
						//载入npc个性化参数（sub）
						if(isset($npc['sub']) && is_array($npc['sub'])) $npc = array_merge($npc,$npc['sub'][$j]);
						//类型和编号，放进初始化函数有点蠢
						$npc['type'] = $i;
						$npc['sNo'] = $j;
						//选择所用地图列表
						$tmp_pls_available = 14 == $i ? $pls_available2 : $pls_available;
						//初始化函数
						$npc = init_npcdata($npc, $tmp_pls_available, $tmp_mapzonedata);
						//writeover('a.txt',json_encode($npc['nskillpara']));
//						$npc['endtime'] = $now;
//						$npc['hp'] = $npc['mhp'];
//						$npc['sp'] = $npc['msp'];
//						$npc['ss'] = $npc['mss'];
//						$npc['exp'] = \lvlctl\calc_upexp($npc['lvl'] - 1);
//						if(is_array($npc['skill'])) {$npc = $npc = array_merge($npc,$npc['skill']);}
//						else { $npc['wp'] = $npc['wk'] = $npc['wg'] = $npc['wc'] = $npc['wd'] = $npc['wf'] = $npc['skill'];}						
//						if($npc['gd'] == 'r'){$npc['gd'] = rand(0,1) ? 'm':'f';}
//						do{$rpls=rand(1,$plsnum-1);}
//						while ($rpls==34);
//						if($npc['pls'] == 99){$npc['pls'] = $rpls; }
//						$npc['state'] = 1;

						//按数据表字段进行格式化并insert
						$npc=\player\player_format_with_db_structure($npc);
						$db->array_insert("{$tablepre}players", $npc);
						
//						$npcqrylit = "(";
//						$npcqry = "(";
//						foreach ($npc as $key => $value)
//						{
//							if (in_array($key,$db_player_structure))
//							{
//								$npcqrylit .= $key.",";
//								$npcqry .= "'".$npc[$key]."',";
//							}
//						}
//						$npcqrylit=substr($npcqrylit,0,strlen($npcqrylit)-1).")";
//						$npcqry=substr($npcqry,0,strlen($npcqry)-1).")";
//						
//						$qry = "INSERT INTO {$tablepre}players ".$npcqrylit." VALUES ".$npcqry;
//						$db->query($qry);
//						unset($qry);
						
//						if (isset($npc['skills']) && is_array($npc['skills'])){
//							$npc['skills']['460']='0';
//							$qry="SELECT * FROM {$tablepre}players WHERE type>'0' ORDER BY pid DESC LIMIT 1";
//							$result=$db->query($qry);
//							$pr=$db->fetch_array($result);
//							$pp=\player\fetch_playerdata_by_pid($pr['pid']);
//							foreach ($npc['skills'] as $key=>$value){
//								if (defined('MOD_SKILL'.$key)){
//									\skillbase\skill_acquire($key,$pp);
//									if ($value>0){
//										\skillbase\skill_setvalue($key,'lvl',$value,$pp);
//									}
//								}	
//							}
//							\player\player_save($pp);
//						}
						//藏好自己，做好清理
						unset($npc);
					}
				}
			}
			//进行第二轮生成 处理miniboss
			foreach ($miniboss_list as $i => $npcs){
				if(!empty($npcs)) {
					//重新取得miniboss要生成的危险度区
					$spbi = $npcs['intensity'];
					//候选列表里有多个miniboss时 只生成第一个
					if($i !== $miniboss_intensity_list[$spbi][0])
					{
						//echo "当前miniboss种类".$i."与首位候选miniboss种类".$miniboss_intensity_list[$spbi][0]."不匹配<br>";
						continue;
					}
					//再包好……
					$npcs['intensity'] = Array($spbi);
					$subnum = sizeof($npcs['sub']);
					$jarr = $jarr0 = array_keys($npcs['sub']);
					if (!$subnum || !$npcs['num']) $jarr=array();
					elseif ($subnum > $npcs['num']) {
						shuffle($jarr);
						$jarr=array_slice($jarr,0,$npcs['num']);
					}elseif ($subnum < $npcs['num']) {
						while(sizeof($jarr) < $npcs['num']) {
							$jarr = array_merge($jarr,$jarr0);
						}
					}
					sort($jarr);
					foreach($jarr as $j){
						$npc = array_merge($npcinit,$npcs);
						if(isset($npc['sub']) && is_array($npc['sub'])) $npc = array_merge($npc,$npc['sub'][$j]);
						$npc['type'] = $i;
						$npc['sNo'] = $j;
						$tmp_pls_available = 14 == $i ? $pls_available2 : $pls_available;
						$npc = init_npcdata($npc, $tmp_pls_available, $tmp_mapzonedata);
						$npc=\player\player_format_with_db_structure($npc);
						$db->array_insert("{$tablepre}players", $npc);
						unset($npc);
					}
				}
			}
		}
	}
	
	function get_npclist(){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','map','npc'));
		return $npcinfo;
	}
	
	//NPC回避禁区的处理
	function addarea_pc_process_single($sub, $atime){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess($sub, $atime);
		eval(import_module('sys','map','npc','c_mapzone'));
		$pid = $sub['pid'];
		$o_sub = $sub;
		$pls_available = \map\get_safe_plslist();//不能移动去的区域，如果不存在，NPC不移动
		$pls_available2 = \map\get_safe_plslist(1, 1);
		if($sub['type'] && !in_array($sub['type'],$killzone_resistant_typelist) && $pls_available){
			//选择所用的安全区列表
			$tmp_pls_available = 14 == $sub['type'] ? $pls_available2 : $pls_available;
			shuffle($tmp_pls_available);
			$sub['pls'] = $tmp_pls_available[0];
			$tmp_pzone_available = $uip['mapzone_end'][$sub['pls']];
			$sub['pzone'] = $sub['pzone']>$tmp_pzone_available ? rand(0,$tmp_pzone_available) : $sub['pzone'];
			$db->array_update("{$tablepre}players",$sub,"pid='$pid'",$o_sub);
			\player\post_pc_avoid_killarea($sub, $atime);
			//echo $sub['pid'].' ';
		}
	}
	
//	function add_new_killarea($where,$atime)
//	{
//		if (eval(__MAGIC__)) return $___RET_VALUE;
//		
//		eval(import_module('sys','map','npc'));
//		$plsnum = sizeof($plsinfo) - 1;
//		if ($areanum >= sizeof($plsinfo) - 1) return $chprocess($where);
//		$query = $db->query("SELECT * FROM {$tablepre}players WHERE pls={$where} AND type>0 AND hp>0");
//		while($sub = $db->fetch_array($query)) 
//		{
//			$pid = $sub['pid'];
//			if (!in_array($sub['type'],$killzone_resistant_typelist))
//			{
//				$pls = $arealist[rand($areanum+1,$plsnum)];
//				if ($areanum+1 < $plsnum)
//				{
//					while ($pls==34) {$pls = $arealist[rand($areanum+1,$plsnum)];}
//				}
//				$db->query("UPDATE {$tablepre}players SET pls='$pls' WHERE pid=$pid");
//			}
//		}
//		$chprocess($where,$atime);
//	}
	
	function get_player_killmsg(&$pdata)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		eval(import_module('player'));
		if ($pdata['type']>0)
		{
			if (isset($killmsginfo[$pdata['type']])){
				if(is_array($killmsginfo[$pdata['type']])) $kilmsg = $killmsginfo[$pdata['type']][$pdata['name']];
				else $kilmsg = $killmsginfo[$pdata['type']];
			}else  $kilmsg = '';
			return $kilmsg;
		}
		else  return $chprocess($pdata);
	}
	
	function get_player_lastword(&$pdata)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		
		if ($pdata['type']>0)
		{
			eval(import_module('player','npc'));
			if($pdata['hp'] > 0){
				if(is_array ( $npc_revive_info [$pdata['type']] )){
					if (isset($npc_revive_info[$pdata['type']][$pdata['name']]))
						return $npc_revive_info[$pdata['type']][$pdata['name']];
				}else {
					if (isset($npc_revive_info[$pdata['type']]))
						return $npc_revive_info[$pdata['type']];
				}
			}
			if (is_array ( $lwinfo [$pdata['type']] )) {
				if (isset($lwinfo[$pdata['type']][$pdata['name']]))
					$lstwd = $lwinfo[$pdata['type']][$pdata['name']];
				else  $lstwd = '';
			} else {
				if (isset($lwinfo[$pdata['type']]))
					$lstwd = $lwinfo[$pdata['type']];
				else  $lstwd = '';
			}
			return $lstwd;
		}
		else  return $chprocess($pdata);
	}
	
}

?>