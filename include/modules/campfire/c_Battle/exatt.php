<?php

namespace c_battle
{
	//属性攻击相关

	//滞留型伤害/效果阶段 这该叫啥？
	function check_dot_effect(&$pa, &$pd, $active)
	{
		if (eval(__MAGIC__)) return $___RET_VALUE;
		//协战状态不判定
		if($pa['is_colatk'] || $pd['is_colatk']) return;
	}

	//修改因非直接伤害而在战斗中暴毙而死时的log
	//在原文件里直接改了，不搬了。
}

?>