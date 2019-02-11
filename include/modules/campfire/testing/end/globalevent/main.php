<?php

namespace globalevent
{
	function init()
	{
	}
	function crm_lose_event()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','logger','addnpc'));
		\addnpc\addnpc(75,0,20);
		\addnpc\addnpc(75,1,20);
		\addnpc\addnpc(75,2,20);
	}	
	function player_kill_enemy(&$pa,&$pd,$active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		if ($pd['name']=='红暮' && $pd['type']==1)
		{
			eval(import_module('globalevent'));
			crm_lose_event();
		}
		$chprocess($pa, $pd, $active);
	}
	function update_game_event($eventnm)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
	}
}

?>
