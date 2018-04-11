<?php
/**
 * 拼车后台管理控制器
 * @author 艾逗笔<http://idoubi.cc>
 */

namespace Addons\Pinche\Controller;
use Mp\Controller\AddonsController;

class WebController extends AddonsController {
	
	public function __construct() {
		parent::__construct();
		
		$this->nav = [
//			[
//				'title' => '模块设置',
//				'url' => create_addon_url('setting'),
//				'class' => in_array($this->act, ['setting']) ? 'active' : ''
//			],
			[
				'title' => '拼车信息',
				'url' => create_addon_url('infoList'),
				'class' => in_array($this->action, ['infolist']) ? 'active' : ''
			],
			[
				'title' => '预约管理',
				'url' => create_addon_url('appointmentList'),
				'class' => in_array($this->action, ['appointmentlist', 'appointmentedit']) ? 'active' : ''
			]
		];
		$this->subnav = [];
	}
    
	// 拼车信息
	public function infoList() {
		$type = I('type', 1, 'intval');
		$this->setMetaTitle('拼车信息')
			->setSubNav([
				[
					'title' => '车找人',
					'url' => create_addon_url('infoList', ['type'=>1]),
					'class' => $type ==  1 ? 'active' : ''
				],
				[
					'title' => '人找车',
					'url' => create_addon_url('infoList', ['type'=>2]),
					'class' => $type == 2 ? 'active' : ''
				]
			])
			->setModel('pinche_info')
			->setListMap(['mpid'=>$this->mpid, 'type'=>$type])
			->addListItem('time', '出发时间', 'date')
			->addListItem('departure', '出发地')
			->addListItem('destination', '目的地')
			->addListItem('id', '操作', 'custom', ['options'=>[
				[
					'title' => '查看详情',
					'url' => create_addon_url('infoDetail', ['id'=>'{id}']),
					'class' => 'btn btn-sm btn-success'
				],
				[
					'title' => '修改',
					'url' => create_addon_url('infoEdit', ['id'=>'{id}']),
					'class' => 'btn btn-sm btn-info'
				],
				[
					'title' => '删除',
					'url' => create_addon_url('infoDelete', ['id'=>'{id}']),
					'class' => 'btn btn-sm btn-danger',
					'attr' => 'onclick="return confirm(\'确认删除？\')"'
				]
			]])
			->common_lists();
	}
	
	// 修改拼车信息
	public function infoEdit() {
		$this->setMetaTitle('修改拼车信息')
			->setSubNav([
				[
					'title' => '返回拼车信息',
					'url' => create_addon_url('infoList'),
					'class' => ''
				],
				[
					'title' => '修改拼车信息',
					'url' => '',
					'class' => 'active'
				]
			])
			->setModel('pinche_info')
			->setFindMap(['mpid'=>$this->mpid,'id'=>I('get.id')])
			->setEditMap(['mpid'=>$this->mpid,'id'=>I('get.id')])
			->setEditSuccessUrl(create_addon_url('infoList'))
			->addFormField('time', '出发时间', 'time')
			->addFormField('departure', '出发地点', 'text')
			->addFormField('destination', '目的地', 'text')
			->addFormField('surplus', '座位数', 'number')
			->addFormField('price', '费用', 'number')
			->addFormField('name', '联系人姓名', 'text')
			->addFormField('gender', '联系人性别', 'radio', ['options'=>[1=>'男',2=>'女']])
			->common_edit();
	}
	
	// 删除拼车信息
	public function infoDelete() {
		$this->setModel('pinche_info')
			->setDeleteMap(['mpid'=>$this->mpid, 'id'=>I('get.id')])
			->setDeleteSuccessUrl(create_addon_url('infoList'))
			->common_delete();
	}
	
	// 拼车详情
	public function infoDetail() {
		$this->setMetaTitle('拼车详情')
			->setSubNav([
				[
					'title' => '返回拼车列表',
					'url' => create_addon_url('infoList'),
					'class' => ''
				],
				[
					'title' => '拼车详情',
					'url' => '',
					'class' => 'active'
				]
			])
			->common_lists();
	}
	
	// 预约管理
	public function appointmentList() {
		$this->setMetaTitle('预约管理')
			->setModel('pinche_appointment')
			->setListMap(['mpid'=>$this->mpid])
			->addListItem('name', '预约人姓名')
			->addListItem('phone', '预约人手机号')
			->addListItem('surplus', '预约座位数')
			->addListItem('status', '状态')
			->addListItem('time', '创建时间', 'datetime')
			->addListItem('id', '操作', 'custom', ['options'=>[
				[
					'title' => '查看拼车信息',
					'url' => create_addon_url('infoDetail', ['id'=>'{iid}']),
					'class' => 'btn btn-sm btn-success'
				],
				[
					'title' => '编辑',
					'url' => create_addon_url('appointmentEdit', ['id'=>'{id}']),
					'class' => 'btn btn-sm btn-info'
				],
				[
					'title' => '删除',
					'url' => create_addon_url('appointmentDelete', ['id'=>'{id}']),
					'class' => 'btn btn-sm btn-danger',
					'attr' => 'onclick="return confirm(\'确认删除？\')"'
				]
			]])
			->common_lists();
	}
	
	// 编辑预约
	public function appointmentEdit() {
		$this->setMetaTitle('编辑预约')
			->setSubNav([
				[
					'title' => '返回预约管理',
					'url' => create_addon_url('appointmentList'),
					'class' => ''
				],
				[
					'title' => '编辑预约',
					'url' => '',
					'class' => 'active'
				]
			])
			->setModel('pinche_appointment')
			->addFormField('name', '预约人姓名', 'text')
			->addFormField('phone', '预约人手机号', 'text')
			->addFormField('surplus', '预约座位数', 'number')
			->setFindMap(['mpid'=>$this->mpid, 'id'=>I('get.id')])
			->setEditMap(['mpid'=>$this->mpid, 'id'=>I('get.id')])
			->setEditSuccessUrl(create_addon_url('appointmentList'))
			->common_edit();
	}
	
	// 删除预约
	public function appointmentDelete() {
		$this->setModel('pinche_appointment')
			->setDeleteMap(['mpid'=>$this->mpid, 'id'=>I('get.id')])
			->setDeleteSuccessUrl(create_addon_url('appointmentList'))
			->common_delete();
	}
	
	
}

?>