<?php 

namespace Mp\Model;
use Think\Model;

/**
 * 插件功能入口模型
 * @author 艾逗笔<765532665@qq.com>
 */
class AddonEntryModel extends Model {

	/**
	 * 自动验证
	 * @author 艾逗笔<765532665@qq.com>
	 */
	protected $_validate = array(
		array('title', 'require', '入口标题不能为空'),
		array('name', 'require', '入口名称不能为空'),
		array('act', 'require', '入口操作不能为空')
	);

	/**
	 * 自动完成
	 * @author 艾逗笔<765532665@qq.com>
	 */
	protected $_auto = array(
		array('mpid', 'get_mpid', 3, 'function'),
		array('addon', 'get_addon', 3, 'function')
	);

	/**
	 * 检测入口是否存在
	 * @author 艾逗笔<765532665@qq.com>
	 */
	protected function is_entry_exists($act) {
		if ($this->get_addon_entry($act)) {
			return true;
		}
		return false;
	}

	/**
	 * 获取功能入口
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_addon_entry($act, $addon = '', $mpid = '') {
		if ($addon == '') {
			$addon = get_addon();
		}
		if ($mpid == '') {
			$mpid = get_mpid();
		}
		if (!$act || !$addon || !$mpid) {
			return false;
		}
		$addon_info_file = ADDON_PATH . get_addon() . '/info.php';
		if (is_file($addon_info_file)) {	// 如果插件信息文件存在
			$addon_info = include $addon_info_file;
		}
		if ($addon_info['config']) {
			$addon_config = $addon_info['config'];
		} else {
			$addon_config = D('Addons')->get_addon_config($addon);
			$addon_config = json_decode($addon_config, true);
		}
		if (!$addon_config || !$addon_config['entry'] || !$addon_config['entry_list']) {
			return false;
		}
		$entry_list = $addon_config['entry_list'];
		foreach ($entry_list as $k => $v) {
			if ($k == $act) {
				$map['mpid'] = $mpid;
				$map['addon'] = $addon;
				$map['act'] = $act;
				$entry = M('addon_entry')->where($map)->find();

				$entry['rule'] = D('MpRule')->get_entry_rule($entry['id']);
				$entry['act'] = $act;
				$entry['name'] = $v;
				$entry['url'] = U('/addon/'.$addon.'/mobile/'.$k.'/mpid/'.$mpid.'@'.C('HTTP_HOST'));
				break;
			}
		}
		return $entry;
	}

	/**
	 * 获取入口信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_entry_info($entry_id) {
		if (!$entry_id) {
			return false;
		}
		$map['id'] = intval($entry_id);
		$entry_info = M('addon_entry')->where($map)->find();
		if (!$entry_info['mpid'] || !$entry_info['addon'] || !$entry_info['act']) {
			return false;
		}
		return $entry_info;
	}
}

?>