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
    public function get_addon_settings($addon = '', $mpid = '', $theme = '', $type = '') {
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

        if (!empty($theme)) {
            $map['theme'] = $theme;
        }
        if (!empty($type)) {
            $map['type'] = $type;
        }

        $settings = [];

        $addonConfig = D('Mp/Addons')->get_addon_config($addon);

        if (isset($addonConfig['setting']) && $addonConfig['setting']) {
            $data = M('addon_setting')->where($map)->select();
            if (isset($addonConfig['setting_list_group']) && !empty($addonConfig['setting_list_group']) && is_array($addonConfig['setting_list_group'])) {
                $groups = [];
                foreach ($addonConfig['setting_list_group'] as $k => $v) {
                    if (isset($v['name']) && !empty($v['name'])) {
                        $groups[] = $v['name'];
                    } elseif (is_string($k)) {
                    	$groups[] = $k;
					}
                }
                if (isset($addonConfig['setting_list']) && !empty($addonConfig['setting_list']) && is_array($addonConfig['setting_list'])) {
                    $fields = [];
                    foreach ($addonConfig['setting_list'] as $k => $v) {
                        if (isset($v['name']) && !empty($v['name'])) {
                            $fields[] = $v['name'];
                        } elseif (is_string($k)) {
                        	$fields[] = $k;
						}
                    }
                    foreach ($data as $v) {
                        if (isset($v['name']) && !empty($v['name']) && in_array($v['name'], $fields)) {
                            if (isset($v['type']) && !empty($v['type']) && in_array($v['type'], $groups)) {
                                $settings[$v['type']][$v['name']] = $v['value'];
                            }
                        }
                    }
                }
            } else {
                if (isset($addonConfig['setting_list']) && !empty($addonConfig['setting_list']) && is_array($addonConfig['setting_list'])) {
                    $fields = [];
                    foreach ($addonConfig['setting_list'] as $k => $v) {
                        if (isset($v['name']) && !empty($v['name'])) {
                            $fields[] = $v['name'];
                        } elseif (is_string($k)) {
                            $fields[] = $k;
                        }
                    }
                    foreach ($data as $v) {
                        if (isset($v['name']) && !empty($v['name']) && in_array($v['name'], $fields)) {
                            $settings[$v['name']] = $v['value'];
                        }
                    }
                }
            }
        }

        if ($type) {
            return isset($settings[$type]) ? $settings[$type] : [];
        }

        return $settings;
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