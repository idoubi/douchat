<?php

namespace Addons\IdouIdioms\Controller;
use Mp\Controller\ApiController;

/**
 * 成语接龙响应控制器
 * @author 艾逗笔
 */
class RespondController extends ApiController {

	/**
	 * 微信交互
	 * @param $message array 微信消息数组
	 */
	public function wechat($message = array()) {
		$config = get_addon_settings('IdouIdioms');
		$config['begin_text'] || $config['begin_text'] = '请输入一个成语，比如：一马当先';
		$config['end_text'] || $config['end_text'] = '你已退出成语接龙模式，再次回复【成语接龙】即可进入~';
		$config['end_keyword'] || $config['end_keyword'] = '退出';

		$content = $message['Content'];			// 用户在微信发送的文本消息
		$api = 'http://i.itpk.cn/api.php?question=@cy';										// 成语接龙api
		
		if (!$this->in_context) {
			$reply = $config['begin_text'];
			$this->begin_context(300);
		} else {
			if ($content == $config['end_keyword']) {
				$reply = $config['end_text'];
				$this->end_context();
			} else {
				$reply = file_get_contents($api.$content);
			}
		}

		reply_text($reply);
	}
}

?>