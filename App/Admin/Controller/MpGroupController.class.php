<?php 

namespace Admin\Controller;
use Admin\Controller\BaseController;

/**
 * 公众号套餐控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class MpGroupController extends BaseController {

	/**
	 * 套餐列表
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function lists() {
		$options = array(
			'edit' => array('编辑', U('edit',array('id'=>'{id}')), 'btn btn-primary btn-sm icon-edit'),
			'delete' => array('删除', U('delete',array('id'=>'{id}')), 'btn btn-danger btn-sm icon-delete')
		);
		$this->addCrumb('系统管理', U('Index/index'), '')
			 ->addCrumb('公众号套餐', U('MpGroup/lists'), '')
			 ->addCrumb('套餐列表', '', 'active')
			 ->addNav('套餐列表', '', 'active')
			 ->addButton('添加公众号套餐', U('add'), 'btn btn-primary')
			 ->setModel('mp_group')
			 ->addListItem('name', '套餐名')
			 ->addListItem('addons', '插件权限', 'callback', array('callback_name'=>'parse_addons'))
			 ->addListItem('id', '操作', 'custom', array('options'=>$options))
			 ->common_lists();
	}

	/**
	 * 新增公众号套餐
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function add() {
		$this->addCrumb('系统管理', U('Index/index'), '')
			 ->addCrumb('公众号套餐', U('MpGroup/lists'), '')
			 ->addCrumb('新增套餐', '', 'active')
			 ->addNav('新增套餐', '', 'active')
			 ->setModel('mp_group')
			 ->addFormField('name', '套餐名', 'text')
			 ->addFormField('addons', '可使用的插件', 'checkbox', array('options'=>'callback','callback_name'=>'get_installed_addons'))
			 ->addValidate('name', 'require', '套餐名不能为空', 1, 'regex', 3)
			 ->addAuto('addons', 'json_encode', 3, 'function')
			 ->common_add();
	}

	/**
	 * 编辑公众号套餐
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function edit() {
		$this->addCrumb('系统管理', U('Index/index'), '')
			 ->addCrumb('公众号套餐', U('MpGroup/lists'), '')
			 ->addCrumb('编辑套餐', '', 'active')
			 ->addNav('编辑套餐', '', 'active')
			 ->setModel('mp_group')
			 ->addFormField('id', '套餐ID', 'hidden', array('value'=>I('get.id')))
			 ->addFormField('name', '套餐名', 'text')
			 ->addFormField('addons', '可使用的插件', 'checkbox', array('options'=>'callback','callback_name'=>'get_installed_addons'))
			 ->setFormData(M('mp_group')->find(I('get.id')))
			 ->setEditMap(array('id'=>I('id')))
			 ->addValidate('name', 'require', '套餐名不能为空', 1, 'regex', 3)
			 ->addAuto('addons', 'json_encode', 3, 'function')
			 ->common_edit();
	}

	/**
	 * 删除公众号套餐
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function delete() {
		$model = array(
			'name' => 'mp_group',
			'title' => '公众号套餐'
		);
		parent::delete($model);
	}

	/**
	 * 获取已安装的插件
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_installed_addons() {
		$installed_addons = D('Addons')->get_installed_addons();
		foreach ($installed_addons as $k => $v) {
			$addons[$v['bzname']] = $v['name'];
		}
		return $addons;
	}

	/**
	 * 解析插件
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function parse_addons($addons) {
		$addons_arr = json_decode($addons, true);
		$addons_name = '';
		foreach ($addons_arr as $k => $v) {
			$addon_info = D('Addons')->get_addon_info_by_bzname($v);
			if ($addon_info['name']) {
				$addons_name .= ',' . $addon_info['name'];
			}
		}
		return substr($addons_name, 1);
	}

	
}

?>