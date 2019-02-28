<?php

namespace campfire_itemextra
{
	//合成成功后可以触发隐藏合成的内容
	$mix_sh_arr = Array(
		//合成 青蔷薇后，可以开启 黑蔷薇的隐藏合成
		'「青蔷薇」' => '「黑蔷薇」',
	);
	$mix_hlist_arr = Array(
		'「黑蔷薇」' => Array
		(
			0 => Array('「青蔷薇」'),
			1 => Array('黑色方块','黑色雏菊','黑色磨刀石','黑板擦','黑魔法-权利','黑魔法-奇技','黑色終曲『HONOUR』','黑色连衣裙'),
		),
	);
	
	function init() 
	{
		eval(import_module('item_slip'));
		$item_slip_metagame_list['「黑蔷薇」'] = Array('「黑蔷薇」','WK',1222221,'∞','BNnrpvV');
	}
	
	function itemmix_success()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('item_slip','sys','campfire_itemextra'));
		//从合成结果判断，如果合成的是随机生成的合成，那么在合成成功后删除该合成
		if(in_array($itm0,array_keys($mix_hlist_arr)))
		{
			if(!empty($gamevars['campfire_metagame'])) {
				$file = GAME_ROOT.'./gamedata/cache/'.$gamevars['campfire_metagame'].'.htm';
				if(file_exists($file)) unlink($file);
				$gamevars['campfire_metagame'] = NULL;
				$gamevars['campfire_metagame_mixinfo'] = '';
				\sys\save_gameinfo();
			}
		}
		elseif(in_array($itm0,array_keys($mix_sh_arr)))
		{
			if(empty($gamevars['campfire_metagame'])) {
			//返回一个随机数字，并生成以这个数字命名的文本文件
				do {
					$ret = rand(1001,1999);
					$file = GAME_ROOT.'./gamedata/cache/'.$ret.'.htm';
				} while(file_exists($file));
				//要解锁的隐藏合成与合成配方
				$hin = $mix_sh_arr[$itm0];
				$hl = $mix_hlist_arr[$hin];
				//内容是合成
				$arr1 = $hl[0];
				$arr2 = $hl[1];
				$arr3 = $hl[2];
				$arr4 = $hl[3];
				$stuff = array();
				foreach(array($arr1,$arr2,$arr3,$arr4) as $v){
					if(!empty($v))
					{
						shuffle($v);
						$nowv = $v[0];
						$stuff[] = $nowv;
					}
				}
				$cont = implode('+',$stuff).'='.$hin;
				$cont_html = '<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>'.$cont.'</body>';
				writeover($file, $cont_html);
				chmod($file,0777);
				//记录一下生成的文件，避免重复生成，游戏结束时删除
				$gamevars['campfire_metagame'] = $ret;
				$gamevars['campfire_metagame_mixinfo'] = $cont;
				\sys\save_gameinfo();
			}else{
				$ret = $gamevars['campfire_metagame'];
			}					
		}	
		$chprocess();
	}	
	
	//meta特殊合成
	function itemmix_recipe_check($mixitem){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','itemmix','item_slip'));
		if(count($mixitem) >= 2){	
			if(!empty($gamevars['campfire_metagame'])) {//如果有metagame数据，则追加一项合成
				list($stuff,$result) = item_slip_get_puzzle($gamevars['campfire_metagame']);
				if(isset($item_slip_metagame_list[$result]) && empty($mixinfo['campfire_metagame'])) {
					$mixinfo['campfire_metagame'] = array('class' => 'hidden', 'stuff' => $stuff, 'result' => $item_slip_metagame_list[$result]);
				}
			}
		}
		return $chprocess($mixitem);
	}
	
	function item_slip_get_puzzle($puzzleid){
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys'));
		if(!empty($gamevars['campfire_metagame']) && $puzzleid == $gamevars['campfire_metagame']) {
			$ret = array();
			$cont = $gamevars['campfire_metagame_mixinfo'];
			list($stuff0, $result) = explode('=',$cont);
			$stuff = explode('+',$stuff0);
			$ret[0] = $stuff;
			$ret[1] = $result;
			return $ret;
		}
		$chprocess($puzzleid);
	}
	
	function post_gameover_events()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		$chprocess();
		eval(import_module('sys'));
		if(!empty($gamevars['campfire_metagame'])) {
			$file = GAME_ROOT.'./gamedata/cache/'.$gamevars['campfire_metagame'].'.htm';
			if(file_exists($file)) unlink($file);
		}
	}
}
?>