<?php

namespace Addons\IdouSign\Controller;
use Mp\Controller\AddonsController;

/**
 * 微签到后台管理控制器
 * @author 艾逗笔
 */
class WebController extends AddonsController {

	/**
	 * 签到记录
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function record() {
		$this->setModel('idou_sign_record')
			 ->setListMap(array('mpid'=>get_mpid()))
			 ->setListOrder('sign_time desc')
			 ->addListItem('openid', '用户头像', 'function', array('function_name'=>'get_fans_headimg'))
			 ->addListItem('openid', '用户昵称', 'function', array('function_name'=>'get_fans_nickname'))
			 ->addListItem('sign_time', '签到时间', 'function', array('function_name'=>'date','params'=>'Y-m-d H:i:s,###'))
			 ->addListItem('continue_times', '连续签到次数')
			 ->addListItem('score', '所获积分')
			 ->common_lists();
	}
}

?>