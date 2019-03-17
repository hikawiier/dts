<?php

namespace skill1998
{	
	function init() 
	{
		define('MOD_SKILL1998_INFO','hidden;upgrade;');
	}
	
	function acquire1998(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function lost1998(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function check_unlocked1998(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}
	
	function activate1998()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('skill1998','player','logger','sys','itemmain','map'));
		\player\update_sdata();
		$log.="你发动了【守护精灵的援护】。<br>";
		$ret = (int)floor($areanum/$areaadd)/10;
		$ret += 1;
		$log.="你的基础属性提升了{$ret}倍！<br>";
		$att=floor($att*$ret);$def=floor($def*$ret);$mhp=floor($mhp*$ret);$msp=floor($msp*$ret);$mss=floor($mss*$ret*2);
		if(\skillbase\skill_query(72)){
			$x=(int)\skillbase\skill_getvalue(72,'t');
			$x = $x>3 ? $x-3 : 0;
			\skillbase\skill_setvalue(72,'t',$x);
			$log.="你可学习的技能次数增加了！<br>";
		}
		for($i=0;$i<=6;$i++)
		{
			if(${'itms'.$i} && strpos(${'itmk'.$i},'HB')!==false)
			{
				$itm['itm']=&${'itm'.$i}; $itm['itmk']=&${'itmk'.$i};
				$itm['itme']=&${'itme'.$i}; $itm['itms']=&${'itms'.$i}; $itm['itmsk']=&${'itmsk'.$i};
				$metal_flag += $itm['itme']*$itm['itms'];	
				$itm['itm']='宛如不醒之梦';$itm['itme']=50000;
				$itm['itms']='∞';$itm['itmsk']='x';
				$log.="你的补给品发生了微妙的变化！<br>";
				break;
			}
		}
		$hp=$mhp;$sp=$msp;$ss=$mss;$rage=100;
		$log.="你的状态回复正常了！<br>";
		$log.="你获得了技能书《隐匿》。<br>";
		$itm0='《隐匿》';$itmk0='VS';$itme0=1;$itms0=1;$itmsk0='246';
		\itemmain\itemget();
		\skillbase\skill_lost(1998,$pa);
	}
	
	function bufficons_list()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		\player\update_sdata();
		if ((\skillbase\skill_query(1998,$sdata))&&check_unlocked1998($sdata))
		{
			$z=Array(
				'style' => 3,
				'disappear' => 0,
				'clickable' => 1,
				'hint' => '「守护精灵的援护」',
				'activate_hint' => '点击获得守护精灵的援护',
				'onclick' => "$('mode').value='special';$('command').value='skill1998_special';$('subcmd').value='activate';postCmd('gamecmd','command.php');this.disabled=true;",
			);
			\bufficons\bufficon_show('img/skill1998.gif',$z);
		}
		$chprocess();
	}
}

?>
