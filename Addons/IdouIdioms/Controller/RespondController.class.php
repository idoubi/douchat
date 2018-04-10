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
		
		if (!$this->in_context) {                                                               // 首次进入成语接龙模式
            $reply = $config['begin_text'];
            $this->begin_context(300);
            reply_text($reply);
        } else {                                                                                                // 处于成语接龙会话模式
            if ($content == $config['end_keyword']) {                                       // 输入关键词退出成语接龙模式
                    $reply = $config['end_text'];
                    $this->end_context();
                    reply_text($reply);
            } elseif ($content == '成语接龙') {
                    $this->end_context();
                    $this->begin_context(300);
                    $reply = $config['begin_text'];
                    reply_text($reply);
            } else {                                                                                                        // 继续成语接龙模式
                    $context = $this->get_context();                                                        // 获取上下文缓存数据
                    if (!isset($context['idiom'])) {                                                        // 如果不存在上一个成语
                            $reply = file_get_contents($api.$content);
                            if (strlen($reply) == 15) {                                                             // 如果响应的是四个字标准成语，则把成语缓存起来
                                    $this->keep_context(300, array('idiom'=>$reply));       // 保持成语接龙会话模式
                            } else {
                                    $this->keep_context(300);                                                       // 如果返回的不是标准成语，则不做缓存，继续会话
                            }
                            reply_text($reply);
                    } else {
                            $idiom = $context['idiom'];                                                             // 获取上一个输入的成语
                            if (substr($idiom, -3) == substr($content, 0, 3)) {                     // 如果本次输入的成语能接上上一个成语
                                    $reply = file_get_contents($api.$content);
                                    if (strlen($reply) == 15) {                                                             // 如果响应的是四个字标准成语，则把成语缓存起来
                                            $this->keep_context(300, array('idiom'=>$reply));       // 保持成语接龙会话模式
                                    } else {
                                            $this->keep_context(300, array('idiom'=>$idiom));       // 如果返回的不是标准成语，则使用上一个成语保持会话
                                    }
                                    reply_text($reply);
                            } else {
                                    $this->keep_context(300, array('idiom'=>$idiom));               // 如果输入的成语不能接上上一个成语
                                    reply_text('你输入的【'.$content.'】与上一个成语【'.$idiom.'】不能接上哦，请重新输入一个成语~输入【成语接龙】可开启新一轮接龙模式~');
                            }
                    }
            }
        }

		reply_text($reply);
	}
}

?>