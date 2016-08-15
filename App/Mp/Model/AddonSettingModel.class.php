<?php 

namespace Mp\Model;
use Think\Model;

/**
 * 插件配置参数模型
 * @author 艾逗笔<765532665@qq.com>
 */
class AddonSettingModel extends Model {

	/**
	 * 自动验证
	 * @author 艾逗笔<765532665@qq.com>
	 */
	protected $_validate = array(
		array('name', 'require', '参数名不能为空')
	);

	/**
	 * 自动完成
	 * @author 艾逗笔<765532665@qq.com>
	 */
	protected $_auto = array(
		
	);

	/**
	 * 获取插件所有配置参数
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_addon_settings($addon = '', $mpid = '') {
		if ($addon == '') {
			$addon = get_addon();
		}
		if ($mpid == '') {
			$mpid = get_mpid();
		}
		if (!$addon || !$mpid) {
			return false;
		}

		$map['mpid'] = $mpid;
		$map['addon'] = $addon;
		$settings = M('addon_setting')->where($map)->select();
		if (!$settings) {
			return false;
		}
		foreach ($settings as $k => $v) {
			$addon_settings[$v['name']] = $v['value'];
		}
		return $addon_settings;
	}

	/**
	 * 根据参数名获取参数信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_addon_setting($name, $addon = '', $mpid = '') {
		if ($addon == '') {
			$addon = get_addon();
		}
		if ($mpid == '') {
			$mpid = get_mpid();
		}
		if (!$name || !$addon || !$mpid) {
			return false;
		}

		$map['name'] = $name;
		$map['mpid'] = $mpid;
		$map['addon'] = $addon;
		$setting = M('addon_setting')->where($map)->find();
		if (!$setting) {
			return false;
		}
		return $setting;
	}

	/**
	 * 获取配置参数值
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_setting_value($name, $addon = '', $mpid = '') {
		$setting = $this->get_addon_setting($name, $addon, $mpid);
		if (!$setting) {
			return false;
		}
		return $setting['value'];
	}
}

?>