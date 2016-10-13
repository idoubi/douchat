<?php 

namespace Addons\IdouDonate\Controller;
use Mp\Controller\AddonsController;

/**
 * 微捐赠后台管理控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class WebController extends AddonsController {

	/**
	 * 捐赠管理
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function donations() {
		$this->setModel('idou_donate_list')
			 ->setListMap(array('mpid'=>get_mpid(),'openid'=>array('neq', ''), 'pay_status'=>1))
			 ->setListOrder('create_time desc')
			 ->addListItem('openid', '捐赠者头像', 'function', array('function_name'=>'get_fans_headimg'))
			 ->addListItem('openid', '捐赠者昵称', 'function', array('function_name'=>'get_fans_nickname'))
			 ->addListItem('money', '捐赠金额')
			 ->addListItem('content', '留言内容')
			 ->addListItem('create_time', '捐赠时间', 'function', array('function_name'=>'date','params'=>'Y-m-d H:i:s,###'))
			 ->common_lists();
	}

}

?>