<?php 

namespace Addons\IdouWeisite\Controller;
use Mp\Controller\AddonsController;

/**
 * 微网站后台管理控制器
 * @author 艾逗笔
 */
class WebController extends AddonsController {

    /**
     * 初始化
     */
    public function _initialize() {
        parent::_initialize();
        $wid = I('get.wid');
        $web_info = M('idou_weisite_list')->where(array('mpid'=>get_mpid(),'id'=>$wid))->find();
        $this->assign('tip', '当前站点：'.$web_info['title'].'，<a href="'.create_addon_url('web_preview',array('wid'=>$wid)).'" target="_blank">点此预览</a>');
    }

	/**
	 * 微站列表
	 */
	public function web_list() {
        $this->assign('tip', '');
		$model = get_addon_model('weisite_web_list');
		$this->common_lists($model);
	}

	/**
	 * 添加微站
	 */
	public function web_add() {
		$model = get_addon_model('weisite_web_add');
		$this->common_add($model);
	}

	/**
	 * 编辑微站
	 */
	public function web_edit() {
		$model = get_addon_model('weisite_web_edit');
		$this->common_edit($model);
	}

	/**
	 * 删除微网
	 * @author 北北
	 */
	public function web_del() {
		$model = array(
			'name' => 'weisite_list',
			'title' => '微网站',
			'success_url' => create_addon_url('web_list')
		);
		parent::delete($model);
	}


	/**
	 * 文章列表
	 */
	public function cms_list() {
		$model = get_addon_model('weisite_cms_list');
		$this->common_lists($model);
	}

	/**
	 * 添加文章
	 */
	public function cms_add() {
		$model = get_addon_model('weisite_cms_add');
		$this->common_add($model);
	}

	/**
	 * 编辑文章
	 */
	public function cms_edit() {
		$model = get_addon_model('weisite_cms_edit');
		$this->common_edit($model);
	}

	/**
	 * 删除文章
	 */
	public function cms_del() {
		$cmsinfo=M('weisite_cms')->find(I('get.id'));
		$model = array(
			'name' => 'weisite_cms',
			'title' => '微网站',
			'success_url' => create_addon_url('cms_list',array('wid'=>$cmsinfo['wid']))
		);
		parent::delete($model);
	}

	/**
	 * 页面管理
	 */
	public function page_list() {
		$model = get_addon_model('weisite_page_list');
		$this->common_lists($model);
	}

	/**
	 * 新增页面
	 */
	public function page_add() {
		$model = get_addon_model('weisite_page_add');
		$this->common_add($model);
	}

	/**
	 * 编辑页面
	 */
	public function page_edit() {
		$model = get_addon_model('weisite_page_edit');
		$this->common_edit($model);
	}

	/**
	 * 删除页面
	 */
	public function page_delete() {
		$model = get_addon_model('weisite_page_delete');
		$this->common_delete($model);
	}

	/**
	 * 根据分类id获取分类名
	 */
	public function get_category($cateid) {
		if ($cateid) {
			$cateinfo = M('idou_weisite_category')->find($cateid);
			if ($cateinfo) {
				return $cateinfo['title'];
			} else {
				return '';
			}
		} else {
			return '';
		}
	}

	/**
	 * 解析分类
	 * @author 北北
	 */
    function tree_format($data,$pid=0,$level=0){
        static $_ret=array();
        foreach ($data as $k => $v) {
            if($v['pid']==$pid){
                $v['level']=$level;
                $_ret[]=$v;
                unset($data[$k]);
                $this->tree_format($data,$v['id'],$level+1);
            }
        }
        return $_ret;
    }


	/**
	 * 获取分类信息，作为文章的分类选择
	 */
    function get_cate_list($wid){
    	$results=M('idou_weisite_category')->where(array('wid'=>$wid))->order('sort asc')->select();
		$results=$this->tree_format($results);
		foreach ($results as $key => $value) {
			$data[$value['id']]=str_repeat("　", $value['level']*2).$value['title'];
		}
		return $data;
	}

	/**
	 * 分类列表
	 */
	public function cate_list() {
		$model = get_addon_model('weisite_cate_list');
		$this->common_lists($model);
	}

	/**
	 * 添加分类
	 */
	public function cate_add() {
		$model = get_addon_model('weisite_cate_add');
		$this->common_add($model);
	}

	/**
	 * 编辑分类
	 */
	public function cate_edit() {
		$model = get_addon_model('weisite_cate_edit');
		$this->common_edit($model);
	}

	/**
	 * 删除分类
	 */
	public function cate_del() {
		$cateinfo=M('weisite_category')->find(I('get.id'));
		$model = array(
			'name' => 'weisite_category',
			'title' => '微网站',
			'success_url' => create_addon_url('cate_list',array('wid'=>$cateinfo['wid']))
		);
		parent::delete($model);
	}


	/**
	 * 获取全部的分类
	 */
	public function get_categories() {
		$data = M('idou_weisite_category')->where(array('mpid'=>get_mpid()))->select();
		$categories[0] = '请选择分类';
		foreach ($data as $k => $v) {
			$categories[$v['id']] = $v['title'];
		}
		return $categories;
	}


