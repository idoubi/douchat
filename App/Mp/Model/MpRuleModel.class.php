<?php 

namespace Mp\Model;
use Think\Model;

/**
 * 公众号响应规则模型
 * @author 艾逗笔<765532665@qq.com>
 */
class MpRuleModel extends Model {

	/**
	 * 自动验证
	 * @author 艾逗笔<765532665@qq.com>
	 */
	protected $_validate = array(
		array('keyword', 'require', '关键词不能为空')
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
	 * 判断关键词是否已经存在
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function is_keyword_exists($keyword) {
		$map['keyword'] = $keyword;
		//$map['mpid'] = get_mpid();
		$rule = $this->where($map)->find();
		if ($rule) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 根据关键词获取响应规则
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_keyword_rule($keyword,$type='',$mpid = '') {
		if (empty($mpid)) {
			$mpid = get_mpid();
		}
		if (!$mpid || !$keyword) {
			return false;
		}
		if (!empty($type)) {
			$map['type'] = $type;
		}
		$map['mpid'] = $mpid;
		if ($type == 'respond') {
			$map['_string'] = "FIND_IN_SET('$keyword', keyword) OR '$keyword' REGEXP keyword";
		} elseif ($type == 'entry') {
			$map['_string'] = "FIND_IN_SET('$keyword', keyword)";
		} else {
			$map['keyword'] = $keyword;
		}
		$rule = $this->where($map)->order('id desc')->find();
		if ($type == 'respond' && !$rule) {
			$Addons = D('Addons');
			$installed_addons = $Addons->get_installed_addons();
			foreach ($installed_addons as $k => $v) {
				$addon_info = $Addons->get_addon_info($v['bzname']);
				if ($addon_info && isset($addon_info['keywords'])) {
					if (in_array($keyword, $addon_info['keywords'])) {
						$rule['mpid'] = get_mpid();
						$rule['addon'] = $v['bzname'];
						$rule['keyword'] = $keyword;
						$rule['type'] = 'respond';
						return $rule;
					}
				}
			}
		}
		return $rule;
	}

	/**
	 * 获取入口响应规则
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_entry_rule($entry_id) {
		if (empty($entry_id)) {
			return false;
		}
		$map['entry_id'] = $entry_id;
		$rule = $this->where($map)->find();
		return $rule;
	}

	/**
	 * 获取关键词响应规则
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_respond_rule($addon = '', $mpid = '') {
		$addon || $addon = get_addon();
		$mpid || $mpid = get_mpid();
		if (!$addon || !$mpid) {
			return false;
		}
		$map['mpid'] = $mpid;
		$map['addon'] = $addon;
		$map['type'] = 'respond';
		$rule = $this->where($map)->find();
		return $rule;
	}

	/**
	 * 获取自动回复规则
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_auto_reply_rule($reply_id) {
		if (!$reply_id) {
			return false;
		}
		$map['reply_id'] = intval($reply_id);
		$rule = $this->where($map)->find();
		if (!$rule) {
			return false;
		}
		return $rule;
	}
}