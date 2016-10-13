<?php

namespace Addons\IdouSign\Controller;
use Mp\Controller\ApiController;

/**
 * 微签到响应控制器
 * @author 艾逗笔
 */
class RespondController extends ApiController {

	/**
	 * 微信交互
	 * @param $message array 微信消息数组
	 */
	public function wechat($message = array()) {
		$settings = get_addon_settings('IdouSign');
		$settings['per_score'] || $settings['per_score'] = 0;		// 每次签到获取的积分
		$record = M('idou_sign_record')->where(array('mpid'=>get_mpid(),'openid'=>get_openid()))->order('sign_time desc')->find();	// 获取最近的一次签到记录
		if (empty($record)) {		// 首次签到
			$insert['mpid'] = get_mpid();
			$insert['openid'] = get_openid();
			$insert['sign_time'] = time();									// 记录签到时间
			$insert['sign_date'] = date('Ymd', $insert['sign_time']);		// 记录签到日期
			$insert['total_times'] = 1;										// 总签到次数
			$insert['continue_times'] = 1;									// 连续签到次数
			$insert['score'] = $settings['per_score'];						// 签到获取积分
			$res = M('idou_sign_record')->add($insert);
			if ($res) {
				$sign_success_tip = '签到成功，你获得'.$settings['per_score'].'积分';
				reply_text($sign_success_tip);
			} else {
				reply_text('签到失败');
			}
		} else {					// 再次签到
			$now = time();							// 签到时间
			$today = date('Ymd', $now);				// 签到日期
			if (strtotime($today) - strtotime($record['sign_date']) == 0) {		// 当天已签到	
				reply_text('你今天已签到，请明天再来');
			} elseif (strtotime($today) - strtotime($record['sign_date']) == 24*3600) { 										// 连续签到
				$continue_times = $record['continue_times'] + 1;					// 连续签到天数加1
			} else {
				$continue_times = 1;												// 连续签到天数重置为1
			}
			$insert['mpid'] = get_mpid();
			$insert['openid'] = get_openid();
			$insert['sign_time'] = $now;									// 记录签到时间
			$insert['sign_date'] = $today;									// 记录签到日期
			$insert['total_times'] = $record['total_times']+1;				// 总签到次数
			$insert['continue_times'] = $continue_times;					// 连续签到次数
			$insert['score'] = $settings['per_score'];						// 签到获取积分
			$res = M('idou_sign_record')->add($insert);
			if ($res) {
				if ($continue_times == 1) {
					$sign_success_tip = '签到成功，你获得'.$settings['per_score'].'积分，总签到次数：'.$insert['total_times'];
				} else {
					$sign_success_tip = '签到成功，你获得'.$settings['per_score'].'积分，总签到次数：'.$insert['total_times'].'，连续签到次数：'.$continue_times;
				}
				reply_text($sign_success_tip);
			} else {
				reply_text('签到失败');
			}
		}
	}
}

?>