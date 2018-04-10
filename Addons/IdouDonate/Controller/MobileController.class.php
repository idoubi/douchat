<?php 

namespace Addons\IdouDonate\Controller;
use Mp\Controller\MobileBaseController;

/**
 * 微捐赠移动端控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class MobileController extends MobileBaseController {

	/**
	 * 捐赠首页
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function index() {
		$settings = get_addon_settings();
		if ($settings['money']) {
			if (strpos($settings['money'], ',')) {
				$money = explode(',', $settings['money']);
			} else {
				$money = explode('，', $settings['money']);
			}
		}
		$this->assign('money', $money);
		$this->display();
	}

	/**
	 * 预捐赠
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function pre_donate() {
		$data['mpid'] = get_mpid();
		$data['openid'] = get_openid();
		$data['money'] = floatval(I('price'));
		$data['is_anonymous'] = intval(I('is_anonymous'));
		$data['pay_status'] = 0;
		$data['create_time'] = time();
		$data['content'] = I('content');
		$data['is_show'] = 0;
		$data['orderid'] = $data['mpid'] . time();
		$res = M('idou_donate_list')->add($data);
		if (!$res) {
			$data['errcode'] = 0;
			$data['errmsg'] = '捐赠失败';
		} else {
			$data['errcode'] = 1;
			$data['errmsg'] = '捐赠成功';
			$data['notify'] = create_addon_url('pay_ok');
			$data['jump_url'] = create_addon_url('pay_ok');
		}
		$this->ajaxReturn($data);
	}

	/**
	 * 支付成功
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function pay_ok() {
		if (I('result_code') == 'SUCCESS' && I('return_code') == 'SUCCESS') {
			$map['orderid'] = I('out_trade_no');
			$data['pay_status'] = 1;
			$data['is_show'] = 1;
			M('idou_donate_list')->where($map)->save($data);
		}
		$this->display();
	}

	/**
	 * 捐赠列表
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function donate_list() {
		$map['mpid'] = get_mpid();
		$map['pay_status'] = 1;
		$map['is_show'] = 1;
		$lists = M('idou_donate_list')->where($map)->order('create_time desc')->select();
		foreach ($lists as $k => &$v) {
			$v['fans'] = get_fans_info($v['openid']);
		}
		$this->assign('lists', $lists);
		$this->display();
	}

}

?>