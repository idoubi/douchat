<?php 

namespace Mp\Model;
use Think\Model;

/**
 * 公众号设置模型
 * @author 艾逗笔<765532665@qq.com>
 */
class MpSettingModel extends Model {

	/**
	 * 获取设置
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_setting_info($name) {
		if (!$name) {
			return false;
		}
		$map['mpid'] = get_mpid();
		$map['name'] = $name;
		$setting = $this->where($map)->find();
		if (!$setting) {
			return false;
		}
		return $setting;
	}

	/**
	 * 获取所有的设置项
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_settings() {
		$map['mpid'] = get_mpid();
		$results = $this->where($map)->select();
		if (!$results) {
			return false;
		}
		foreach ($results as $k => $v) {
			$settings[$v['name']] = $v['value'];
		}
		if (!$settings) {
			return false;
		}
		return $settings;
	}

	/**
	 * 添加设置项
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function add_settings($settings) {
		foreach ($settings as $k => $v) {
			$setting = $this->get_setting_info($k);
			if ($setting) {
				$this->where(array('id'=>$setting['id']))->setField('value', $v);
			} else {
				$data['mpid'] = get_mpid();
				$data['name'] = $k;
				$data['value'] = $v;
				$this->add($data);
			}
		}
		return true;
	}
}

?>