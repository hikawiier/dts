#campfireMod HOW TO INSTALL
1、 将campfire文件夹放入newdts的modules文件夹中；
1、 打开Install文件夹，将sql文件夹内的文件放入对应目录；
2、 在modules.list.php文件中写入CampfireModuleslist中的模块信息；
3、 重新安装游戏，并重设模块缓存；
4、 冲！

---

#campfireMod todolist：
- 设计结局：命途莫测（里）
    优先级：中
    已知问题：请见群共享内的流程设计方案；
- 智能摸物机器人
    优先级：高
    已知问题：暂无    
- 清理并转移资源层项目（itemmix, itemshop, npc, npcchat）
    优先级：低
    已知问题：
    > 1、转移后与帮助模板不兼容；
- 清理并转移hidden_area功能的实现方式（map, sys, explore, player, weather, itemmain, trap,  npc, event, skill452）
    优先级：低
    已知问题：
    > 1、rs_game()函数使用$chprocess()将多个初始化环节拼接在一起，在无法了解函数彼此间的优先级判定方式前，想要改变判定条件，只能将所有初始化环节拼接为一个完整的rs_game()函数，并直接重载……；
- 
