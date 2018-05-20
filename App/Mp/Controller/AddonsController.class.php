<?php

/**
 * 插件后台公用控制器
 * @author 艾逗笔<http://idoubi.cc>
 */
namespace Mp\Controller;
use Mp\Controller\BaseController;

class AddonsController extends BaseController {

	public $crumb;
	public $nav;
	public $subnav;
	public $tip;
	
	// 初始化
	public function __construct() {
		parent::__construct();
		
		global $_G;
		$_G['addon_info'] = D('Addons')->get_addon_info($this->addon);		// 获取插件信息
		$_G['addon_config'] = $_G['addon_info']['config'];					// 获取插件配置
		$_G['addon_type'] = !isset($_G['addon_info']['type']) ? [1] : explode(',', $_G['addon_info']['type']);	// 插件类型
		if (!in_array($this->action, ['manage'])) {
			if (!in_array($this->mp_type, $_G['addon_type'])) {
				$this->error('此应用不支持当前账号类型');
			}
		}
		$_G['addon_path'] = $_G['addons_path'] . $_G['addon'] . '/';
		$_G['addon_url'] = $_G['addons_url'] . $_G['addon'] . '/';
		$_G['addon_public_path'] = $_G['addons_path'] . 'View/Public/';
		$_G['addon_public_url'] = $_G['addons_url'] . 'View/Public/';
		if (empty($_G['addon']) && $_G['action'] != 'manage') {
			$this->redirect('Addons/manage');
		}
		$this->assign('_G', $_G);
		add_hook('tip', 'Mp\Behavior\TipBehavior');							// 添加生成提示信息的钩子
		$this->tip = hook('tip');											// 执行钩子，获取提示信息
		add_hook('crumb', 'Mp\Behavior\CrumbBehavior');						// 添加生成面包屑的钩子
		$this->crumb = hook('crumb');										// 执行钩子，获取面包屑
		add_hook('nav', 'Mp\Behavior\NavBehavior');							// 添加生成插件导航的钩子
		$this->nav = hook('nav');											// 执行钩子，获取插件导航数据
		if ($_G['action'] == 'entry') {										// 获取子导航
			$this->subnav = [];
		} elseif ($_G['action'] == 'setting') {
			$this->subnav = [];
		} else {
			$this->subnav = [];
		}
	}
	
	public function _initialize() {
		parent::_initialize();
	}

