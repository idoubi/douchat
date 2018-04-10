<?php

/**
 * Api控制器
 * @author 艾逗笔<http://idoubi.cc>
 */
namespace Addons\Weisite\Controller;

use Mp\Controller\ApiBaseController;

class ApiController extends  ApiBaseController {

    // 获取配置
    public function getSettings() {
        $addonSettings = D('Mp/AddonSetting')->get_addon_settings('Weisite', $this->mpid);
        $settings = [];
		
        // 幻灯片
        if (!empty($addonSettings['index_show']['slider_is_show'])) {
        	$sliderCount = !empty($addonSettings['index_show']['slider_count']) ? intval($addonSettings['index_show']['slider_count']) : 3;
        	$sliderItems = M('weisite_slider')->where([
        		'mpid' => $this->mpid,
				'is_show' => 1
			])->field('id,title,img,url')->order('sort desc')->limit($sliderCount)->select();
        	$settings['index_show']['slider'] = [
        		'is_show' => 1,
				'height' => !empty($addonSettings['index_show']['slider_height']) ? intval($addonSettings['index_show']['slider_height']) : 200,
				'count' => count($sliderItems),
				'items' => $sliderItems
			];
		} else {
        	$settings['index_show']['slider'] = [
        		'is_show' => 0,
				'height' => 200,
				'count' => 0,
				'items' => []
			];
		}
		
		// 网站公告
		if (!empty($addonSettings['index_show']['notice_is_show'])) {
        	$noticeIcon = !empty($addonSettings['index_show']['notice_icon']) ? $addonSettings['index_show']['notice_icon'] : '';
        	$noticeStyle = !empty($addonSettings['index_show']['notice_style']) ? $addonSettings['index_show']['notice_style'] : 'style_1';
        	$noticeContent = !empty($addonSettings['index_show']['notice_content']) ? explode(PHP_EOL, $addonSettings['index_show']['notice_content']) : [];
        	$settings['index_show']['notice'] = [
        		'is_show' => 1,
				'style' => $noticeStyle,
				'icon' => $noticeIcon,
				'content' => $noticeContent
			];
		} else {
        	$settings['index_show']['notice'] = [
        		'is_show' => 0,
				'style' => 'style_1',
				'icon' => '',
				'content' => []
			];
		}
		
		// 首页导航
		if (!empty($addonSettings['index_show']['nav_is_show'])) {
        	$navCount = !empty($addonSettings['index_show']['nav_count']) ? intval($addonSettings['index_show']['nav_count']) : 4;
        	$navStyle = !empty($addonSettings['index_show']['nav_style']) ? $addonSettings['index_show']['nav_style'] : 'style_1';
        	$navItems = M('weisite_navigation')->where([
        		'mpid' => $this->mpid,
				'is_show' => 1,
				'pid' => 1
			])->field('id,title,icon,url')->order('sort desc')->limit($navCount)->select();
        	$settings['index_show']['nav'] = [
        		'is_show' => 1,
				'style' => $navStyle,
				'count' => count($navItems),
				'items' => $navItems
			];
		} else {
        	$settings['index_show']['nav'] = [
        		'is_show' => 0,
				'style' => 'style_1',
				'count' => 0,
				'items' => []
			];
		}
		
		// 首页分类
		if (!empty($addonSettings['index_show']['category_is_show'])) {
        	$cateCount = !empty($addonSettings['index_show']['category_count']) ? intval($addonSettings['index_show']['category_count']) : 1;
        	$cateItems = M('weisite_category')->where([
        		'mpid' => $this->mpid,
				'is_show' => 1,
				'index_show' => 1
			])->field('id,title,intro,icon')->order('sort desc')->limit($cateCount)->select();
        	foreach ($cateItems as &$v) {
        		if (empty($v['id'])) {
        			continue;
				}
        		$v['title_style'] = !empty($v['index_show_title_style']) ? $v['index_show_title_style'] : 'style_1';
        		$v['content_style'] = !empty($v['index_show_content_style']) ? $v['index_show_content_style'] : 'style_1';
        		$articleCount = !empty($v['index_show_count']) ? intval($v['index_show_count']) : 5;
        		$articleItems = M('weisite_article')->where([
        			'mpid' => $this->mpid,
					'cate_id' => $v['id'],
					'is_show' => 1
				])->field('id,title,intro,cover')->limit($articleCount)->select();
        		$v['count'] = count($articleItems);
        		$v['articles'] = $articleItems;
			}
			$settings['index_show']['category'] = [
				'is_show' => 1,
				'count' => count($cateItems),
				'items' => $cateItems
			];
		} else {
        	$settings['index_show']['category'] = [
        		'is_show' => 0,
				'count' => 0,
				'items' => []
			];
		}
		
		// 底部导航
		if (!empty($addonSettings['tabbar']['is_show'])) {
  			$tabbarCount = 5;
  			$tabbarItems = M('weisite_navigation')->where([
  				'mpid' => get_mpid(),
				'pid' => 2,
				'is_show' => 1
			])->field('id,title,icon,selected_icon,intro,url')->order('sort desc')->limit($tabbarCount)->select();
  			$settings['tabbar'] = [
  				'is_show' => 1,
				'font_color' => $addonSettings['tabbar']['font_color'],
				'selected_font_color' => $addonSettings['tabbar']['selected_font_color'],
				'backgroud_color' => $addonSettings['tabbar']['backgroud_color'],
				'border_color' => $addonSettings['tabbar']['border_color'],
				'items' => $tabbarItems
			];
		} else {
        	$settings['tabbar'] = [
        		'is_show' => 0,
				'font_color' => '',
				'selected_font_color' => '',
				'background_color' => '',
				'border_color' => '',
				'count' => 0,
				'items' => []
 			];
		}
		
		$settings['site'] = [
			'title' => $addonSettings['site']['title'],
			'description' => $addonSettings['site']['description'],
			'copyright' => $addonSettings['site']['copyright'],
			'theme_color' => $addonSettings['site']['theme_color']
		];

        $this->response(0, '获取成功', $settings);
    }

    // 获取分类下的文章列表
    public function getArticles() {
        $cid = I('cid', 0, 'intval');
        $page = I('page', 1, 'intval');
        $per = 12;
        $data = M('weisite_article')->where([
            'mpid' => $this->mpid,
            'cate_id' => $cid,
            'is_show' => 1
                                            ])->field('id,title,intro,cover,content,created_at')
			->order('sort desc, created_at desc')
			->page($page, $per)->select();
        $this->response(0, '获取成功', $data);
    }
    
    // 获取文章详情
	public function getArticle() {
    	$aid = I('aid', 0, 'intval');
    	$data = M('weisite_article')->where([
    		'mpid' => $this->mpid,
			'id' => $aid,
			'is_show' => 1
		])->field('id,title,intro,cover,content,created_at')
			->find();
    	$this->response(0, '获取成功', $data);
			
	}
	
	// 获取页面内容
	public function getPage() {
    	$pid = I('pid', 0, 'intval');
    	$data = M('weisite_article')->where([
    		'mpid' => $this->mpid,
			'id' => $pid
		])->field('id,title,intro,cover,content,created_at')
			->find();
    	$this->response(0, '获取成功', $data);
	}

}