	/**
	 * 轮播列表
	 * @author 北北
	 */
	public function slideshow_list() {
		$model = get_addon_model('weisite_slideshow_list');
		$this->common_lists($model);
	}

	/**
	 * 添加轮播
	 * @author 北北
	 */
	public function slideshow_add() {
		$model = get_addon_model('weisite_slideshow_add');
		$this->common_add($model);
	}

	/**
	 * 编辑轮播
	 * @author 北北
	 */
	public function slideshow_edit() {
		$model = get_addon_model('weisite_slideshow_edit');
		$this->common_edit($model);
	}

	/**
	 * 删除轮播
	 * @author 北北
	 */
	public function slideshow_del() {
		$slideshowinfo=M('slideshow_list')->find(I('get.id'));
		$model = array(
			'name' => 'weisite_slideshow',
			'title' => '微网站',
			'success_url' => create_addon_url('slideshow_list',array('wid'=>$slideshowinfo['wid']))
		);
		parent::delete($model);
	}


	/**
	 * 设置模板
	 * @author 北北
	 */
	public function temp_set() {
		$webinfo=M('idou_weisite_list')->find(I('get.wid'));
		if ($webinfo['mpid']!=get_mpid()) {
			$this->error('对不起，本微网不是您的');
		}

		$nav = array(
			array(
				'title' => '微网列表',
				'url' => create_addon_url('web_list'),
				'class' => ''
			),
			array(
				'title' => '微网设置',
				'url' => create_addon_url('web_edit',array('wid'=>I('get.wid'))),
				'class' => ''
			),
			array(
				'title' => '模板设置',
				'url' => create_addon_url('temp_set',array('wid'=>I('get.wid'))),
				'class' => 'active'
			),
			array(
				'title' => '分类管理',
				'url' => create_addon_url('cate_list',array('wid'=>I('get.wid'))),
				'class' => ''
			),
			array(
				'title' => '轮播图片',
				'url' => create_addon_url('slideshow_list',array('wid'=>I('get.wid'))),
				'class' => ''
			),
			array(
				'title' => '文章管理',
				'url' => create_addon_url('cms_list',array('wid'=>I('get.wid'))),
				'class' => ''
			)
		);
		$this->assign('subnav', $nav);

		$btn = array(
			array(
				'class' => 'btn btn-primary',
				'url' => create_addon_url('temp_set',array('wid'=>I('get.wid'),'method'=>'index')),
				'title'=>'首页模板'
			),
			array(
				'class' => 'btn btn-primary',
				'url' => create_addon_url('temp_set',array('wid'=>I('get.wid'),'method'=>'sub')),
				'title'=>'二级页面模板'
			),
			array(
				'class' => 'btn btn-primary',
				'url' => create_addon_url('temp_set',array('wid'=>I('get.wid'),'method'=>'list')),
				'title'=>'列表模板'
			),
			array(
				'class' => 'btn btn-primary',
				'url' => create_addon_url('temp_set',array('wid'=>I('get.wid'),'method'=>'cont')),
				'title'=>'内容页模板'
			),
			array(
				'class' => 'btn btn-primary',
				'url' => create_addon_url('temp_set',array('wid'=>I('get.wid'),'method'=>'foot')),
				'title'=>'页脚'
			),
		);
		$this->assign('btn',$btn);

		$type=$temp_method=I('get.method','index').'_temp';
		//需要设置的模板值
		$default=$webinfo[$temp_method];

        if(!empty($_POST)){
        	$data['id']=I('get.wid');
        	$data[$temp_method]=I('post.tempname');
        	$result=M('weisite_list')->save($data);
        	if ($result) {
        		$this->ajaxReturn('设置成功');
        	} else {
        		$this->ajaxReturn('设置失败');
        	}
        }else{
			$dir = SITE_PATH . '/Addons/IdouWeisite/View/Mobile/' . $type;
			$url = __ROOT__. '/Addons/IdouWeisite/View/Mobile/' . $type;

			$dirObj = opendir ( $dir );
			while ( $file = readdir ( $dirObj ) ) {
				if ($file === '.' || $file == '..' || $file == '.svn' || is_file ( $dir . '/' . $file ))
					continue;
				
				$res ['dirName'] = $res ['title'] = $file;
				
				// 获取配置文件
				if (file_exists ( $dir . '/' . $file . '/info.php' )) {
					$info = require_once $dir . '/' . $file . '/info.php';
					$res = array_merge ( $res, $info );
				}
				
				// 获取效果图
				if (file_exists ( $dir . '/' . $file . '/info.php' )) {
					$res ['icon'] = __ROOT__ . '/Addons/IdouWeisite/View/Mobile/' . $type . '/' . $file . '/icon.png';
				} else {
					//设置默认图片
					// $res ['icon'] = ADDON_PUBLIC_PATH . '/default.png';
				}
				
				// 默认选中
				if ($default == $file) {
					$res ['class'] = 'selected';
					$res ['checked'] = 'checked="checked"';
				}
				
				$tempList [] = $res;
				unset ( $res );
			}
			closedir ( $dir );
			$this->assign ( 'tempList', $tempList );
			
			$this->display ();        	
        }

	}

	public function web_preview() {
		$this->preview('index', array('wid'=>I('get.wid')));
	}

}

?>