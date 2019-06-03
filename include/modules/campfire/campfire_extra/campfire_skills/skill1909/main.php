<?php

namespace skill1909
{
	
	function init() 
	{
		define('MOD_SKILL1909_INFO','card;unique;');
		eval(import_module('clubbase'));
		$clubskillname[1909] = '<span class="red b">自爆</span>';
	}
	
	function acquire1909(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		\skillbase\skill_setvalue(1909,'lvl','1',$pa);
	}
	
	function lost1909(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
	
	function check_unlocked1909(&$pa)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		return 1;
	}
	
	function add_area_once1909($awn)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','skill1901'));
		$awlog = "警告：幻境遭到干扰，下一次禁区即将到来了！";
		addnews($now, 'addarea1909',$awn);
		\sys\systemputchat($now,'addarea1909',$awlog);
		$areatime = $now+1;
		save_gameinfo();
	}

	function apply_damage(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if (\skillbase\skill_query(1909,$pd) && $pd['hp']<=$pa['dmg_dealt'] && (int)\skillbase\skill_getvalue(1909,'lvl',$pd))
		{
			eval(import_module('logger'));
			$pd['hp']=0; $pd['deathmark']=40;
			$pd_var = (int)\skillbase\skill_getvalue(1909,'lvl',$pd);$pd_var--;
			\skillbase\skill_setvalue(1909,'lvl',$pd_var,$pd);
			$suicidedmg = $pa['dmg_dealt'];
			if($suicidedmg < $pa['hp'])
			{
				$pa['hp'] -= $suicidedmg;
				if ($active)
					$log .= "<span class=\"yellow b\">你暴风骤雨般的攻击将对方打得毫无还手之力，<br>
					正当你欺身向前，准备了结对方的性命时，对方身上忽然迸发出一阵奇异的流光！</span><br>
					<br>
					<font size=5><b><font color='#00AEF2'>U</font><font color='#00AEE6'>n</font><font color='#00AEDA'>l</font><font color='#00AECE'>i</font><font color='#00AEC2'>m</font><font color='#00AEB6'>i</font><font color='#00AEAA'>t</font><font color='#00AE9D'>e</font><font color='#00AE91'>d</font> <font color='#00AE85'>C</font><font color='#00AE79'>o</font><font color='#00AE6D'>d</font><font color='#00AE61'>e</font> <font color='#00AE55'>W</font><font color='#00AE48'>o</font><font color='#00AE3C'>r</font><font color='#00AE30'>k</font><font color='#00AE24'>s</font><font color='#00AE18'>!</font></b></font><br>
					<br>
					<span class=\"yellow b\">这是……信息干扰炸弹！</span><br>
					狂暴的信息流瞬间掠过你的大脑，对你造成了<span class=\"red b\">$suicidedmg</span>点伤害！<br>";
				else	
					$log .= "<span class=\"yellow b\">你被对方暴风骤雨般的攻击打得毫无还手之力。<br>
					对方欺身向前，准备了结你的性命。但从你身上忽然迸发出奇异的流光！：</span><br>
					<br>
					<font size=5><b><font color='#00AEF2'>U</font><font color='#00AEE6'>n</font><font color='#00AEDA'>l</font><font color='#00AECE'>i</font><font color='#00AEC2'>m</font><font color='#00AEB6'>i</font><font color='#00AEAA'>t</font><font color='#00AE9D'>e</font><font color='#00AE91'>d</font> <font color='#00AE85'>C</font><font color='#00AE79'>o</font><font color='#00AE6D'>d</font><font color='#00AE61'>e</font> <font color='#00AE55'>W</font><font color='#00AE48'>o</font><font color='#00AE3C'>r</font><font color='#00AE30'>k</font><font color='#00AE24'>s</font><font color='#00AE18'>!</font></b></font><br><br>
					<br>
					<span class=\"yellow b\">就让你尝尝我巨大的……信息干扰炸弹吧！</span><br>
					狂暴的信息流瞬间掠过对方的大脑，对<span class=\"yellow b\">".$pa['name']."</span>造成了<span class=\"red b\">$suicidedmg</span>点伤害！<br>";
			}	
			else
			{					
				add_area_once1909($pd['name']);
				if ($active)
					$log .= "<span class=\"yellow b\">你暴风骤雨般的攻击将对方打得毫无还手之力，<br>
					正当你欺身向前，准备了结对方的性命时，对方身上忽然迸发出一阵奇异的流光！</span><br>
					<br>
					<font size=5><b><font color='#00AEF2'>U</font><font color='#00AEE6'>n</font><font color='#00AEDA'>l</font><font color='#00AECE'>i</font><font color='#00AEC2'>m</font><font color='#00AEB6'>i</font><font color='#00AEAA'>t</font><font color='#00AE9D'>e</font><font color='#00AE91'>d</font> <font color='#00AE85'>C</font><font color='#00AE79'>o</font><font color='#00AE6D'>d</font><font color='#00AE61'>e</font> <font color='#00AE55'>W</font><font color='#00AE48'>o</font><font color='#00AE3C'>r</font><font color='#00AE30'>k</font><font color='#00AE24'>s</font><font color='#00AE18'>!</font></b></font><br>
					<br>
					<span class=\"yellow b\">这是……信息干扰炸弹！</span><br>
					狂暴的信息流喷涌而出，你还以为自己就要死在这了，但那信息流却径直掠过了你！<br>
					信息炸弹突入了幻境系统的核心中枢，干扰了其正常运转！<br>";
				else
					$log .= "<span class=\"yellow b\">你被对方暴风骤雨般的攻击打得毫无还手之力。<br>
					对方欺身向前，准备了结你的性命。但从你身上忽然迸发出奇异的流光！：</span><br>
					<br>
					<font size=5><b><font color='#00AEF2'>U</font><font color='#00AEE6'>n</font><font color='#00AEDA'>l</font><font color='#00AECE'>i</font><font color='#00AEC2'>m</font><font color='#00AEB6'>i</font><font color='#00AEAA'>t</font><font color='#00AE9D'>e</font><font color='#00AE91'>d</font> <font color='#00AE85'>C</font><font color='#00AE79'>o</font><font color='#00AE6D'>d</font><font color='#00AE61'>e</font> <font color='#00AE55'>W</font><font color='#00AE48'>o</font><font color='#00AE3C'>r</font><font color='#00AE30'>k</font><font color='#00AE24'>s</font><font color='#00AE18'>!</font></b></font><br><br>
					<br>
					<span class=\"yellow b\">就让你尝尝我巨大的……信息干扰炸弹吧！</span><br>
					狂暴的信息流直接越过了你的敌人，并突入了幻境系统的核心中枢，干扰了其正常运转！<br>";
			}
		}
		return $chprocess($pa,$pd,$active);
	}
	
	function parse_news($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr = array())
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player'));
		
		if($news == 'addarea1909') 
			return "<li id=\"nid$nid\">{$hour}时{$min}分{$sec}秒，<span class=\"yellow b\">{$a}死前引爆的信息炸弹干扰了幻境系统的正常运转，使得禁区提前到来了！</span></li>";
		
		return $chprocess($nid, $news, $hour, $min, $sec, $a, $b, $c, $d, $e, $exarr);
	}
}

?>