	/**
	 * 插件管理
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function manage() {
		if (IS_AJAX) {
			$addon = I('addon');
			$status = I('status');
			$map['user_id'] = $this->user_id;
			$map['mpid'] = get_mpid();
			$map['addon'] = $addon;
			if (M('addons_access')->where($map)->find()) {
				M('addons_access')->where($map)->setField('status', $status);
			} else {
				$map['status'] = $status;
				M('addons_access')->add($map);
			}
			$return['errcode'] = 0;
			$return['errmsg'] = 'success';
			$return['data'] = array(
				'addon' => $addon,
				'status' => $status
			);
			$this->ajaxReturn($return);
		} else {
			$crumb = array(
				array(
					'title' => '应用中心',
					'url' => '',
					'class' => ''
				),
				array(
					'title' => '全部应用',
					'url' => '',
					'class' => 'active'
				)
			);
			$this->assign('crumb', $crumb);
			$addons = $this->user_access['addons'];
			foreach ($addons as $k => &$v) {
				if (!D('Addons')->get_addon_dir_info($v)) {
					unset($addons[$k]);
				}
				$v = D('Addons')->get_addon_info_by_bzname($v);
				if (M('addons_access')->where(array('user_id'=>get_user_id(),'mpid'=>get_mpid(),'addon'=>$v['bzname']))->getField('status') == 2) {
					$v['forbidden'] = 1;
				} else {
					$v['forbidden'] = 0;
				}
			}
			$this->assign('addons', $addons);
			$this->display();
		}
	}

	/**
	 * 插件首页
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function index() {
		$this->assign('sidenavs', $this->nav);
		$this->display('Addons/index');
	}

	/**
	 * 响应规则设置
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function rule() {
		if (IS_POST) {
			$_POST['type'] = 'respond';			
			$Rule = D('MpRule');
			if (!$Rule->create()) {
				$this->error($Rule->getError());
			} else {
				if (I('id')) {
					$Rule->save();
				} else {
					$Rule->add();
				}
				$this->success('保存响应规则成功');
			}
		} else {
			$addon = get_addon();
			$addon_info = D('Addons')->get_addon_info();
			$rule = D('MpRule')->get_respond_rule();
			$this->setCrumb($this->crumb)
				 ->setNav($this->nav)
				 ->setSubNav($this->subnav)
				 ->setTip($this->tip)
				 ->assign('rule', $rule)
				 ->display('Addons/rule');
		}		
	}

	/**
	 * 配置参数
	 * @author 艾逗笔<765532665@qq.com>
	 */
    public function setting($settings = array()) {
		$mpid = get_mpid();
        $addon = get_addon();
        $addon_config = D('Mp/Addons')->get_addon_config($addon);
        $type = I('type', '');
        $theme = I('theme', '');
        $theme_config = [];
        $nav = [];          // 导航
        $fields = [];   // 配置字段
        if (!empty($theme)) {       // 主题设置
            $this->tip = '当前进行设置的主题：'.$theme;
            if (isset($addon_config['theme_list'])) {
                if (isset($addon_config['theme_list'][$theme])) {
                    $theme_config = $addon_config['theme_list'][$theme];
                } else {
                    foreach ($addon_config['theme_list'] as $vtheme) {
                        if (isset($vtheme['name']) && $vtheme['name'] == $theme) {
                            $theme_config = $vtheme;
                        }
                    }
                }
            }
        } else {    // 模块设置
            $theme_config = $addon_config;
        }

        if (isset($theme_config['setting_list_group'])) {
            $listGroup = [];
            foreach ($theme_config['setting_list_group'] as $k => $v) {
                if (isset($v['is_show']) && $v['is_show'] == 1) {
                    if (isset($v['name']) && !empty($v['name']) && isset($v['title']) && !empty($v['title'])) {
                        $listGroup[$v['name']] = $v;
                    } elseif (is_string($k)) {
                    	$listGroup[$k] = $v;
					}
                }
            }

            if (empty($type)) {
                if ($theme_config['setting_list_default_group']) {
                    $type = $theme_config['setting_list_default_group'];
                } else {
                    $types = array_keys($listGroup);
                    $type = $types[0];
                }
            }

            foreach ($theme_config['setting_list'] as $k => $v) {
                if ($listGroup[$type]['is_show'] == 1) {
                    if ($v['group'] == $type) {
                        $fields[$k] = $v;
                    }
                }
            }

            foreach ($listGroup as $k => $v) {
                $nav[] = array(
                    'title' => $v['title'],
                    'url' => U('/addon/'.$addon.'/setting?type='.$k.'&theme='.$theme),
                    'class' => $type == $k ? 'active' : ''
                );
            }

        } else {
            $fields = $theme_config['setting_list'];
        }

        $keys = [];
        foreach ($fields as $k => $v) {
            if (isset($v['name']) && !empty($v['name'])) {
                $keys[] = $v['name'];
            } elseif (is_string($k)) {
                $keys[] = $k;
            }
        }

        $fields[] = [
            'name' => 'theme',
            'title' => '当前主题',
            'value' => $theme,
            'type' => 'hidden'
        ];
        $fields[] = [
            'name' => 'type',
            'title' => '当前类型',
            'value' => $type,
            'type' => 'hidden'
        ];
		
        if (IS_POST) {
            $post = I('post.');
            $map = [
                'mpid' => $mpid,
                'addon' => $addon,
                'theme' => $theme,
                'type' => $type
            ];
            $datas = [];
            foreach ($post as $k => $v) {
                if (in_array($k, $keys)) {
                    $map['name'] = $k;
                    if (M('addon_setting')->where($map)->find()) {
                        M('addon_setting')->where($map)->save(['value'=>$v]);
                    } else {
                        $data = $map;
                        $data['name'] = $k;
                        $data['value'] = $v;
                        $datas[] = $data;
                    }
                }
            }
            if (!empty($datas)) {
                M('addon_setting')->addAll($datas);
            }
			try {
				$redisHost = C('REDIS_HOST', null, '127.0.0.1');
				$redisPort = C('REDIS_PORT', null, '6379');
				$redisPrefix = C('REDIS_PREFIX', null, 'dc_');
				$redisDB = C('REDIS_DB', null, 0);
				$redis = new \Redis();
				$redis->connect($redisHost,$redisPort);
				$redis->select($redisDB);
				$settingsKey = $redisPrefix . 'settings_raw:' . $addon . ':' . $mpid;
				$settingsValue = M('addon_setting')->where([
					'addon' => $addon,
					'mpid' => $mpid,
					'name' => ['in', $keys]
				])->select();
				$redis->set($settingsKey, json_encode($settingsValue));
			} catch (\Exception $e) {
		
			}
            $this->success('编辑成功', U('/addon/'.$addon.'/setting?type='.$type.'&theme='.$theme));
        } else {
            $this->setCrumb($this->crumb)
                ->setMetaTitle('配置参数')
                ->setNav($this->nav)
                ->setSubNav($this->subnav)
                ->setTip($this->tip)
                ->setModel('addon_setting')
                ->setFormFields($fields)
                ->setFormData(D('AddonSetting')->get_addon_settings($addon, $mpid, $theme, $type))
                ->common_edit();
        }
    }

