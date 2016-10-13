<?php 

namespace Addons\IdouGuestbook\Controller;
use Mp\Controller\AddonsController;

/**
 * 留言板后台控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class WebController extends AddonsController {

	/**
	 * 留言管理
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function messages() {
		$model = get_addon_model('idou_guestbook_list');
		$this->setModel($model)
			 ->common_lists();
	}

	/**
	 * 通用删除数据方法
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function delete() {
		$this->setModel('idou_guestbook_list')
			 ->setDeleteMap(array('id'=>I('id'),'mpid'=>get_mpid()))
			 ->setDeleteSuccessUrl(create_addon_url('messages'))
			 ->common_delete();
	}

	/**
	 * 编辑留言
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function edit() {
		$this->addSubNav('编辑留言', '', 'active')
			 ->setModel('idou_guestbook_list')
			 ->addFormField('nickname', '留言者昵称', 'text')
			 ->addFormField('content', '留言内容', 'textarea')
			 ->addFormField('status', '留言状态', 'radio', array('options'=>array(0=>'未审核',1=>'审核通过',2=>'审核不通过')))
			 ->setFindMap(array('mpid'=>get_mpid(),'id'=>I('get.id')))
			 ->setEditMap(array('mpid'=>get_mpid(),'id'=>I('get.id')))
			 ->setEditSuccessUrl(create_addon_url('messages'))
			 ->common_edit();
	}
}

?>