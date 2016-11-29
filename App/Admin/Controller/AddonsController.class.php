<?php 

namespace Admin\Controller;
use Admin\Controller\BaseController;

/**
 * 插件管理控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class AddonsController extends BaseController {

	/**
	 * 已安装插件
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function lists() {
		$install_addons = D('Addons')->get_installed_addons();
		$this->addCrumb('系统管理', U('Index/index'), '')
			 ->addCrumb('插件管理', u('Addons/lists'), '')
			 ->addCrumb('已安装插件', '', 'active')
			 ->addNav('已安装插件', '', 'active')
			 ->addNav('未安装插件', U('Addons/not_install'), '')
			 ->addNav('设计新插件', U('Addons/add'), '')
			 ->addListItem('logo', '插件LOGO', 'image', array('attr'=>'width=80,height=80'))
			 ->addListItem('name', '插件名称')
			 ->addListItem('bzname', '插件标识名')
			 ->addListItem('desc', '插件描述')
			 ->addListItem('version', '当前版本')
			 ->addListItem('last_version', '最新版本')
			 ->addListItem('author', '作者')
			 ->addListItem('id', '操作', 'custom', array(
			 	'options' => array(
			 		array(
			 			'title' => '卸载插件',
			 			'url' => U('uninstall_addon', array('_addon'=>'{bzname}')),
			 			'class' => 'btn btn-danger btn-sm icon-delete'
			 		)
			 	)
			 ))
			 ->setListPer(20)
			 ->setListData($install_addons)
			 ->common_lists();
	}

	/**
	 * 未安装插件
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function not_install() {
		$dir_arr = array();
		$scan_dir = ADDON_PATH;			// 文件夹遍历目标
		$handle = opendir($scan_dir);			// 打开文件目录
		if ($handle) {
			while (($file = readdir($handle)) !== false) {		// 遍历目录
				if ($file != '.' && $file != '..') {
					$exists_addon_info = D('Addons')->get_addon_info_by_bzname($file);
					if (!$exists_addon_info || $exists_addon_info['status'] == 0) {			    // 找出未安装的插件
						$addon_info = D('Addons')->get_addon_dir_info($file);
						if ($addon_info) {
							$addons[] = $addon_info;
						}
					} 
				}
			}
		} else {
			$addons = array();
		}
		$this->addCrumb('系统管理', U('Index/index'), '')
			 ->addCrumb('插件管理', u('Addons/lists'), '')
			 ->addCrumb('未安装插件', '', 'active')
			 ->addNav('已安装插件', U('Addons/lists'), '')
			 ->addNav('未安装插件', '', 'active')
			 ->addNav('设计新插件', U('Addons/add'), '')
			 ->addListItem('logo', '插件LOGO', 'image', array('attr'=>'width=80,height=80'))
			 ->addListItem('name', '插件名称')
			 ->addListItem('bzname', '插件标识名')
			 ->addListItem('desc', '插件描述')
			 ->addListItem('version', '当前版本')
			 ->addListItem('last_version', '最新版本')
			 ->addListItem('author', '作者')
			 ->addListItem('id', '操作', 'custom', array(
			 	'options' => array(
			 		array(
			 			'title' => '安装插件',
			 			'url' => U('install_addon', array('_addon'=>'{bzname}')),
			 			'class' => 'btn btn-success btn-sm icon-edit'
			 		)
			 	)
			 ))
			 ->setListPer(20)
			 ->setListData($addons)
			 ->common_lists();
	}

	/**
	 * 安装插件
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function install_addon() {
		$addon = I('_addon');
		if (empty($addon)) {
			$this->error('没有要安装的插件');
		}
		$addon_info = D('Addons')->get_addon_dir_info($addon);
		if (!$addon_info) {
			$this->error('插件信息文件不存在，无法安装插件');
		}
		if ($addon_info['bzname'] != $addon) {
			$this->error('插件信息文件中的标识名与要安装的插件名称不符，无法安装插件');
		}
		if (!$addon_info['name'] || !$addon_info['version'] || !$addon_info['author']) {
			$this->error('插件名称、版本号、作者等信息不完整，无法安装插件');
		}

		$exists_addon_info = D('Addons')->get_addon_info_by_bzname($addon_info['bzname']);
		if ($exists_addon_info['status'] == 1) {
			$this->error('插件已安装，请先卸载插件再重新安装');
		}
		if ($exists_addon_info) {
			$data['id'] = $exists_addon_info['id'];
		}
		$data['name'] = $addon_info['name'];
		$data['bzname'] = $addon_info['bzname'];
		$data['desc'] = $addon_info['desc'];
		$data['type'] = $addon_info['type'];
		$data['version'] = $addon_info['version'];
		$data['author'] = $addon_info['author'];
		$data['logo'] = $addon_info['logo'];
		$data['config'] = json_encode($addon_info['config']);
		C('TOKEN_ON', false);
		$Addons = D('Addons');
		if (!$Addons->create($data)) {
			$this->error($Addons->getError());
		} else {
			if ($addon_info['install_sql']) {
				$install_sql_path = ADDON_PATH . $addon . DIRECTORY_SEPARATOR . $addon_info['install_sql'];
				if (is_file($install_sql_path)) {
					if (strpos($addon_info['install_sql'], '.sql')) {
						execute_sql_file($install_sql_path);
					} elseif (strpos($addon_info['install_sql'], '.php')) {
						include $install_sql_path;
					} else {
						$this->error('数据库安装文件格式错误');
					}
				}
			}
			if (isset($data['id']) && $data['id'] > 0) {
				$Addons->save();
			} else {
				$Addons->add();
			}
			$this->success('安装插件成功', U('lists'));
		}
	}

	/**
	 * 卸载插件
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function uninstall_addon() {
		$addon = I('_addon');
		if (empty($addon)) {
			$this->error('没有要卸载的插件');
		}
		$exists_addon_info = D('Addons')->get_addon_info_by_bzname($addon);
		if (!$exists_addon_info) {
			$this->error('要卸载的插件不存在');
		}
		if ($exists_addon_info['status'] != 1) {
			$this->error('插件未安装，不能进行卸载');
		}
		$res = D('Addons')->uninstall_addon($addon);
		if (!$res) {
			$this->success('卸载插件失败');
		} else {
			$this->success('卸载插件成功', U('not_install'));
		}
	}

	/**
	 * 升级插件
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function upgrade_addon() {
		$addon = I('_addon');
		if (empty($addon)) {
			$this->error('没有要升级的插件');
		}
		$exists_addon_info = D('Addons')->get_addon_info_by_bzname($addon);
		if (!$exists_addon_info) {
			$this->error('要升级的插件不存在');
		}
		if ($exists_addon_info['status'] != 1) {
			$this->error('插件未安装，不能进行升级');
		}
		$info_path = ADDON_PATH . $addon . DIRECTORY_SEPARATOR . 'info.php';
		if (!is_file($info_path)) {
			$this->error('插件信息文件不存在，无法进行升级');
		}
		$addon_info = include $info_path;
		if ($addon_info['bzname'] != $addon) {
			$this->error('插件信息文件中的标识名与要升级的插件名称不符，无法进行升级');
		}
		if (!$addon_info['name'] || !$addon_info['version'] || !$addon_info['author']) {
			$this->error('插件名称、版本号、作者等信息不完整，无法安装插件');
		}
		if ($exists_addon_info['version'] > $addon_info['version']) {
			$this->error('要升级到的版本号低于插件当前版本号，不能进行升级');
		}

		$data['id'] = $exists_addon_info['id'];
		$data['name'] = $addon_info['name'];
		$data['bzname'] = $addon_info['bzname'];
		$data['desc'] = $addon_info['desc'];
		$data['version'] = $addon_info['version'];
		$data['author'] = $addon_info['author'];
		$data['config'] = json_encode($addon_info['config']);
		C('TOKEN_ON', false);
		$Addons = D('Addons');
		if (!$Addons->create($data)) {
			$this->error($Addons->getError());
		} else {
			if (isset($data['id']) && $data['id'] > 0) {
				$Addons->save();
			} else {
				$Addons->add();
			}
			$this->success('升级插件成功', U('lists'));
		}
	}

	/**
	 * 创建插件
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function add() {
		if (IS_POST) {
			$addons_path = ADDON_PATH;
			$data = I('post.');
			if (!$data['name']) {
				$this->error('插件名称不能为空');
			}
			if (!$data['bzname']) {
				$this->error('插件标识名不能为空');
			}
			if (!preg_match('/^[a-zA-Z][a-zA-Z]{1,29}$/', $data['bzname'])) {
				$this->error('插件标识名不合法');
			}
			if (is_dir("{$addons_path}{$data['bzname']}")) {
				$this->error('相同名称的插件文件夹已存在');
			}
			if (!$data['version']) {
				$this->error('插件版本号不能为空');
			}
			if (!preg_match('/^[0-20]\.([0-9]\.){0,1}[0-9]$/', $data['version'])) {
				$this->error('插件版本号格式不正确');
			}
			if (!$data['author']) {
				$this->error('作者名称必须');
			}
			if (!$data['desc']) {
				$this->error('插件描述必须');
			}
			if (!is_writable($addons_path)) {
				$this->error('插件目录没有写入权限');
			}
			$addon_dir = "{$addons_path}{$data['bzname']}/";
			$files = array();
			$files[] = "{$addon_dir}";
			$files[] = "{$addon_dir}info.php";
			$files[] = "{$addon_dir}Controller/";
			$files[] = "{$addon_dir}Controller/WebController.class.php";
			if ($data['respond_rule'] == '1') {
				$files[] = "{$addon_dir}Controller/RespondController.class.php";
			}
			if ($data['entry'] == '1') {
				$files[] = "{$addon_dir}Controller/MobileController.class.php";
				$files[] = "{$addon_dir}View/";
				$files[] = "{$addon_dir}View/Mobile/";
			}
			$res = create_dir_or_files($files);			// 生成插件文件夹及文件

            // 上传插件logo
            $logofile = $_FILES['logo'];
            $logo = '';
            if ($logofile['name'] != '') {      // 上传了logo文件
                if ($logofile['error'] == 0 && $logofile['size'] < 5*1024*1000 && in_array($logofile['type'], array('image/png','image/gif','image/jpg','image/jpeg'))) {
                    $logoname = 'logo.' . substr($logofile['type'], 6);
                    $logopath = $addon_dir . $logoname;
                    move_uploaded_file($logofile['tmp_name'], $logopath);
                    $logo = $logoname;
                }
            }
			$info_file = <<<str
<?php

return array(
	'name' => '{$data['name']}',
	'bzname' => '{$data['bzname']}',
	'desc' => '{$data['desc']}',
	'version' => '{$data['version']}',
	'author' => '{$data['author']}',
    'logo' => '{$logo}',
	'config' => array(
		'respond_rule' => {$data['respond_rule']},
		'setting' => {$data['setting']},
		'entry' => {$data['entry']},
		'menu' => {$data['menu']}
	)
);

?>
str;
			file_put_contents("{$addon_dir}info.php", $info_file);
			$web_file = <<<str
<?php

namespace Addons\\{$data['bzname']}\Controller;
use Mp\Controller\AddonsController;

/**
 * {$data['name']}后台管理控制器
 * @author {$data['author']}
 */
