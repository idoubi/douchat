<?php

/**
 * 示例插件后台管理控制器
 * @author 艾逗笔
 */
namespace Addons\Demo\Controller;
use Mp\Controller\AddonsController;

class WebController extends AddonsController {
	
	// 日记列表
	public function diaryList() {
		$this->setMetaTitle('日记管理')
			->setModel('demo_diary')
			->setListMap(['mpid'=>get_mpid()])
			->setListOrder('status desc, created_at desc')
			->addListItem('title', '标题')
			->addListItem('content', '内容')
			->addListItem('openid', '用户头像', 'function', array('function_name'=>'get_fans_headimg'))
			->addListItem('openid', '用户昵称', 'function', array('function_name'=>'get_fans_nickname'))
			->addListItem('created_at', '发布时间', 'datetime')
			->addListItem('status', '状态', 'enum', ['options'=>[-1=>'已删除',1=>'正常']])
			->addListItem('id', '操作', 'custom', ['options'=>[
				[
					'title' => '编辑',
					'url' => create_addon_url('editDiary', ['id'=>'{id}']),
					'class' => 'btn btn-sm btn-info'
				],
				[
					'title' => '删除',
					'url' => create_addon_url('deleteDiary', ['id'=>'{id}']),
					'class' => 'btn btn-sm btn-danger',
					'attr' => 'onclick=return confirm(\"确认删除？\")'
				]
			]])
			->common_lists();
	}
	
	// 编辑日记
	public function editDiary() {
		$this->setMetaTitle('编辑日记')
			->setSubNav([
				[
					'title' => '返回日记列表',
					'url' => create_addon_url('diaryList'),
					'class' => ''
				],
				[
					'title' => '编辑日记',
					'url' => '',
					'class' => 'active'
				]
			])
			->setModel('demo_diary')
			->addFormField('title', '标题', 'text')
			->addFormField('content', '内容', 'textarea')
			->setFindMap(['mpid'=>get_mpid(), 'id'=>I('get.id')])
			->setEditMap(['mpid'=>get_mpid(), 'id'=>I('get.id')])
			->setEditSuccessUrl(create_addon_url('diaryList'))
			->common_edit();
	}
	
	// 删除日记
	public function deleteDiary() {
		$this->setModel('demo_diary')
			->setDeleteMap([
				'mpid' => get_mpid(),
				'id' => I('get.id')
			])
			->setDeleteSuccessUrl(create_addon_url('diaryList'))
			->common_delete();
	}
}

?>