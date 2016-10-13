<?php

namespace Addons\IdouAround\Controller;
use Mp\Controller\ApiController;

/**
 * 周边查询响应控制器
 * @author 艾逗笔
 */
class RespondController extends ApiController {

	/**
	 * 微信交互
	 * @param $message array 微信消息数组
	 */
	public function wechat($message = array()) {
		if ($message['MsgType'] == 'location') {		// 发送地理位置
			reply_text($this->in_context);
			if (!$this->in_context) {		// 不在消息上下文			

			} else {
				$context = $this->get_context();
				if (!empty($context['what'])) {
					$what = $context['what'];
					$url = 'http://apis.map.qq.com/uri/v1/search?keyword='.$what.'&center='.$message['Location_X'].','.$message['Location_Y'].'&radius=500&referer=LBS';
					$articles[0] = array(
						'Title' => '周边'.$what,
						'Description' => '点此查看',
						'PicUrl' => 'http://img1.imgtn.bdimg.com/it/u=2137379729,3835887084&fm=206&gp=0.jpg',
						'Url' => $url
					);
					reply_news($articles);
				}
			}	
		} elseif ($message['MsgType'] == 'text') {										// 发送文本消息
			$content = $message['Content'];
			if ($content == '退出') {
				$this->end_context();
				reply_text('已退出周边查询模式');
			}
			preg_match('/^周边(.+)/', $content, $m);
			if ($m[1]) {								// 要查询的信息类型存在
				$this->begin_context(30,array('what'=>$m[1]));
				reply_text('请发送地理位置来查询周边的'.$m[1].'信息');
			} else {	
				reply_text('请输入要查询的信息类型，如：周边KTV，周边酒店');
			}
		}
		

	}
}

?>