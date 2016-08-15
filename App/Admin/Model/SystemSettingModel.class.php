<?php 

namespace Admin\Model;
use Think\Model;

/**
 * 系统设置模型
 * @author 艾逗笔<765532665@qq.com>
 */
class SystemSettingModel extends Model {

	/**
	 * 获取设置
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_setting_info($name) {
		if (!$name) {
			return false;
		}
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
	public function get_settings($type = '') {
		if (!empty($type)) {
			$results = $this->where(array('type'=>$type))->select();
		} else {
			$results = $this->select();
		}
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
	public function add_settings($settings, $type) {
		foreach ($settings as $k => $v) {
			if ($k == '__hash__') {
				continue;
			}
			$setting_list[]['name'] = $k;
			$setting = $this->get_setting_info($k);
			if ($setting) {
				$data['id'] = $setting['id'];
			}
			$data['name'] = $k;
			$data['value'] = $v;
			$data['type'] = $type;
			if (!$this->create($data)) {
				$this->error($this->getError());
			} else {
				if ($data['id']) {
					$this->save($data);
				} else {
					$this->add($data);
				}
			}
		}
		return true;
	}
}

?>