	/**
	 * 封面入口设置
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function entry() {
		if (IS_POST) {
			if (!I('keyword')) {
				$this->error('关键词不能为空');
			}

			// 添加入口
			$AddonEntry = D('AddonEntry');
			// /$AddonEntry->startTrans();			// 开启事务
			if (!$AddonEntry->create()) {
				$this->error($AddonEntry->getError());
			} else {
				if (I('id')) {
					$AddonEntry->save();
					$entry_id = I('id');
				} else {
					$entry_id = $AddonEntry->add();
				}

				// 添加关键词
				$Rule = D('MpRule');
				C('TOKEN_ON', false);
				$_POST['keyword'] = I('keyword');
				$_POST['type'] = 'entry';
				$_POST['entry_id'] = $entry_id;

				if (!$Rule->create()) {
					$AddonEntry->rollback();			// 添加关键词失败，事务回滚
					$this->error($Rule->getError());
				} else {
					if (I('rule_id')) {
						$_POST['id'] = I('rule_id');
						$Rule->save($_POST);
						$rule_id = I('rule_id');
					} else {
						$rule_id = $Rule->add();
					}
					if ($entry_id && $rule_id) {
						$AddonEntry->rollback();
						$this->success('保存功能入口成功');
					} else {
						$AddonEntry->rollback();				// 添加响应规则失败，事务回滚
						$this->success('保存功能入口失败');
					}
				}
			}			
		} else {
			$addon_entry = D('AddonEntry')->get_addon_entry(I('act'));
			if (!$addon_entry) {
				foreach ($addon_config['entry_list'] as $k => $v) {
					if ($k == I('act')) {
						$addon_entry['act'] = $k;
						$addon_entry['name'] = $v;
						break;
					}
				}
			}
			$this->setCrumb($this->crumb)
				 ->setMetaTitle($addon_entry['name'])
				 ->setNav($this->nav)
				 ->setSubNav($this->subnav)
				 ->setTip($this->tip)
				 ->assign('entry', $addon_entry)
				 ->display('Addons/entry');
		}
	}

	/**
	 * 重写模板显示方法
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function display($templateFile='',$charset='',$contentType='',$content='',$prefix='') {
		if ($this->addon && ACTION_NAME != 'rule' && ACTION_NAME != 'setting' && ACTION_NAME != 'entry' && ACTION_NAME != 'preview' && ACTION_NAME != 'index') {
			if (empty($templateFile)) {
				$templateFile = ADDON_PATH . $this->addon . '/View/' . CONTROLLER_NAME . '/' . ACTION_NAME . C('TMPL_TEMPLATE_SUFFIX');
			} else {
				$tempArr = explode('/', $templateFile);
				switch (count($tempArr)) {
					case 1:
						$templateFile = ADDON_PATH . $this->addon . '/View/' . CONTROLLER_NAME . '/' . $tempArr[0] . C('TMPL_TEMPLATE_SUFFIX');
						break;
					case 2:
						$templateFile = ADDON_PATH . $this->addon . '/View/' . $tempArr[0] . '/' . $tempArr[1] . C('TMPL_TEMPLATE_SUFFIX');
						break;
					default:
						break;
				}
			}
			if (!is_file($templateFile)) {
				E('模板不存在:'.$templateFile);
			}
			$this->model['crumb'] || $this->setCrumb($this->crumb);
			$this->model['nav'] || $this->setNav($this->nav);
			$this->model['subnav'] || $this->setSubNav($this->subnav);
			$this->model['tip'] || $this->setTip($this->tip);
			parent::display($templateFile,$charset,$contentType,$content,$prefix);
		} else {
			parent::display($templateFile,$charset,$contentType,$content,$prefix);
		}
    }

	/**
	 * 页面预览
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function preview($act, $params=array()){
		if (!$params['mpid']) {
			$params['mpid'] = get_mpid();
		}
		$url = U('/addon/'.get_addon().'/mobile/'.$act.'@'.C('HTTP_HOST'), $params);
	    $this->assign('url',$url);
	    parent::display ("Addons/preview");
	}

	private function parse_children($type) {
		$addons = D('Addons')->get_installed_addons($type);
		foreach ($addons as $k => $v) {
			if (!in_array($v['bzname'], $this->user_access['addons'])) {
				continue;
			}
			$arr['title'] = $v['name'];
			$arr['url'] = U('Mp/Web/index', array('addon'=>$v['bzname']));
			$arr['class'] = '';
			$children[] = $arr;
		}
		return $children;
	}
}



 ?>