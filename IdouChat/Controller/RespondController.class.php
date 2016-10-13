<?php 

namespace Addons\IdouChat\Controller;
use Mp\Controller\ApiController;

/**
 * 聊天机器人响应控制器
 * @author 艾逗笔
 */
class RespondController extends ApiController {

	/**
	 * 微信交互
	 * @param $message array 微信消息数组
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function wechat($message = array()) {
		$settings = get_addon_settings('IdouChat');
		$settings['enter_tip'] || $settings['enter_tip'] = '你想聊点什么呢';
		$settings['keep_time'] || $settings['keep_time'] = 300;
		$settings['exit_keyword'] || $settings['exit_keyword'] = '退出';
		$settings['exit_tip'] || $settings['exit_tip'] = '下次无聊的时候可以再找我聊天哦';
		if (!$settings['api_url'] || !$settings['api_key']) {
			reply_text('机器人聊天接口未填写，暂时不能使用此功能');
			exit();
		}
		if ($message['MsgType'] == 'voice') {
			if ($settings['can_voice'] == '1') {
				$content = $message['Recognition'];		// 语音识别，直接开启机器人聊天模式
				$reply = $this->turingAPI($content);
				if (is_array($reply)) {
					return reply_news($reply);
				} else {
					return reply_text($reply);
				}
			}
		} else {
			$content = $message['Content'];			// 通过消息上下文机制与机器人展开聊天
			if (!$this->in_context) {
				$reply = $settings['enter_tip'];
				$this->begin_context($settings['keep_time']);					// 开启上下文模式
			} else {
				if ($content == $settings['exit_keyword']) {
					$reply = $settings['exit_tip'];
					$this->end_context();
				} else {
					$reply = $this->turingAPI($content);
					$this->keep_context($settings['keep_time']);				// 保持消息上下文
				}
			}

			if (is_array($reply)) {
				return reply_news($reply);
			} else {
				return reply_text($reply);
			}
		}
	}

	// 图灵机器人
	private function turingAPI($keyword) {
		$settings = get_addon_settings('IdouChat');
		$settings['api_url'] || $settings['api_url'] = '';
		$settings['api_key'] || $settings['api_key'] = '';
		$api_url = $settings['api_url'] . "?key=" . $settings['api_key'] . "&info=" . $keyword;
		
		$result = file_get_contents ( $api_url );
		$result = json_decode ( $result, true );
		if ($_GET ['format'] == 'test') {
			dump ( '图灵机器人结果：' );
			dump ( $result );
		}
		if ($result ['code'] > 40000 && $result['code'] < 40008) {
			if ($result ['code'] < 40008 && ! empty ( $result ['text'] )) {
				return '图灵机器人请你注意：' . $result ['text'];
			} else {
				return false;
			}
		}
		switch ($result ['code']) {
			case '100000' :
				return $result['text'];
				break;
			case '200000' :
				$text = $result ['text'] . ',<a href="' . $result ['url'] . '">点击进入</a>';
				return $text;
				break;
			case '301000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['name'],
							'Description' => $info ['author'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			case '302000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['article'],
							'Description' => $info ['source'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			case '304000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['name'],
							'Description' => $info ['count'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			case '305000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['start'] . '--' . $info ['terminal'],
							'Description' => $info ['starttime'] . '--' . $info ['endtime'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			case '306000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['flight'] . '--' . $info ['route'],
							'Description' => $info ['starttime'] . '--' . $info ['endtime'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			case '307000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['name'],
							'Description' => $info ['info'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			case '308000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['name'],
							'Description' => $info ['info'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			case '309000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['name'],
							'Description' => '价格 : ' . $info ['price'] . ' 满意度 : ' . $info ['satisfaction'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			case '310000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['number'],
							'Description' => $info ['info'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			case '311000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['name'],
							'Description' => '价格 : ' . $info ['price'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			case '312000' :
				foreach ( $result ['list'] as $info ) {
					$articles [] = array (
							'Title' => $info ['name'],
							'Description' => '价格 : ' . $info ['price'],
							'PicUrl' => $info ['icon'],
							'Url' => $info ['detailurl'] 
					);
				}
				return $articles;
				break;
			default :
				if (empty ( $result ['text'] )) {
					return false;
				} else {
					return $result ['text'];
				}
		}
		return true;
	}
}