<?php

namespace c_mobilephone
{
	function init() {}

	function act()
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		eval(import_module('sys','player','input','c_mapzone'));
		if ($mode == 'special' && $command == 'viewphone') 
		{
			$mode = MOD_C_MOBILEPHONE_PHONEPAGE;
			return;
		}
		if ($mode == 'special' && $command == 'mapzone_special') 
		{
			\c_mapzone\move_to_zone($subcmd);
			return;
		}

		$chprocess();
	}
}

?>