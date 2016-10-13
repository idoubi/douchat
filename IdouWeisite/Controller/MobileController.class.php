<?php 

namespace Addons\IdouWeisite\Controller;
use Mp\Controller\MobileBaseController;

/**
 * 微网站移动端控制器
 * @author 艾逗笔
 */
class MobileController extends MobileBaseController {
   
    /**
     * 微站首页
     */
    function index() {
       global $_G;
       $site_info = M('idou_weisite_list')->where(array('mpid'=>get_mpid(),'id'=>I('get.wid')))->find();
       $template = $_G['addon_path'] . 'View/Mobile/default/index.html';
       $this->assign('_G', $_G);
       $this->assign('site_info', $site_info);
       $this->display($template);
    }

    // 分类列表
    function lists() {
        $cate_id = I('get.cate_id');
        $map ['wid'] = I('get.wid');
        $webinfo=M('idou_weisite_list')->find(I('get.wid'));
        $this->assign('webinfo',$webinfo);
        if ($cate_id) {
            $map ['cate_id'] = $cate_id;
        }
        //当前分类信息
        $cate = M ( 'idou_weisite_category' )->where ( 'id = ' . $map ['cate_id'] )->find ();
        $this->assign ( 'cate', $cate );

        // 二级分类
        $category = M ( 'idou_weisite_category' )->where ( 'pid = ' . $map ['cate_id'] )->order ( 'sort asc, id desc' )->select ();
        //如果存在二级分类
        if (!empty ( $category )) {
            $catelist = '';
            foreach ( $category as &$vo ) {
                empty ( $vo ['url'] ) && $vo ['url'] = create_addon_url('lists',array ('cate_id' => $vo['id'],'wid' => I('get.wid')));
                $catelist .= ','.$vo['id']; 
            }
            //获取属于这些分类的30篇文章
            $map['cate_id']=array('in',$catelist);
            $cmslist = M ('idou_weisite_cms')->where ($map)->field('id,title,intro,cover,cate_id')->order('cTime asc')->limit(30)->select ();
            foreach ($cmslist as $key => $value) {
                $temp=M ( 'idou_weisite_category' )->find($value['cate_id']);
                $cmslist[$key]['catename']=$temp['title'];
            }
            $this->assign('list_data',$cmslist);
            //将阅读量的前3显示在图片区
            $bannerlist = M ('idou_weisite_cms')->where ($map)->field('id,title,intro,cover')->order('view_count desc')->limit(3)->select ();
            $this->assign('bannerlist',$bannerlist);
            //所属的二级分类
            $this->assign ( 'category', $category );
            // $this->_footer ();
	        if ($webinfo['sub_temp']) {
	            $tempname=ADDON_PATH .'IdouWeisite/View/Mobile/sub_temp/'.$webinfo['sub_temp'].'/cate.html';
	            $currentpath=ADDON_PATH .'IdouWeisite/View/Mobile/sub_temp/'.$webinfo['sub_temp'];
	        }else{
	            $tempname=ADDON_PATH .'IdouWeisite/View/Mobile/sub_temp/default/cate.html';
	            $currentpath=ADDON_PATH .'IdouWeisite/View/Mobile/sub_temp/default';
	        }
	        $this->assign('currentpath',$currentpath);
	        $this->display($tempname);
        } else {
            $page = I ( 'p', 1, 'intval' );
            $row = 20;
            $data = M ( 'idou_weisite_cms' )->where ( $map )->order ( 'sort asc, id DESC' )->page ( $page, $row )->select();
            //轮播阅读量最高的三篇文章
            $bannerlist = M ( 'idou_weisite_cms' )->where ( $map )->order ( 'view_count desc' )->limit(3)->select ();
            $this->assign('bannerlist',$bannerlist);
            /* 查询记录总数 */
            $count = M ( 'idou_weisite_cms' )->where ( $map )->count ();
            $list_data ['list_data'] = $data;
            // 分页
            if ($count > $row) {
                $page = new \Think\Page ( $count, $row );
                $page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
                $list_data ['_page'] = $page->show ();
            }
            
            $this->assign ( $list_data );

            
        	$this->_footer ($webinfo['id']);

	        if ($webinfo['list_temp']) {
	            $tempname=ADDON_PATH .'IdouWeisite/View/Mobile/list_temp/'.$webinfo['list_temp'].'/lists.html';
	            $currentpath=ADDON_PATH .'IdouWeisite/View/Mobile/list_temp/'.$webinfo['list_temp'];
	        }else{
	            $tempname=ADDON_PATH .'IdouWeisite/View/Mobile/list_temp/default/lists.html';
	            $currentpath=ADDON_PATH .'IdouWeisite/View/Mobile/list_temp/default';
	        }
	        $this->assign('currentpath',$currentpath);
	        $this->display($tempname);
        }
    }
    // 详情
    function detail() {
        $map ['id'] = I ( 'get.id', 0, 'intval' );
        $info = M ( 'weisite_cms' )->where ( $map )->find ();
        $this->assign ( 'info', $info );
        
        M ( 'weisite_cms' )->where ( $map )->setInc ( 'view_count' );

        $webinfo=M('idou_weisite_list')->find($info['wid']);
        $this->assign('webinfo',$webinfo);
        $this->_footer ($webinfo['id']);

        if ($webinfo['cont_temp']) {
            $tempname=ADDON_PATH .'WeiSite/View/Mobile/cont_temp/'.$webinfo['cont_temp'].'/detail.html';
            $currentpath=ADDON_PATH .'WeiSite/View/Mobile/cont_temp/'.$webinfo['cont_temp'];
        }else{
            $tempname=ADDON_PATH .'WeiSite/View/Mobile/cont_temp/default/detail.html';
            $currentpath=ADDON_PATH .'WeiSite/View/Mobile/cont_temp/default';
        }
        $this->display($tempname);
    }
    
    // 3G页面底部导航
    function _footer($wid) {
        $list = M ( 'idou_weisite_nav' )->where(array('wid'=>$wid))->select();
        foreach ( $list as $k => $vo ) {
			if ($vo ['icon']) {
				$vo ['icon'] = '<img src="' . $vo ['icon'] . '" >';
			} else {
				$vo ['icon'] = '';
			}

            if ($vo ['pid'] != 0)
                continue;
            $one_arr [$k] = $vo;
            unset ( $list [$k] );
        }
        
        foreach ( $one_arr as &$p ) {
            $two_arr = array ();
            foreach ( $list as $key => $l ) {
                if ($l ['pid'] != $p ['id'])
                    continue;
                $two_arr [] = $l;
                unset ( $list [$key] );
            }
            $p ['child'] = $two_arr;
        }
        $this->assign ( 'footer', $one_arr );

        $webinfo=M('idou_weisite_list')->find($wid);
        if ($webinfo['foot_temp']) {
            $tempname=ADDON_PATH .'IdouWeisite/View/Mobile/foot_temp/'.$webinfo['foot_temp'].'/footer.html';
            $currentpath=ADDON_PATH .'IdouWeisite/View/Mobile/foot_temp/'.$webinfo['foot_temp'];
        }else{
            $tempname=ADDON_PATH .'IdouWeisite/View/Mobile/foot_temp/default/footer.html';
            $currentpath=ADDON_PATH .'IdouWeisite/View/Mobile/foot_temp/default';
        }

        $html = $this->fetch ($tempname);
        $this->assign ( 'footer_html', $html );
    }

}

?>