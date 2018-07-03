<?php

/**
 * 插件管理控制器
 * @author 艾逗笔<http://idoubi.cc>
 */
namespace Admin\Controller;
use Admin\Controller\BaseController;

class AddonsController extends BaseController {
    private $addon_dir;
    private $wx_dir;

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
		$data['type'] = isset($addon_info['type']) ? $addon_info['type'] : 1;
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
			$wx_template_path = './Wxapps/';
			$addon_model = D('Addons');
            $data = I('post.');
            $data['type'] = implode(',',$data['type']);

			if(!$addon_model->create($data,4)){
			    $this->error($addon_model->getError());
            }

			if (!is_writable($addons_path)) {
				$this->error('插件目录没有写入权限');
			}

			if(strpos($data['type'],'2') !== false){
			    if(!is_writable($wx_template_path)){
                    $this->error('小程序模板目录没有写入权限');
                }elseif (is_dir($wx_template_path . "{$data['bzname']}")){
                    $this->error('相同名称的小程序已存在');
                }
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
			    //生成小程序模板
                $this->wx_dir = $wx_template_path . "{$data['bzname']}/";
                $this->create_template_of_wx($data);
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

    /**
     * 生成小程序模板
     * @desc
     * @author 16
     * @date 2018/5/15
     */
    private function create_template_of_wx($info){
        $this->create_dir_or_files_of_wx();
        $this->put_common_pages_file();
        $this->put_util_js_file();
        $this->put_app_file();
        $this->put_config_file($info);
    }

    /**
     * 生成相应的小程序模板文件
     * @desc
     * @author 16
     * @date 2018/5/15
     */
    private function create_dir_or_files_of_wx(){
        $files = array();
        $files[] = "{$this->wx_dir}";
        $files[] = "{$this->wx_dir}pages/";
        $files[] = "{$this->wx_dir}pages/index/";
        $files[] = "{$this->wx_dir}pages/index/index.js";
        $files[] = "{$this->wx_dir}pages/index/index.wxml";
        $files[] = "{$this->wx_dir}pages/index/index.wxss";
        $files[] = "{$this->wx_dir}pages/logs/";
        $files[] = "{$this->wx_dir}pages/logs/logs.js";
        $files[] = "{$this->wx_dir}pages/logs/logs.wxml";
        $files[] = "{$this->wx_dir}pages/logs/logs.wxss";
        $files[] = "{$this->wx_dir}utils/";
        $files[] = "{$this->wx_dir}utils/util.js";
        $files[] = "{$this->wx_dir}app.js";
        $files[] = "{$this->wx_dir}ext.js";
        $files[] = "{$this->wx_dir}app.json";
        $files[] = "{$this->wx_dir}app.wxss";
        $files[] = "{$this->wx_dir}project.config.json";

        $res = create_dir_or_files($files);
    }

    /**
     * 填充基础的pages目录内容
     * @desc
     * @author 16
     * @date 2018/5/15
     */
    private function put_common_pages_file(){
        //index.js
        $index_js_file = <<<str
//index.js
//获取应用实例
const app = getApp()

Page({
  data: {
    motto: 'Hello World',
    userInfo: {},
    hasUserInfo: false,
    canIUse: wx.canIUse('button.open-type.getUserInfo')
  },
  //事件处理函数
  bindViewTap: function() {
    wx.navigateTo({
      url: '../logs/logs'
    })
  },
  onLoad: function () {
    if (app.globalData.userInfo) {
      this.setData({
        userInfo: app.globalData.userInfo,
        hasUserInfo: true
      })
    } else if (this.data.canIUse){
      // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
      // 所以此处加入 callback 以防止这种情况
      app.userInfoReadyCallback = res => {
        this.setData({
          userInfo: res.userInfo,
          hasUserInfo: true
        })
      }
    } else {
      // 在没有 open-type=getUserInfo 版本的兼容处理
      wx.getUserInfo({
        success: res => {
          app.globalData.userInfo = res.userInfo
          this.setData({
            userInfo: res.userInfo,
            hasUserInfo: true
          })
        }
      })
    }
  },
  getUserInfo: function(e) {
    console.log(e)
    app.globalData.userInfo = e.detail.userInfo
    this.setData({
      userInfo: e.detail.userInfo,
      hasUserInfo: true
    })
  }
})
str;
        file_put_contents("{$this->wx_dir}pages/index/index.js", $index_js_file);

        //index.wxml
        $index_wxml_file = <<<str
<!--index.wxml-->
<view class="container">
  <view class="userinfo">
    <button wx:if="{{!hasUserInfo && canIUse}}" open-type="getUserInfo" bindgetuserinfo="getUserInfo"> 获取头像昵称 </button>
    <block wx:else>
      <image bindtap="bindViewTap" class="userinfo-avatar" src="{{userInfo.avatarUrl}}" background-size="cover"></image>
      <text class="userinfo-nickname">{{userInfo.nickName}}</text>
    </block>
  </view>
  <view class="usermotto">
    <text class="user-motto">{{motto}}</text>
  </view>
</view>
str;
        file_put_contents("{$this->wx_dir}pages/index/index.wxml", $index_wxml_file);

        //index.wxss
        $index_wxss_file = <<<str
/**index.wxss**/
.userinfo {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.userinfo-avatar {
  width: 128rpx;
  height: 128rpx;
  margin: 20rpx;
  border-radius: 50%;
}

.userinfo-nickname {
  color: #aaa;
}

.usermotto {
  margin-top: 200px;
}
str;
        file_put_contents("{$this->wx_dir}pages/index/index.wxss", $index_wxss_file);

        //logs.js
        $logs_js_file = <<<str
//logs.js
const util = require('../../utils/util.js')

Page({
  data: {
    logs: []
  },
  onLoad: function () {
    this.setData({
      logs: (wx.getStorageSync('logs') || []).map(log => {
        return util.formatTime(new Date(log))
      })
    })
  }
})

str;
        file_put_contents("{$this->wx_dir}pages/logs/logs.js", $logs_js_file);

        //logs.wxml
        $logs_wxml_file = <<<str
<!--logs.wxml-->
<view class="container log-list">
  <block wx:for="{{logs}}" wx:for-item="log">
    <text class="log-item">{{index + 1}}. {{log}}</text>
  </block>
</view>
str;
        file_put_contents("{$this->wx_dir}pages/logs/logs.wxml", $logs_wxml_file);

        //logs.wxss
        $logs_wxss_file = <<<str
.log-list {
  display: flex;
  flex-direction: column;
  padding: 40rpx;
}
.log-item {
  margin: 10rpx;
}

str;
        file_put_contents("{$this->wx_dir}pages/logs/logs.wxss", $logs_wxss_file);

        //logs.json
        $logs_json_file = <<<str
{
  "navigationBarTitleText": "查看启动日志"
}
str;
        file_put_contents("{$this->wx_dir}pages/logs/logs.json", $logs_json_file);
    }

    /**
     * 填充util文件
     * @desc
     * @author 16
     * @date 2018/5/15
     */
    private function put_util_js_file(){
        //util.js
        $util_js_file = <<<str
var apiBase = {
    domain: '',
    mpid: 0,
    addon: '',
    version: '',
    ak: '',
    sk: ''
};

// 初始化
function init() {
    var ext = require('../ext.js')
    if (ext && ext.apiType == 1) {  // 手动接入的方式
        apiBase = ext.apiBase;
    }
}

// 发起请求
function request(options) {
    var url = options.url || '';    // 请求地址
    if (url.indexOf('http') != 0) {     // 通过相对地址发起请求
        url = apiBase.domain + '/addon/' + apiBase.addon + '/api/' + url + '/mpid/' + apiBase.mpid;
    }
    var data = options.data || {};          // 请求数据
    var header = options.header || {};      // 请求头
    if (!header['content-type']) {
        header['content-type'] = 'application/x-www-form-urlencoded';
    }
    if (!header['ak']) {
        header['ak'] = apiBase.ak;
    }
    if (!header['sk']) {
        header['sk'] = apiBase.sk;
    }
    if (!header['version']) {
        header['version'] = apiBase.version;
    }
    if (!header['User-Token']) {
        header['User-Token'] = wx.getStorageSync('userToken');
    }
    var method = (options.method || 'get').toUpperCase();   // 请求方式
    var dataType = options.dataType || 'json';              // 请求数据的格式
    var responseType = options.responseType || 'text';      // 响应数据格式
    wx.request({
        url: url,
        method: method,
        data: data,
        header: header,
        dataType: dataType,
        responseType: responseType,
        success: function(res) {
            if (options.success && typeof options.success == 'function') {
                options.success(res.data);
            }
        },
        fail: function(res) {
            if (options.fail && typeof options.fail == 'function') {
                options.fail(res.data);
            }
        },
        complete: function(res) {
            if (options.complete && typeof options.complete == 'function') {
                options.complete(res.data);
            }
        }
    });
}

// 获取配置
function getSettings(cb, refresh) {
    var settings = wx.getStorageSync('settings');
    if (!settings || refresh == true) {
        request({
            url: 'getSettings',
            method: 'get',
            success: function (res) {
                if (res && res.errcode == 0 && res.items) {
                    wx.setStorageSync('settings', res.items);
                    if (typeof cb == 'function') {
                        cb(res.items);
                    }
                }
            }
        });
    } else {
        if (typeof cb == 'function') {
            cb(settings);
        }
    }
}

// 获取用户信息
function getUserInfo(cb, refresh) {
    if (refresh == true) {
        login(cb)
    } else {
        var userInfo = wx.getStorageSync('userInfo');
        if (typeof cb == 'function') {
            cb(userInfo);
        }
    }
}

// 登录检测
function checkLogin(options) {
    wx.checkSession({
       success: function() {
           var userInfo = wx.getStorageSync('userInfo');
           var userToken = wx.getStorageSync('userToken');
           if (!userInfo || !userToken) {
               if (options && typeof options.fail == 'function') {
                   options.fail();
               }
           } else {
               request({
                   url: 'isLogin',
                   method: 'post',
                   header: {
                       'User-Token': userToken
                   },
                   success: function (res) {
                       if (res && res.errcode == 0) {  // 登录有效
                           if (options && typeof options.success == 'function') {
                               options.success();
                           }
                       } else {
                           if (options && typeof options.fail == 'function') {
                               options.fail();
                           }
                       }
                   },
                   fail: function () {
                       if (options && typeof options.fail == 'function') {
                           options.fail();
                       }
                   }
               });
           }
       },
       fail: function() {
           if (options && typeof options.fail == 'function') {
               options.fail();
           }
       } 
    });
}

// 用户登录
function login(cb) {
    wx.login({
       success: function(res) {     // 本地登录成功
            wx.getUserInfo({    // 获取用户信息
                success: function(ret) {
                    request({       // 远程登录
                        url: 'login',
                        method: 'post',
                        data: {
                            code: res.code,
                            encryptedData: ret.encryptedData,
                            iv: ret.iv
                        },
                        success: function(data) {
                            if (data.errcode == 0 && data.items) { // 登录成功
                                // 缓存用户信息和登录态sk
                                wx.setStorageSync('userInfo', data.items.user_info);
                                wx.setStorageSync('userToken', data.items.user_token);
                                if (typeof cb == 'function') {
                                    cb(data.items.user_info);
                                }
                            } else {
                                loginFail();
                            }
                        },
                        fail: function() {
                            loginFail();
                        }
                    });
                },
                fail: function() {
                    loginFail();
                }
            });
       },
       fail: function() {   // 登录失败
           loginFail();
       }
    });
}

// 登录失败
function loginFail() {
    wx.showModal({
        content: '登录失败，请允许获取用户信息,如不显示请删除小程序重新进入',
        showCancel: false
    });
}

function showTip(sms, icon, fun, t) {
    if (!t) {
        t = 1000;
    }
    wx.showToast({
        title: sms,
        icon: icon,
        duration: t,
        success: fun
    })
}

function showModal(c, t, fun) {
    if (!t)
        t = '提示'
    wx.showModal({
        title: t,
        content: c,
        showCancel: false,
        success: fun
    })
}

function formatTime(date) {
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var day = date.getDate();
    var hour = date.getHours();
    var minute = date.getMinutes();
    var second = date.getSeconds();

    return [year, month, day].map(formatNumber).join('/') + ' ' + [hour, minute, second].map(formatNumber).join(':');
}

function formatNumber(n) {
    n = n.toString();
    return n[1] ? n : '0' + n
}

module.exports = {
    formatTime: formatTime,
    init: init,
    checkLogin: checkLogin,
    login: login,
    getSettings: getSettings,
    getUserInfo: getUserInfo,
    request: request,
    showTip: showTip,
    showModal: showModal
};

str;
        file_put_contents("{$this->wx_dir}utils/util.js", $util_js_file);
    }

    /**
     * 填充主要文件
     * @desc
     * @author 16
     * @date 2018/5/15
     */
    private function put_app_file(){
        //app.js
        $app_js_file = <<<str
//app.js
App({
  onLaunch: function () {
    // 展示本地存储能力
    var logs = wx.getStorageSync('logs') || []
    logs.unshift(Date.now())
    wx.setStorageSync('logs', logs)

    // 登录
    wx.login({
      success: res => {
        // 发送 res.code 到后台换取 openId, sessionKey, unionId
      }
    })
    // 获取用户信息
    wx.getSetting({
      success: res => {
        if (res.authSetting['scope.userInfo']) {
          // 已经授权，可以直接调用 getUserInfo 获取头像昵称，不会弹框
          wx.getUserInfo({
            success: res => {
              // 可以将 res 发送给后台解码出 unionId
              this.globalData.userInfo = res.userInfo

              // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
              // 所以此处加入 callback 以防止这种情况
              if (this.userInfoReadyCallback) {
                this.userInfoReadyCallback(res)
              }
            }
          })
        }
      }
    })
  },
  globalData: {
    userInfo: null
  }
})
str;
        file_put_contents("{$this->wx_dir}app.js", $app_js_file);
        //app.json
        $app_json_file = <<<str
{
  "pages":[
    "pages/index/index",
    "pages/logs/logs"
  ],
  "window":{
    "backgroundTextStyle":"light",
    "navigationBarBackgroundColor": "#fff",
    "navigationBarTitleText": "WeChat",
    "navigationBarTextStyle":"black"
  }
}

str;
        file_put_contents("{$this->wx_dir}app.json", $app_json_file);
        //app.wxss
        $app_wxss_file = <<<str
/**app.wxss**/
.container {
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
  padding: 200rpx 0;
  box-sizing: border-box;
}
str;
        file_put_contents("{$this->wx_dir}app.wxss", $app_wxss_file);
        //project.config.json
        $project_config_json_file = <<<str
{
	"description": "项目配置文件。",
	"packOptions": {
		"ignore": []
	},
	"setting": {
		"urlCheck": true,
		"es6": true,
		"postcss": true,
		"minified": true,
		"newFeature": true
	},
	"compileType": "miniprogram",
	"libVersion": "1.9.98",
	"appid": "touristappid",
	"projectname": "demo",
	"condition": {
		"search": {
			"current": -1,
			"list": []
		},
		"conversation": {
			"current": -1,
			"list": []
		},
		"game": {
			"currentL": -1,
			"list": []
		},
		"miniprogram": {
			"current": -1,
			"list": []
		}
	}
}
str;
        file_put_contents("{$this->wx_dir}project.config.json", $project_config_json_file);
    }

    /**
     * 填充配置文件
     * @desc
     * @param $info
     * @author 16
     * @date 2018/5/15
     */
    private function put_config_file($info){
        //ext.js
        $site_url = SITE_URL;
        $mpid = get_mpid();
        list($ak,$sk) = $this->get_secret_key();
        $ext_js_file = <<<str
    module.exports = {
    apiType: 1, // 手动接入的方式
    apiBase: {
        domain: '{$site_url}',
        mpid: {$mpid},
        addon: '{$info['bzname']}',
        version: '{$info['version']}',
        ak: '{$ak}',
        sk: '{$sk}'
    }
}
str;
        file_put_contents("{$this->wx_dir}ext.js", $ext_js_file);
    }

    /**
     * 获取密钥
     * @desc
     * @author 16
     * @date 2018/5/16
     */
    private function get_secret_key(){
        return get_mpid() ? array_values(D('AccessKey')->get_mp_access_key()) : [0,0];
    }

}
