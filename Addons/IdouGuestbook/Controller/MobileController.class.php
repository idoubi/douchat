<?php

/**
 * 留言板移动端控制器
 * @author 艾逗笔<http://idoubi.cc>
 */
namespace Addons\IdouGuestbook\Controller;
use Mp\Controller\MobileBaseController;

class MobileController extends MobileBaseController {

    // 首页
    public function index() {
        global $_G;
        $settings = get_addon_settings('IdouGuestbook');
        $settings['share_title'] || $settings['share_title'] = '留言板';
        $settings['share_desc'] || $settings['share_desc'] = '点击进来给我留言吧';
        $settings['share_cover'] || $settings['share_cover'] = $_G['site_url'] . 'Public/Admin/img/nopic.jpg';
        $this->assign('settings', $settings);
        $this->assign('fans_info', get_fans_info());
        $this->display();
    }
}

?>