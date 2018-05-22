<?php

/**
 * 模板消息管理控制器
 * @author 艾逗笔<http://idoubi.cc>
 */
namespace Mp\Controller;
use Mp\Controller\BaseController;

class TempmsgController extends BaseController {
	
	// 数据列表
	public function lists() {
		$type = I('get.type', 1);
		if ($type == 2) {
			$tip = '用户支付成功后收集到的数据';
		} else {
			$tip = '用户在小程序提交表单收集到的数据';
		}
		$this->setMetaTitle('待通知列表')
			->setModel('mp_tempmsg')
			->setTip($tip)
			->setListMap(['mpid'=>$this->mpid,'type'=>$type])
			->setCrumb([
				[
					'title' => '小程序管理'
				],
				[
					'title' => '待通知列表',
					'class' => 'active'
				]
			])
			->setNav([
				[
					'title' => '待通知列表',
					'class' => 'active'
				]
			])
			->setSubNav([
				[
					'title' => '表单通知',
					'url' => U('lists', ['type'=>1]),
					'class' => $type == 1 ? 'active' : ''
 				],
				[
					'title' => '支付通知',
					'url' => U('lists', ['type'=>2]),
					'class' => $type == 2 ? 'active' : ''
				]
			])
			->setListOrder('created_at desc')
			->addListItem('id', 'ID')
			->addListItem('openid', '粉丝昵称', 'function', ['function_name'=>'get_fans_nickname'])
			->addListItem('openid', '粉丝头像', 'function', ['function_name'=>'get_fans_headimg'])
			->addListItem('formid', $type == 2 ? 'prepay_id' : 'formid')
			->addListItem('created_at', $type == 2 ? '支付时间' : '收集时间', 'datetime')
			->addListItem('status', '状态')
			->common_lists();
	}
}