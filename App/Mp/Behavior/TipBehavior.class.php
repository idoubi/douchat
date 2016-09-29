<?php 

namespace Mp\Behavior;
use Think\Behavior;

/**
 * 生成提示信息行为类
 * @author 艾逗笔<765532665@qq.com>
 */
class TipBehavior extends Behavior {

	public function run(&$params) {
		$addon = get_addon();		// 插件名称
		if ($addon) {
			if (ACTION_NAME == 'rule') {
				$tip = '用户在微信中发送的文本消息匹配到此处设置的关键词时，系统会把用户发送的消息分发到此插件的交互控制器进行处理';
			} elseif (ACTION_NAME == 'entry') {
				$tip = '用户在微信中发送的文本消息匹配到此处设置的关键词时，系统会根据此处设置的封面参数回复一条单图文消息，用户点击图文消息可进入对应的功能页面';
			}
		}
		return $tip;
	}
}

 ?>