<?php 

namespace Admin\Controller;
use Admin\Controller\BaseController;

/**
 * 公众号控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class MpController extends BaseController {

	/**
	 * 公众号列表
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function lists() {
		$this->addCrumb('系统管理', U('Index/index'), '')
			 ->addCrumb('公众号管理', U('Mp/lists'), '')
			 ->addCrumb('公众号列表', '', 'active')
			 ->addNav('公众号列表', '', 'active')
			 ->setModel('mp')
			 ->addListItem('name', '公众号名称')
			 ->addListItem('type', '公众号类型', 'enum', array('options'=>array(1=>'普通订阅号',2=>'认证订阅号',3=>'普通服务号',4=>'认证服务号',5=>'测试号')))
			 ->addListItem('status', '公众号状态', 'enum', array('options'=>array(0=>'禁用',1=>'正常',2=>'审核中')))
			 ->addListItem('user_id', '主管理员', 'callback', array('callback_name'=>'get_user_name'))
			 ->addListItem('create_time', '创建时间', 'function', array('function_name'=>'date','params'=>'Y-m-d H:i:s,###'))
			 ->addListItem('id', '操作', 'custom', array('options'=>array('edit'=>array('编辑',U('edit',array('id'=>'{id}')),'btn btn-primary btn-sm icon-edit',''))))
			 ->common_lists();
	}

	/**
	 * 编辑公众号
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function edit() {
		$validate = array(
			array('name', 'require', '公众号名称不能为空'),
			array('type', 'require', '请选择公众号类型'),
			array('origin_id', 'require', '公众号原始ID不能为空'),
			array('origin_id', '/^gh_[0-9|a-z]{12}$/', '公众号原始ID格式错误')
		);
		$this->addCrumb('系统管理', U('Index/index'), '')
			 ->addCrumb('公众号管理', U('Mp/lists'), '')
			 ->addCrumb('编辑公众号', '', 'active')
			 ->addNav('编辑公众号', '', 'active')
			 ->setModel('mp')
			 ->addFormField('name', '公众号名称', 'text')
			 ->addFormField('type', '公众号类型', 'radio', array('options'=>array(1=>'普通订阅号',2=>'认证订阅号',3=>'普通服务号',4=>'认证服务号',5=>'测试号')))
			 ->addFormField('status', '公众号状态', 'radio', array('options'=>array(0=>'禁用',1=>'正常',2=>'审核中')))
			 ->addFormField('origin_id', '公众号原始ID', 'text')
			 ->addFormField('appid', 'APPID', 'text')
			 ->addFormField('appsecret', 'APPSECRET', 'text')
			 ->setValidate($validate)
			 ->setFormData(M('mp')->find(I('get.id')))
			 ->setEditMap(array('id'=>I('get.id')))
			 ->common_edit();
	}

	/**
	 * 获取用户昵称
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_user_name($user_id) {
		$user_info = D('User')->get_user_info($user_id);
		return $user_info['nickname'];
	}

	/**
	 * 获取功能套餐列表
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_mp_groups() {
		$lists = D('MpGroup')->get_group_lists();
		foreach ($lists as $k => $v) {
			$groups[$v['id']] = $v['name'];
		}
		return $groups;
	}

	/**
	 * 解析套餐名
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function parse_groups($groups) {
		$groups_arr = json_decode($groups, true);
		$groups_name = '';
		foreach ($groups_arr as $k => $v) {
			$group_info = D('MpGroup')->get_group_info($v);
			if ($group_info['name']) {
				$groups_name .= ',' . $group_info['name'];
			}	
		}
		return substr($groups_name, 1);
	}
	
}

?>