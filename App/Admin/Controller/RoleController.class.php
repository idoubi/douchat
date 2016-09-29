<?php 

namespace Admin\Controller;
use Admin\Controller\BaseController;

/**
 * RBAC角色管理控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class RoleController extends BaseController {

	/**
	 * 角色列表
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function lists() {
		$options = array(
			'mp_access' => array(
				'功能授权',
				U('mp_access',array('role_id'=>'{id}')),
				'btn btn-success btn-sm icon-ul',
				''
			),
			'edit' => array(
				'编辑',
				U('edit',array('id'=>'{id}')),
				'btn btn-primary btn-sm icon-edit',
				''
			),
			'delete' => array(
				'删除',
				U('delete',array('id'=>'{id}')),
				'btn btn-danger btn-sm icon-delete',
				''
			)
		);
		$this->addCrumb('系统管理', U('Index/index'), '')
			 ->addCrumb('角色管理', U('Role/lists'), '')
			 ->addCrumb('角色列表', '', 'active')
			 ->addNav('角色列表', '', 'active')
			 ->addButton('添加角色', U('add'), 'btn btn-primary')
			 ->setModel('rbac_role')
			 ->addListItem('name', '角色名称')
			 ->addListItem('remark', '角色描述')
			 ->addListItem('type', '角色类型', 'enum', array('options'=>array('system_manager'=>'系统管理员','admin_manager'=>'后台管理员','mp_manager'=>'公众号管理员')))
			 ->addListItem('status', '状态', 'enum', array('options'=>array(0=>'禁用',1=>'正常')))
			 ->addListItem('id', '可创建公众号数量', 'callback', array('callback_name'=>'get_role_mp_count'))
			 ->addListItem('id', '操作', 'custom', array('options'=>$options))
			 ->common_lists();
	}

	/**
	 * 添加角色
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function add() {
		$this->addCrumb('系统管理', U('Index/index'), '')
			 ->addCrumb('角色管理', U('Role/lists'), '')
			 ->addCrumb('添加角色', '', 'active')
			 ->addNav('添加角色', '', 'active')
			 ->setModel('rbac_role')
			 ->addFormField('name', '角色名称', 'text', array('placeholder'=>'管理员'))
			 ->addFormField('remark', '角色描述', 'textarea')
			 ->addFormField('type', '角色类型', 'radio', array('options'=>array('system_manager'=>'系统管理员','admin_manager'=>'后台管理员','mp_manager'=>'公众号管理员'),'value'=>'mp_manager','tip'=>'在没有设置角色访问授权的前提下，角色类型起作用。系统管理员可以进入系统后台和公众号管理后台，后台管理员仅能进入系统后台，公众号管理员仅能进入公众号管理后台'))
			 ->addFormField('status', '状态', 'radio', array('options'=>array(0=>'禁用',1=>'正常'),'value'=>1))
			 ->addValidate('name', 'require', '角色名称不能为空', 1, 'regex', 3)
			 ->addAuto('pid', 0)
			 ->common_add();
	}

	/**
	 * 编辑角色
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function edit() {
		$this->addCrumb('系统管理', U('Index/index'), '')
			 ->addCrumb('角色管理', U('Role/lists'), '')
			 ->addCrumb('编辑角色', '', 'active')
			 ->addNav('编辑角色', '', 'active')
			 ->setModel('rbac_role')
			 ->addFormField('name', '角色名称', 'text', array('placeholder'=>'管理员'))
			 ->addFormField('remark', '角色描述', 'textarea')
			 ->addFormField('type', '角色类型', 'radio', array('options'=>array('system_manager'=>'系统管理员','admin_manager'=>'后台管理员','mp_manager'=>'公众号管理员'),'tip'=>'在没有设置角色访问授权的前提下，角色类型起作用。系统管理员可以进入系统后台和公众号管理后台，后台管理员仅能进入系统后台，公众号管理员仅能进入公众号管理后台'))
			 ->addFormField('status', '状态', 'radio', array('options'=>array(0=>'禁用',1=>'正常')))
			 ->addValidate('name', 'require', '角色名称不能为空', 1, 'regex', 3)
			 ->setFormData(M('rbac_role')->find(I('id')))
			 ->setEditMap(array('id'=>I('get.id')))
			 ->common_edit();
	}

	/**
	 * 删除角色
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function delete() {
		$role_id = I('id');
		M('rbac_mp_access')->where(array('role_id'=>$role_id))->delete();
		M('rbac_access')->where(array('role_id'=>$role_id))->delete();
		$res = M('rbac_role')->delete($role_id);
		if (!$res) {
			$this->error('删除角色失败');
		} else {
			$this->success('删除角色成功', U('lists'));
		}
	}

	/**
	 * 访问授权管理
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function access() {
		if (IS_POST) {
			$nodes = I('nodes');
			foreach ($nodes as $k => $v) {
				$tmp = explode('_', $v);
				$data['node_id'] = $tmp[0];
				$data['level'] = $tmp[1];
				$data['role_id'] = I('role_id');
				$datas[] = $data;
			}
			$Access = M('rbac_access');
			if (!$Access->autoCheckToken($_POST)) {
				$this->error('表单令牌错误');
			} else {
				$Access->where(array('role_id'=>I('role_id')))->delete();
				if ($nodes) {
					$res = $Access->addAll($datas);
					if ($res === false) {
						$this->error('编辑访问授权出现错误');
					}
				}
				$this->success('编辑访问授权成功', U('lists'));
			}
		} else {
			$results = M('rbac_node')->order('sort desc')->select();
			$access = M('rbac_access')->field('node_id')->where(array('role_id'=>I('role_id')))->select();
			foreach ($access as $k => $v) {
				$access_nodes[] = $v['node_id'];
			}
			$nodes = $this->parse_node($results, $access_nodes);
			$this->addCrumb('系统管理', U('Index/index'), '')
				 ->addCrumb('角色管理', U('Role/lists'), '')
				 ->addCrumb('访问授权', '', 'active')
				 ->addNav('访问授权', '', 'active')
				 ->assign('nodes', $nodes)
				 ->assign('role_id', I('role_id'))
				 ->display();
		}	
	}

	/**
	 * 功能授权
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function mp_access() {
		if (IS_POST) {
			$data['role_id'] = I('role_id');
			$data['mp_count'] = I('mp_count');
			$data['register_invite_count'] = I('register_invite_count');
			$data['mp_groups'] = json_encode(I('mp_groups'));
			$data['addons'] = json_encode(I('addons'));
			M('rbac_mp_access')->where(array('role_id'=>I('role_id')))->delete();
			$res = M('rbac_mp_access')->add($data);
			if (!$res) {
				$this->error('编辑功能授权失败');
			} else {
				$this->success('编辑功能授权成功', U('lists'));
			}
		} else {
			$this->addCrumb('系统管理', U('Index/index'), '')
				 ->addCrumb('角色管理', U('Role/lists'), '')
				 ->addCrumb('功能授权', '', 'active')
				 ->addNav('功能授权', '', 'active')
				 ->setModel('rbac_mp_access')
				 ->addFormField('role_id', '角色ID', 'hidden', array('value'=>I('role_id')))
				 ->addFormField('mp_count', '可创建公众号数量', 'number', array('placeholder'=>'填0表示不限制，填负数或者不填表示不能创建'))
				 ->addFormField('register_invite_count', '可邀请注册数', 'number', array('placeholder'=>'填0表示不限制，填负数或者不填表示不能创建','tip'=>'在开启邀请注册功能时，该角色的用户能创建的邀请码数量'))
				 ->addFormField('mp_groups', '可使用的功能套餐', 'checkbox', array('options'=>'callback','callback_name'=>'get_mp_group'))
				 ->addFormField('addons', '附加插件权限', 'checkbox', array('options'=>'callback','callback_name'=>'get_installed_addons','tip'=>'该角色能使用的插件为此项选择的插件与所选功能套餐包含的插件总和'))
				 ->setFormData(M('rbac_mp_access')->where(array('role_id'=>I('role_id')))->find())
				 ->common_edit();
		}
	}

	/**
	 * 解析节点数组
	 * @author 艾逗笔<765532665@qq.com>
	 */
	private function parse_node($nodes, $access_nodes, $pid = 0) {
		$arr = array();
		foreach ($nodes as $k => $v) {
			if (in_array($v['id'], $access_nodes)) {
				$v['checked'] = 1;
			}
			if ($v['pid'] == $pid) {
				$v['children'] = $this->parse_node($nodes, $access_nodes, $v['id']);
				$arr[] = $v;
			}
		}
		return $arr;
	}

	/**
	 * 获取公众号套餐
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_mp_group() {
		$lists = M('mp_group')->select();
		foreach ($lists as $k => $v) {
			$options[$v['id']] = $v['name']; 
		}
		return $options;
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
	 * 获取角色可创建公众号的数量
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_role_mp_count($role_id) {
		$mp_count = M('rbac_mp_access')->where(array('role_id'=>$role_id))->getField('mp_count');
		return $mp_count == 0 ? '不限' : $mp_count;
	}
}

?>