class WebController extends AddonsController {

}

?>
str;
			file_put_contents("{$addon_dir}Controller/WebController.class.php", $web_file);

			if ($data['respond_rule'] == '1') {
				$respond_file = <<<str
<?php

namespace Addons\\{$data['bzname']}\Controller;
use Mp\Controller\ApiController;

/**
 * {$data['name']}响应控制器
 * @author {$data['author']}
 */
class RespondController extends ApiController {

	/**
	 * 微信交互
	 * @param \$message array 微信消息数组
	 */
	public function wechat(\$message = array()) {

	}
}

?>
str;
				file_put_contents("{$addon_dir}Controller/RespondController.class.php", $respond_file);
			}
			if ($data['entry'] == '1') {
				$mobile_file = <<<str
<?php

namespace Addons\\{$data['bzname']}\Controller;
use Mp\Controller\MobileBaseController;

/**
 * {$data['name']}移动端控制器
 * @author {$data['author']}
 */
class MobileController extends MobileBaseController {

}

?>
str;
				file_put_contents("{$addon_dir}Controller/MobileController.class.php", $mobile_file);
			}
			$this->success('创建插件成功', U('not_install'));
		} else {
			$crumb = array(
				array(
					'title' => '系统管理',
					'url' => U('Index/index'),
					'class'=>''
				),
				array(
					'title' => '扩展管理',
					'url' => U('Addons/lists'),
					'class' => '',
				),
				array(
					'title' => '设计新插件',
					'url' => '',
					'class' => 'active'
				)
			);
			$nav = array(
				array(
					'title' => '已安装插件',
					'url' => U('Addons/lists'),
					'class' => ''
				),
				array(
					'title' => '未安装插件',
					'url' => U('Addons/not_install'),
					'class' => ''
				),
				array(
					'title' => '设计新插件',
					'url' => '',
					'class' => 'active'
				)
			);
			$this->assign('crumb', $crumb);
			$this->assign('nav', $nav);
			$model['fields'] = array(
				'name' => array(
					'title' => '插件名称',
					'type' => 'text',
					'placeholder' => '留言板',
					'tip' => '请输入插件的中文名称'
				),
				'bzname' => array(
					'title' => '插件标识名',
					'type' => 'text',
					'placeholder' => 'IdouGuestbook',
					'tip' => '请输入插件的英文标识，为了防止插件重名，请带上自定义前缀，采用驼峰式命名，如IdouDemo、HaoShop、JohnHelloWorld等'
				),
				'version' => array(
					'title' => '插件版本号',
					'type' => 'text',
					'placeholder' => '1.0.1',
					'tip' => '请输入插件版本号，格式如0.1、1.0.2、2.3'
				),
				'author' => array(
					'title' => '作者',
					'type' => 'text',
					'placeholder' => '张小龙',
					'tip' => '请留下你的大名'
				),
				'desc' => array(
					'title' => '插件描述',
					'type' => 'textarea',
					'placeholder' => '一个好用的商城插件',
					'tip' => '请输入插件描述，描述越详细，越容易受到关注'
				),
                'logo' => array(
                    'title' => '插件logo',
                    'type' => 'file',
                    'tip' => '请上传一张插件的logo'
                ),
				'respond_rule' => array(
					'title' => '是否需要响应规则',
					'type' => 'radio',
					'options' => array(
						1 => '需要',
						0 => '不需要'
					),
					'value' => 1,
					'tip' => '如果选择需要响应规则，创建插件时会自动生成Repond控制器，用于处理用户通过公众号发送的消息'
				),
				'setting' => array(
					'title' => '是否需要配置参数',
					'type' => 'radio',
					'options' => array(
						1 => '需要',
						0 => '不需要'
					),
					'value' => 1,
					'tip' => '如果选择需要配置参数，创建插件时会在Web控制器自动生成setting方法，用户设置插件配置项'
				),
				'entry' => array(
					'title' => '是否需要功能入口',
					'type' => 'radio',
					'options' => array(
						1 => '需要',
						0 => '不需要'
					),
					'value' => 1,
					'tip' => '如果选择需要功能入口，创建插件时会自动生成Mobile控制器，用于移动端功能开发'
				),
				'menu' => array(
					'title' => '是否需要业务导航',
					'type' => 'radio',
					'options' => array(
						1 => '需要',
						0 => '不需要'
					),
					'value' => 1,
					'tip' => '如果选择需要业务导航，则可以通过编辑业务导航菜单来进行插件后台开发'
				)
			);
            $model['submit_type'] = 'post';
            $model['meta_title'] = '创建插件';
			parent::common_add($model);
		}
	}
}



 ?>