<?php 

namespace Addons\IdouGuestbook\Controller;
use Mp\Controller\MobileBaseController;

/**
 * 留言板移动端控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class MobileController extends MobileBaseController {

    /**
     * 留言板首页
     * @author 艾逗笔<765532665@qq.com>
     */
    public function index() {
        global $_G;
        $settings = get_addon_settings('IdouGuestbook');
        $settings['share_title'] || $settings['share_title'] = '留言板';
        $settings['share_desc'] || $settings['share_desc'] = '点击进来给我留言吧';
        $settings['share_cover'] || $settings['share_cover'] = $_G['site_url'] . 'Public/Admin/img/nopic.jpg';
        $this->assign('settings', $settings);
        $map['status'] = 1;
        $map['mpid'] = get_mpid();

        $page = max(1, intval(I('p')));
        $count = M('idou_guestbook_list')->where($map)->count();
        $per = $settings['per'] ? $settings['per'] : 10;
        $page_count = ceil($count/$per);

        $show['page_count'] = $page_count;
        $show['page'] = $page;
        if ($page < $page_count) {
            $show['next_page_url'] = create_addon_url('index', array('p'=>($page+1)));
        }
        if ($page > 1) {
            $show['prev_page_url'] = create_addon_url('index', array('p'=>($page-1)));
        }
        $message_list = M('idou_guestbook_list')->where($map)->order('create_time desc')->page($page, $per)->select();
        foreach ($message_list as $k => &$v) {
            $v['fans_info'] = get_fans_info($v['openid']);
        }
        $this->assign('fans_info', get_fans_info());
        $this->assign('message_list', $message_list);
        $this->assign('show', $show);
        $this->display();
    }

    /**
     * 处理留言
     * @author 艾逗笔<765532665@qq.com>
     */
    public function deal_message() {
    	$GuestbookList = D('Addons://IdouGuestbook/IdouGuestbookList');
    	C('TOKEN_ON', false);
    	if (!$GuestbookList->create()) {
    		$return['errcode'] = 0;
	    	$return['errmsg'] = $GuestbookList->getError();
    	} else {
    		if (I('id')) {
    			$GuestbookList->save();
    		} else {
    			$GuestbookList->add();
    		}
    		$return['errcode'] = 1;
    		$return['errmsg'] = '留言成功';	
    	}
    	$return['data'] = I('post.');
    	$this->ajaxReturn($return);	
    }
}

?>