<?php 

namespace Mp\Model;
use Think\Model;

class AddonsModel extends Model {

	/**
	 * 获取用户权限范围内的插件
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_access_addons() {
		$user_id = get_user_id();				// 当前管理用户
		$user_access = D('User/User')->get_user_access($user_id);
		$installed_addons = $this->get_installed_addons();
		foreach ($installed_addons as $k => $v) {
			if (!in_array($v['bzname'], $user_access['addons'])) {
				continue;
			}
			$arr['title'] = $v['name'];
			$arr['bzname'] = $v['bzname'];
			preg_match('/.*index.php/', $v['index_url'], $m);
			$arr['url'] = str_replace($m[0], SITE_URL.'index.php', $v['index_url']);
			$arr['class'] = '';
			
			$addon_info = $this->get_addon_info($v['bzname']);
			$arr['config'] = $addon_info['config'];
			$access_addons[] = $arr;
		}
		return $access_addons;
	}

	/**
	 * 判断插件是否禁用
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function is_addon_forbidden($addon, $mpid) {
		$status = M('addons_access')->where(array('addon'=>$addon,'mpid'=>$mpid))->getField('status');
		if ($status == 2) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 获取已安装的插件
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_installed_addons($type = '') {
		$map['status'] = 1;
		if ($type) {
			$map['type'] = $type;
		}
		$addons = M('addons')->where($map)->field('id,name,bzname,type')->select();
		foreach ($addons as $k => &$v) {
			$addon_dir_info = $this->get_addon_dir_info($v['bzname']);
			$v['last_version'] = $addon_dir_info['version'];
			if ($addon_dir_info['config']['index_url']) {
				$v['index_url'] = $addon_dir_info['config']['index_url'];
			} elseif ($addon_dir_info['config']['respond_rule']) {
				// $v['index_url'] = U('Mp/Web/rule', array('addon'=>$v['bzname']));
				$v['index_url'] = U('/addon/'.$v['bzname'].'/rule');
			} elseif ($addon_dir_info['config']['setting']) {
				// $v['index_url'] = U('Mp/Web/setting', array('addon'=>$v['bzname']));
				$v['index_url'] = U('/addon/'.$v['bzname'].'/setting');
			} else {
				// $v['index_url'] = U('Mp/Web/index', array('addon'=>$v['bzname']));
				$v['index_url'] = U('/addon/'.$v['bzname'].'/index');
			}
			
			if (!$v['last_version']) {
				unset($addons[$k]);
			}
		}
		if (!$addons) {
			return false;
		}
		return $addons;
	}

	/**
	 * 根据插件标识名获取插件信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_addon_info_by_bzname($bzname) {
		if (!$bzname) {
			return false;
		}
		$map['bzname'] = $bzname;
		$addon_info = M('addons')->where($map)->find();
		if (!$addon_info) {
			return false;
		}
		return $addon_info;
	} 

	/**
	 * 获取插件信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_addon_info($addon='', $type='file') {
		if (empty($addon)) {
			$addon = get_addon();
		}
		if ($type == 'file') {				// 取插件信息文件里面的插件信息
			$info_path = ADDON_PATH . $addon . DIRECTORY_SEPARATOR . 'info.php';
			if (!is_file($info_path)) {
				return false;
			}
			$addon_info = include $info_path;
			if ($addon_info['bzname'] != $addon) {
				return false;
			}
			if (!$addon_info['name'] || !$addon_info['version'] || !$addon_info['author']) {
				return false;
			}
			return $addon_info;
		} else {							// 取数据库addons表的插件信息
			$map['bzname'] = $addon;
			$addon_info = $this->where($map)->find();
			if (!$addon_info) {
				return false;
			}
			$addon_info['config'] = json_decode($addon_info['config'], true);
			return $addon_info;
		}
	}

	/**
	 * 获取插件配置信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_addon_config($addon='') {
		if (empty($addon)) {
			$addon = get_addon();
		}
		$addon_info = $this->get_addon_info($addon);
		if (!$addon_info || empty($addon_info['config'])) {
			return false;
		}
		return $addon_info['config'];
	}

	/**
	 * 获取插件模型信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_addon_model($model) {
		if (empty($model)) {
			return false;
		}
		$addon_info = $this->get_addon_info();
		if (empty($addon_info['model'][$model])) {
			return false;
		}
		return $addon_info['model'][$model];
	}

	/**
	 * 获取业务导航信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_addon_menu($act, $addon = '') {
		if ($addon == '') {
			$addon = get_addon();
		}
		if (!$act || !$addon) {
			return false;
		}
		$info_path = ADDON_PATH . $addon . DIRECTORY_SEPARATOR . 'info.php';
		if (is_file($info_path)) {
			$addon_info = include $info_path;
			$menu_list = $addon_info['config']['menu_list'];
		} else {
			$addon_info = $this->get_addon_info_by_bzname($addon);
			$addon_config = $this->get_addon_config($addon);
			$addon_config = json_decode($addon_config, true);
			$menu_list = $addon_config['menu_list'];
		}
		foreach ($menu_list as $k => $v) {
			if ($k == $act) {
				$menu['act'] = $k;
				$menu['title'] = $v;
				break;
			}
		}
		return $menu;		
	}

	/**
	 * 更新插件配置信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function save_addon_config($config, $addon) {
		if (!$addon) {
			return false;
		}
		$map['bzname'] = $addon;
		$data['config'] = $config;
		M('addons')->where($map)->save($data);
		return true;
	}

	/**
	 * 获取插件文件夹信息
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_addon_dir_info($bzname) {
		if (!$bzname) {
			return false;
		}
		$info_path = ADDON_PATH . $bzname . DIRECTORY_SEPARATOR . 'info.php';
		if (!is_file($info_path)) {
			return false;
		}
		$addon_info = include $info_path;
		if ($addon_info['bzname'] != $bzname) {
			return false;
		}
		if (!$addon_info['name'] || !$addon_info['version'] || !$addon_info['author']) {
			return false;
		}
		return $addon_info;
	}

}