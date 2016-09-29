<?php 

namespace Admin\Controller;
use Admin\Controller\BaseController;

/**
 * 全局设置控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class SettingController extends BaseController {

	/**
	 * 站点信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function siteinfo() {
		if (IS_POST) {
			$this->save_settings('siteinfo');
		} else {
			$this->setMetaTitle('站点信息-全局设置')
				 ->addCrumb('系统管理', U('Index/index'), '')
				 ->addCrumb('全局设置', U('Setting/siteinfo'), '')
				 ->addCrumb('站点信息', '', 'active')
				 ->addNav('站点信息', '', 'active')
				 ->addNav('注册访问', U('Setting/register'), '')
				 ->addFormField('site_name', '站点标题', 'text')
				 ->addFormField('site_intro', '站点简介', 'textarea')
				 ->addFormField('site_keywords', '站点关键词', 'textarea')
				 ->addFormField('site_copyright', '版权信息', 'text')
				 ->addFormField('site_icp_beian', '备案信息', 'text')
				 ->setFormData(D('SystemSetting')->get_settings('siteinfo'))
				 ->common_edit();
		}
	}

	/**
	 * 注册访问
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function register() {
		if (IS_POST) {
			$this->save_settings('register');
		} else {
			$this->addCrumb('系统管理', U('Index/index'), '')
				 ->addCrumb('全局设置', U('Setting/siteinfo'), '')
				 ->addCrumb('注册访问', '', 'active')
				 ->addNav('站点信息', U('Setting/siteinfo'), '')
				 ->addNav('注册访问', '', 'active')
				 ->addFormField('register_on', '是否开放注册', 'radio', array('options'=>array(0=>'不开放',1=>'开放'),'value'=>0))
				 ->addFormField('register_default_role_id', '用户注册成功后默认所属的角色', 'select', array('options'=>'callback','callback_name'=>'get_role_list'))
				 ->setFormData(D('system_setting')->get_settings('register'))
				 ->common_edit();
		}
	}

	/**
	 * 获取角色列表
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_role_list() {
		$lists = M('rbac_role')->where(array('status'=>1))->select();
		$roles[] = '请选择角色';
		foreach ($lists as $k => $v) {
			$roles[$v['id']] = $v['name'];
		}
		return $roles;
	}

	/**
	 * 保存配置
	 * @author 艾逗笔<765532665@qq.com>
	 */
	private function save_settings($type) {
		C('TOKEN_ON', false);
		$SystemSetting = D('SystemSetting');
		$settings = I('post.');
		if (!$SystemSetting->add_settings($settings, $type)) {
			$this->error('保存设置失败');
		} else {
			$this->success('保存设置成功');
		}
	}
}


?>