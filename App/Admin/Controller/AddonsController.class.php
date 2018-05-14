<?php

/**
 * 插件管理控制器
 * @author 艾逗笔<http://idoubi.cc>
 */
namespace Admin\Controller;
use Admin\Controller\BaseController;

class AddonsController extends BaseController {
    private $addon_dir;

	/**
	 * 已安装插件
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
			 ->addListItem('type', '插件类型')
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
			$addon_model = D('Addons');
            $data = I('post.');
            $data['type'] = implode(',',$data['type']);

			if(!$addon_model->create($data,4)){
			    $this->error($addon_model->getError());
            }

			if (!is_writable($addons_path)) {
				$this->error('插件目录没有写入权限');
			}

            $this->addon_dir = ADDON_PATH . "{$data['bzname']}/";
			$this->createDirOrFiles($data); // 生成插件文件夹及文件
			$logo = $this->uploadLogo();

			$this->putInfoField($data,$logo);
            $this->putWebControllerFile($data);

			if ($data['respond_rule'] == '1') {
			    $this->putResponseControllerFile($data);
			}
			if ($data['entry'] == '1') {
			    $this->putMobileControllerFile($data);
			}
			if (strpos($data['type'],'2') !== false) {
			    $this->putApiControllerFile($data);
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
                'type' => array(
                    'title' => '选择插件类型',
                    'type' => 'checkbox',
                    'options' => array(
                        1 => '公众号',
                        2 => '小程序'
                    ),
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

    /**
     * 生成相应的文件
     * @desc
     * @param $data
     * @author 16
     * @date 2018/5/10
     */
    private function createDirOrFiles($data)
    {

        $files = array();
        $files[] = "{$this->addon_dir}";
        $files[] = "{$this->addon_dir}info.php";
        $files[] = "{$this->addon_dir}Controller/";
        $files[] = "{$this->addon_dir}Controller/WebController.class.php";

        if ($data['respond_rule'] == '1') {
            $files[] = "{$this->addon_dir}Controller/RespondController.class.php";
        }

        if ($data['entry'] == '1') {
            $files[] = "{$this->addon_dir}Controller/MobileController.class.php";
            $files[] = "{$this->addon_dir}View/";
            $files[] = "{$this->addon_dir}View/Mobile/";
        }

        if (strpos($data['addon_type'], '2')) {
            $files[] = "{$this->addon_dir}Controller/ApiController.class.php";
        }

        $res = create_dir_or_files($files);            // 生成插件文件夹及文件
    }

    /**
     * 上传插件logo
     * @desc
     * @author 16
     * @date 2018/5/10
     */
    private function uploadLogo(){
        $logofile = $_FILES['logo'];
        $logo = '';
        if ($logofile['name'] != '') {      // 上传了logo文件
            if ($logofile['error'] == 0 && $logofile['size'] < 5*1024*1000 && in_array($logofile['type'], array('image/png','image/gif','image/jpg','image/jpeg'))) {
                $logoname = 'logo.' . substr($logofile['type'], 6);
                $logopath = $this->addon_dir . $logoname;
                move_uploaded_file($logofile['tmp_name'], $logopath);
                $logo = $logoname;
            }
        }

        return $logo;
    }

    /**
     * 填充info.php文件
     * @desc
     * @param $data
     * @author 16
     * @date 2018/5/10
     */
    private function putInfoField($data,$logo){
        $info_file = <<<str
<?php

return array(
	'name' => '{$data['name']}',
	'bzname' => '{$data['bzname']}',
	'type'=>'{$data['type']}',
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
        file_put_contents("{$this->addon_dir}info.php", $info_file);
    }

    /**
     * 填充webController文件
     * @desc
     * @param $data
     * @author 16
     * @date 2018/5/10
     */
    private function putWebControllerFile($data){
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
        file_put_contents("{$this->addon_dir}Controller/WebController.class.php", $web_file);
    }

    /**
     * 填充ResponseController文件
     * @desc
     * @author 16
     * @date 2018/5/10
     */
    private function putResponseControllerFile($data){
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
        file_put_contents("{$this->addon_dir}Controller/RespondController.class.php", $respond_file);
    }

    /**
     * 填充Mobile文件
     * @desc
     * @param $data
     * @author 16
     * @date 2018/5/10
     */
    private function putMobileControllerFile($data){
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
        file_put_contents("{$this->addon_dir}Controller/MobileController.class.php", $mobile_file);
    }

    /**
     * 填充ApiController文件
     * @desc
     * @param $data
     * @author 16
     * @date 2018/5/10
     */
    private function putApiControllerFile($data){
        $api_file = <<<str
<?php

namespace Addons\\{$data['bzname']}\Controller;
use Mp\Controller\ApiBaseController;

/**
 * {$data['name']}插件Api控制器
 * @author {$data['author']}
 */
class ApiController extends ApiBaseController {


}

?>
str;
        file_put_contents("{$this->addon_dir}Controller/ApiController.class.php", $api_file);
    }

}
 ?>