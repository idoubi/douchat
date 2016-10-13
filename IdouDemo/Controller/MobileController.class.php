<?php

namespace Addons\IdouDemo\Controller;
use Mp\Controller\MobileBaseController;

/**
 * 功能演示移动端控制器
 * @author 艾逗笔
 */
class MobileController extends MobileBaseController {

    // 功能演示入口
    public function index() {
        $this->display();
    }

    // 功能演示列表
    public function demo_list($type) {
        switch ($type) {
            case 'basic':
                $title = '微信交互功能演示';
                $lists = array(
                    array(
                        'title' => '回复文本',
                        'url' => create_addon_url('demo_detail', array('tag'=>'reply_text')),
                    ),
                    array(
                        'title' => '回复图文',
                        'url' => create_addon_url('demo_detail', array('tag'=>'reply_news')),
                    )
                );
                break;
            
            default:
                # code...
                break;
        }
        $this->assign('title', $title);
        $this->assign('lists', $lists);
        $this->display();
    }

    // 功能演示详情
    public function demo_detail() {
        $this->display();
    }

}

?>