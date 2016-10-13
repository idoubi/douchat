<?php

namespace Addons\IdouFeedback\Controller;
use Mp\Controller\AddonsController;

/**
 * 意见反馈后台管理控制器
 * @author 艾逗笔
 */
class WebController extends AddonsController {

	/**
	 * 反馈列表
	 */
	public function lists() {
		$this->setModel('idou_feedback')
			 ->setListMap(array('mpid'=>get_mpid()))
			 ->setListOrder('create_time desc')
			 ->addListItem('openid', '反馈者头像', 'function', array('function_name'=>'get_fans_headimg'))
			 ->addListItem('name', '反馈者姓名')
			 ->addListItem('content', '反馈内容')
			 ->addListItem('create_time', '反馈时间', 'function', array('function_name'=>'date', 'params'=>'Y-m-d H:i:s,###'))
			 ->common_lists();
	}

}

?>