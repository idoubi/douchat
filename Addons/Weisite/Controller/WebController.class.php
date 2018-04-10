<?php

/**
 * 后台管理控制器
 * @author 艾逗笔<http://idoubi.cc>
 */
namespace Addons\Weisite\Controller;

use Mp\Controller\AddonsController;

class WebController extends AddonsController {
	
	public function __construct() {
		parent::__construct();
		$act = ACTION_NAME;
		$nav = [
            [
                'title' => '模块设置',
                'url' => create_addon_url('setting'),
                'class' => $act == 'setting' ? 'active' : ''
            ],
            [
                'title' => '幻灯片管理',
                'url' => create_addon_url('sliderList'),
                'class' => in_array($act, ['sliderList', 'sliderAdd', 'sliderEdit', 'sliderDelete']) ? 'active' : ''
            ],
            [
                'title' => '导航管理',
                'url' => create_addon_url('navigationList'),
                'class' => in_array($act, ['navigationList', 'navigationAdd', 'navigationEdit', 'navigationDelete']) ? 'active' : ''
            ],
            [
                'title' => '分类管理',
                'url' => create_addon_url('categoryList'),
                'class' => in_array($act, ['categoryList', 'categoryAdd', 'categoryEdit', 'categoryDelete']) ? 'active' : ''
            ],
            [
                'title' => '文章管理',
                'url' => create_addon_url('articleList'),
                'class' => in_array($act, ['articleList', 'articleAdd', 'articleEdit', 'articleDelete']) ? 'active' : ''
            ],
            [
                'title' => '页面管理',
                'url' => create_addon_url('pageList'),
                'class' => in_array($act, ['pageList', 'pageAdd', 'pageEdit', 'pageDelete']) ? 'active' : ''
            ]
        ];
		$this->nav = $nav;
		$this->setNav($nav);
	}

	// 模块设置
    public function setting() {
	    parent::setting();
    }

    // 主题管理
    public function theme() {
	    parent::theme();
    }
    
	// 跳转提示
	public function getUrlTip() {
		$tip = '
仅支持下面列出的跳转地址，{}包裹的参数根据实际情况填写：<br>
首页：      /pages/index/index<br>
文章列表（cid为分类id）：   /pages/imglist/imglist?cid={cid}<br>
文章详情（aid为文章id）：   /pages/content/content?aid={aid}<br>
页面内容（pid为页面id）：   /pages/content/content?pid={pid}<br>
联系我们：   /pages/contact/contact<br>
关于我们：   /pages/about/about<br>
自定义H5页面（url为要跳转到的h5地址）：   /pages/h5/h5?url={url}
';
		return $tip;
	}

    // 获取幻灯片
    public function getSliders() {
	    $sliders = [];
	    $data = M('weisite_slider')->where(['mpid'=>get_mpid(), 'is_show'=>1])->select();
	    foreach ($data as $v) {
	        if (isset($v['title']) && !empty($v['title'])) {
	            $sliders[$v['id']] = $v['title'];
            }
        }
        return $sliders;
    }

    // 获取分类
    public function getCates() {
	    $cates = [];
	    $data = M('weisite_category')->where(['mpid'=>get_mpid(), 'is_show'=>1])->select();
	    foreach ($data as $v) {
	        if (isset($v['title']) && !empty($v['title'])) {
	            $cates[$v['id']] = $v['title'];
            }
        }
        return $cates;
    }

    // 获取分类名称
    public function getCateName($id) {
        return M('weisite_category')->where(['id'=>$id])->getField('title');
    }

    // 获取页面
    public function getPages() {
	    $pages = [];
	    $data = M('weisite_page')->where(['mpid'=>get_mpid()])->select();
	    foreach ($data as $v) {
	        if (isset($v['title']) && !empty($v['title'])) {
	            $pages[$v['id']] = $v['title'];
            }
        }
        return $pages;
    }

    // 获取导航
    public function getNavs() {
	    $pid = I('pid', 0, 'intval');
	    $data = M('weisite_navigation')->where(['mpid'=>get_mpid(),'id'=>$pid])->field('id,title')->select();
	    $navs = [];
	    if (!empty($data) && count($data) > 0) {
	        foreach ($data as $v) {
	            if (isset($v['title']) && !empty($v['title'])) {
	                $navs[$v['id']] = $v['title'];
                }
            }
        } else {
	        $navs[0] = '无';
        }
	    return $navs;
    }

    // 获取导航类型
    public function getNavTypes() {
	    return [
            'category' => '跳转到分类',
            'article' => '跳转到文章',
            'page' => '跳转到页面',
            'link' => '跳转到链接'
        ];
    }

    // 获取导航样式
    public function getNavStyles() {
	    return [
	        'style_1' => '样式1',
            'style_2' => '样式2',
            'style_3' => '样式3'
        ];
    }

	// 幻灯片列表
    public function sliderList() {
	    $this->setMetaTitle('幻灯片列表')
            ->addSubNav('幻灯片列表', '', 'active')
            ->setTip('如果开启了首页幻灯片显示，此处添加的幻灯片将会在首页展示')
            ->addButton('添加幻灯片', create_addon_url('sliderAdd'), 'btn btn-primary')
            ->setModel('weisite_slider')
			->addListItem('id', 'ID')
            ->addListItem('title', '标题')
            ->addListItem('img', '图片', 'image', ['attr'=>'width=120;height=80'])
            ->addListItem('url', '链接')
            ->addListItem('sort', '排序')
            ->addListItem('is_show', '是否显示', 'enum', ['options'=>[0=>'不显示',1=>'显示']])
            ->addListItem('id', '操作', 'custom', ['options'=>[
                [
                    'title' => '编辑',
                    'url' => create_addon_url('sliderEdit', ['id'=>'{id}']),
                    'class' => 'btn btn-info btn-sm'
                ],
                [
                    'title' => '删除',
                    'url' => create_addon_url('sliderDelete', ['id'=>'{id}']),
                    'class' => 'btn btn-danger btn-sm',
                    'attr' => 'onclick="return confirm(\'确认删除？\')"'
                ]
            ]])
            ->setListMap(['mpid'=>get_mpid()])
            ->common_lists();
    }

    // 添加幻灯片
    public function sliderAdd() {
	    $this->setMetaTitle('添加幻灯片')
            ->addSubNav('返回幻灯片列表', create_addon_url('sliderList'), '')
            ->addSubNav('添加幻灯片', '', 'active')
            ->setModel('weisite_slider')
            ->addFormField('title', '标题', 'text')
            ->addFormField('img', '图片', 'image')
            ->addFormField('url', '跳转链接', 'text', ['tip'=>$this->getUrlTip()])
            ->addFormField('sort', '排序', 'number', ['tip'=>'数字越大越靠前','value'=>0])
            ->addFormField('is_show', '是否显示', 'radio', ['options'=>[0=>'不显示',1=>'显示'],'value'=>1])
            ->addValidate('title', 'require', '标题必须')
            ->addValidate('img', 'require', '图片必须')
            ->addAuto('mpid', get_mpid())
            ->setAddSuccessUrl(create_addon_url('sliderList'))
            ->common_add();
    }

    // 编辑幻灯片
    public function sliderEdit() {
        $this->setMetaTitle('编辑幻灯片')
            ->addSubNav('返回幻灯片列表', create_addon_url('sliderList'), '')
            ->addSubNav('编辑幻灯片', '', 'active')
            ->setModel('weisite_slider')
            ->addFormField('title', '标题', 'text')
            ->addFormField('img', '图片', 'image')
            ->addFormField('url', '跳转链接', 'text', ['tip'=>$this->getUrlTip()])
            ->addFormField('sort', '排序', 'number', ['tip'=>'数字越大越靠前','value'=>0])
            ->addFormField('is_show', '是否显示', 'radio', ['options'=>[0=>'不显示',1=>'显示'],'value'=>1])
            ->addValidate('title', 'require', '标题必须')
            ->addValidate('img', 'require', '图片必须')
            ->setFindMap(['mpid'=>get_mpid(), 'id'=>I('get.id')])
            ->setEditMap(['mpid'=>get_mpid(), 'id'=>I('get.id')])
            ->setEditSuccessUrl(create_addon_url('sliderList'))
            ->common_edit();
    }

    // 删除幻灯片
    public function sliderDelete() {
	    $this->setModel('weisite_slider')
            ->setDeleteMap(['mpid'=>get_mpid(),'id'=>I('get.id')])
            ->setDeleteSuccessUrl(create_addon_url('sliderList'))
            ->common_delete();
    }

	// 分类列表
    public function categoryList() {
	    $this->setMetaTitle('分类列表')
            ->addSubNav('分类列表', '', 'active')
            ->addButton('添加分类', create_addon_url('categoryAdd'), 'btn btn-primary')
			->addListItem('id', 'ID')
            ->addListItem('title', '分类名称')
            ->addListItem('intro', '分类简介')
            ->addListItem('icon', '分类图标', 'image', ['attr'=>'width=60;height=60'])
            ->addListItem('sort', '分类排序')
            ->addListItem('is_show', '是否显示', 'enum', ['options'=>[0=>'不显示',1=>'显示']])
            ->addListItem('index_show', '是否在首页显示', 'enum', ['options'=>[0=>'不显示',1=>'显示']])
            ->addListItem('id', '操作', 'custom', ['options'=>[
                [
                    'title' => '编辑',
                    'url' => create_addon_url('categoryEdit', ['id'=>'{id}']),
                    'class' => 'btn btn-info btn-sm'
                ],
                [
                    'title' => '删除',
                    'url' => create_addon_url('categoryDelete', ['id'=>'{id}']),
                    'class' => 'btn btn-danger btn-sm',
                    'attr' => 'onclick="return confirm(\'确认删除？\')"'
                ]
            ]])
            ->setModel('weisite_category')
            ->setListMap(['mpid'=>get_mpid()])
            ->setListOrder('sort desc')
            ->common_lists();
    }

	// 添加分类
    public function categoryAdd() {
	    $this->setMetaTitle('添加分类')
            ->addSubNav('返回分类列表', create_addon_url('categoryList'), '')
            ->addSubNav('添加分类', '', 'active')
            ->setModel('weisite_category')
            ->addFormField('title', '分类标题', 'text')
            ->addFormField('intro', '分类简介', 'text', ['tip'=>'分类英文名称'])
            ->addFormField('icon', '分类图标', 'image')
            ->addFormField('sort', '分类排序', 'number', ['tip'=>'数字越大排序越靠前','value'=>0])
            ->addFormField('is_show', '是否显示', 'radio', ['options'=>[0=>'不显示',1=>'显示'],'value'=>1])
            ->addFormField('index_show', '是否在首页显示', 'radio', ['options'=>[0=>'不显示',1=>'显示'],'value'=>0])
            ->addFormField('index_show_count', '在首页显示的内容条数', 'text', ['value'=>5])
            ->addFormField('index_show_title_style', '在首页显示的标题样式', 'radio', ['options'=>['style_1'=>'样式一', 'style_2'=>'样式二'],'value'=>'style_1'])
            ->addFormField('index_show_content_style', '在首页显示的内容样式', 'radio', ['options'=>['style_1'=>'样式一', 'style_2'=>'样式二', 'style_3'=>'样式三', 'style_4'=>'样式四', 'style_5'=>'样式五'],'value'=>'style_1'])
            ->addFormField('cate_show_sub', '在分类页是否显示子分类', 'radio', ['options'=>[0=>'不显示',1=>'显示'],'value'=>0])
            ->addFormField('cate_show_style', '在分类页文章列表显示样式', 'radio', ['options'=>['style_1'=>'样式一']])
            ->addValidate('title', 'require', '分类标题必填')
            ->addAuto('mpid', get_mpid())
            ->setAddSuccessUrl(create_addon_url('categoryList'))
            ->common_add();
    }

    // 编辑分类
    public function categoryEdit() {
        $this->setMetaTitle('编辑分类')
            ->addSubNav('返回分类列表', create_addon_url('categoryList'), '')
            ->addSubNav('编辑分类', '', 'active')
            ->setModel('weisite_category')
            ->addFormField('title', '分类标题', 'text')
            ->addFormField('intro', '分类简介', 'text', ['tip'=>'分类英文名称'])
            ->addFormField('icon', '分类图标', 'image')
            ->addFormField('sort', '分类排序', 'number', ['tip'=>'数字越大排序越靠前','value'=>0])
            ->addFormField('is_show', '是否显示', 'radio', ['options'=>[0=>'不显示',1=>'显示'],'value'=>1])
            ->addFormField('index_show', '是否在首页显示', 'radio', ['options'=>[0=>'不显示',1=>'显示'],'value'=>0])
            ->addFormField('index_show_count', '在首页显示的内容条数', 'text', ['value'=>5])
            ->addFormField('index_show_title_style', '在首页显示的标题样式', 'radio', ['options'=>['style_1'=>'样式一', 'style_2'=>'样式二'],'value'=>'style_1'])
            ->addFormField('index_show_content_style', '在首页显示的内容样式', 'radio', ['options'=>['style_1'=>'样式一', 'style_2'=>'样式二', 'style_3'=>'样式三', 'style_4'=>'样式四', 'style_5'=>'样式五'],'value'=>'style_1'])
            ->addFormField('cate_show_sub', '在分类页是否显示子分类', 'radio', ['options'=>[0=>'不显示',1=>'显示'],'value'=>0])
            ->addFormField('cate_show_style', '在分类页文章列表显示样式', 'radio', ['options'=>['style_1'=>'样式一'],'value'=>'style_1'])
            ->addValidate('title', 'require', '分类标题必填')
            ->setFindMap(['mpid'=>get_mpid(), 'id'=>I('get.id')])
            ->setEditMap(['mpid'=>get_mpid(), 'id'=>I('get.id')])
            ->setEditSuccessUrl(create_addon_url('categoryList'))
            ->common_edit();
    }

    // 删除分类
    public function categoryDelete() {
	    $this->setModel('weisite_category')
            ->setDeleteMap(['mpid'=>get_mpid(), 'id'=>I('get.id')])
            ->setDeleteSuccessUrl(create_addon_url('categoryList'))
            ->common_delete();
    }

    // 文章列表
    public function articleList() {
	    $this->setMetaTitle('文章列表')
            ->addSubNav('文章列表', '', 'active')
            ->addButton('添加文章', create_addon_url('articleAdd'), 'btn btn-primary')
            ->setModel('weisite_article')
			->addListItem('id', 'ID')
            ->addListItem('cate_id', '所属分类', 'callback', ['callback_name'=>'getCateName'])
            ->addListItem('title', '文章标题')
            ->addListItem('cover', '封面图片', 'image', ['attr'=>'width=120;height=80'])
            ->addListItem('intro', '文章简介')
            ->addListItem('sort', '排序')
            ->addListItem('is_show', '是否显示', 'enum', ['options'=>[0=>'不显示',1=>'显示']])
            ->addListItem('created_at', '创建时间', 'datetime')
            ->addListItem('id', '操作', 'custom', ['options'=>[
                [
                    'title' => '编辑',
                    'url' => create_addon_url('articleEdit', ['id'=>'{id}']),
                    'class' => 'btn btn-info btn-sm'
                ],
                [
                    'title' => '删除',
                    'url' => create_addon_url('articleDelete', ['id'=>'{id}']),
                    'class' => 'btn btn-danger btn-sm',
                    'attr' => 'onclick="return confirm(\'确认删除\')"'
                ]
            ]])
            ->setListMap(['mpid'=>get_mpid()])
            ->common_lists();
    }

    // 添加文章
    public function articleAdd() {
	    $this->setMetaTitle('添加文章')
            ->setTip('添加文章前请确保已添加了文章分类。<a href="'.create_addon_url('categoryAdd').'">点此添加分类</a>')
            ->addSubNav('返回文章列表', create_addon_url('articleList'), '')
            ->addSubNav('添加文章', '', 'active')
            ->addFormField('title', '文章标题', 'text')
            ->addFormField('cate_id', '所属分类', 'select', ['options'=>'callback', 'callback_name'=>'getCates'])
            ->addFormField('cover', '封面图片', 'image')
            ->addFormField('intro', '文章简介', 'textarea')
            ->addFormField('sort', '排序', 'number', ['tip'=>'数字越大越靠前', 'value'=>0])
            ->addFormField('is_show', '是否显示', 'radio', ['options'=>[0=>'不显示',1=>'显示'],'value'=>1])
            ->addFormField('content', '文章内容', 'editor')
            ->addValidate('title', 'require', '文章标题必须')
            ->addValidate('cate_id', 'require', '请选择文章所属分类')
            ->addAuto('mpid', get_mpid())
            ->addAuto('created_at', time())
            ->setModel('weisite_article')
            ->setAddSuccessUrl(create_addon_url('articleList'))
            ->common_add();
    }

    // 编辑文章
    public function articleEdit() {
        $this->setMetaTitle('编辑文章')
            ->addSubNav('返回文章列表', create_addon_url('articleList'), '')
            ->addSubNav('编辑文章', '', 'active')
            ->addFormField('title', '文章标题', 'text')
            ->addFormField('cate_id', '所属分类', 'select', ['options'=>'callback', 'callback_name'=>'getCates'])
            ->addFormField('cover', '封面图片', 'image')
            ->addFormField('intro', '文章简介', 'textarea')
            ->addFormField('sort', '排序', 'number', ['tip'=>'数字越大越靠前', 'value'=>0])
            ->addFormField('is_show', '是否显示', 'radio', ['options'=>[0=>'不显示',1=>'显示'],'value'=>1])
            ->addFormField('content', '文章内容', 'editor')
            ->addValidate('title', 'require', '文章标题必须')
            ->addValidate('cate_id', 'require', '请选择文章所属分类')
            ->setModel('weisite_article')
            ->setFindMap(['mpid'=>get_mpid(), 'id'=>I('get.id')])
            ->setEditMap(['mpid'=>get_mpid(), 'id'=>I('get.id')])
            ->setEditSuccessUrl(create_addon_url('articleList'))
            ->common_edit();
    }

    // 删除文章
    public function articleDelete() {
	    $this->setModel('weisite_article')
            ->setDeleteMap(['mpid'=>get_mpid(), 'id'=>I('get.id')])
            ->setDeleteSuccessUrl(create_addon_url('articleList'))
            ->common_delete();
    }

    // 页面列表
    public function pageList() {
        $this->setMetaTitle('页面列表')
            ->addSubNav('页面列表', '', 'active')
            ->addButton('添加页面', create_addon_url('pageAdd'), 'btn btn-primary')
            ->setModel('weisite_page')
			->addListItem('id', 'ID')
            ->addListItem('title', '页面标题')
            ->addListItem('cover', '封面图片', 'image', ['attr'=>'width=120;height=80'])
            ->addListItem('intro', '页面简介')
            ->addListItem('created_at', '创建时间', 'datetime')
            ->addListItem('id', '操作', 'custom', ['options'=>[
                [
                    'title' => '编辑',
                    'url' => create_addon_url('pageEdit', ['id'=>'{id}']),
                    'class' => 'btn btn-info btn-sm'
                ],
                [
                    'title' => '删除',
                    'url' => create_addon_url('pageDelete', ['id'=>'{id}']),
                    'class' => 'btn btn-danger btn-sm',
                    'attr' => 'onclick="return confirm(\'确认删除\')"'
                ]
            ]])
            ->setListMap(['mpid'=>get_mpid()])
            ->common_lists();
    }

    // 添加页面
    public function pageAdd() {
        $this->setMetaTitle('添加页面')
            ->addSubNav('返回页面列表', create_addon_url('pageList'), '')
            ->addSubNav('添加页面', '', 'active')
            ->addFormField('title', '页面标题', 'text')
            ->addFormField('cover', '封面图片', 'image')
            ->addFormField('intro', '页面简介', 'textarea')
            ->addFormField('content', '页面内容', 'editor')
            ->addValidate('title', 'require', '页面标题必须')
            ->addAuto('mpid', get_mpid())
            ->addAuto('created_at', time())
            ->setModel('weisite_page')
            ->setAddSuccessUrl(create_addon_url('pageList'))
            ->common_add();
    }

    // 编辑页面
    public function pageEdit() {
        $this->setMetaTitle('编辑页面')
            ->addSubNav('返回页面列表', create_addon_url('pageList'), '')
            ->addSubNav('编辑页面', '', 'active')
            ->addFormField('title', '页面标题', 'text')
            ->addFormField('cover', '封面图片', 'image')
            ->addFormField('intro', '页面简介', 'textarea')
            ->addFormField('content', '页面内容', 'editor')
            ->addValidate('title', 'require', '页面标题必须')
            ->setModel('weisite_page')
            ->setFindMap(['mpid'=>get_mpid(), 'id'=>I('get.id')])
            ->setEditMap(['mpid'=>get_mpid(), 'id'=>I('get.id')])
            ->setEditSuccessUrl(create_addon_url('pageList'))
            ->common_edit();
    }

    // 删除页面
    public function pageDelete() {
        $this->setModel('weisite_page')
            ->setDeleteMap(['mpid'=>get_mpid(), 'id'=>I('get.id')])
            ->setDeleteSuccessUrl(create_addon_url('pageList'))
            ->common_delete();
    }

    // 导航列表
    public function navigationList() {
	    $listData = [];
	    $pid = I('pid', 1, 'intval');	// 1：首页导航 2：底部导航
		$data = M('weisite_navigation')->where([
			'mpid' => get_mpid(),
			'pid' => $pid,
			'is_show' => 1
		])->order('sort desc')->select();
		$listData = $data;
		$tip = $pid == 2 ? '如果开启了展示底部导航，则会使用此处添加的导航内容在页面底部显示' : '如果开启了首页导航展示，则会使用此处添加的导航内容在首页显示';
	    $this->setMetaTitle('首页导航')
            ->setSubNav([
            	[
            		'title' => '首页导航',
					'url' => create_addon_url('navigationList', ['pid'=>1]),
					'class' => $pid == 2 ? '' : 'active'
				],
				[
					'title' => '底部导航',
					'url' => create_addon_url('navigationList', ['pid'=>2]),
					'class' => $pid == 2 ? 'active' : ''
				]
			])
            ->addButton('添加导航', create_addon_url('navigationAdd', ['pid'=>$pid]), 'btn btn-primary')
            ->setTip($tip)
            ->addListItem('title', '导航标题')
            ->addListItem('url', '跳转地址')
            ->addListItem('sort', '排序')
            ->addListItem('is_show', '是否显示', 'enum', ['options'=>[0=>'不显示',1=>'显示']])
            ->addListItem('id', '操作', 'custom', ['options'=>[
                [
                    'title' => '编辑',
                    'url' => create_addon_url('navigationEdit', ['id'=>'{id}', 'pid'=>$pid]),
                    'class' => 'btn btn-info btn-sm'
                ],
                [
                    'title' => '删除',
                    'url' => create_addon_url('navigationDelete', ['id'=>'{id}', 'pid'=>$pid]),
                    'class' => 'btn btn-danger btn-sm',
                    'attr' => 'onclick="return confirm(\'确认删除？\')"'
                ]
            ]])
            ->addListItem('pid', '父级ID', 'hidden')
            ->setListData($listData)
            ->common_lists();
    }

    // 添加导航
    public function navigationAdd() {
		$pid = I('pid', 1, 'intval');
	    $this->setMetaTitle('添加导航')
            ->addSubNav('返回导航列表', create_addon_url('navigationList'), '')
            ->addSubNav('添加导航', '', 'active')
            ->addFormField('title', '导航标题', 'text')
            ->addFormField('pid', '父级ID', 'hidden', ['options'=>'callback', 'callback_name'=>'getNavs', 'callback_params'=>I('get.pid'), 'value'=>I('get.pid')])
            ->addFormField('icon', '导航图标', 'image')
            ->addFormField('selected_icon', '选中时的导航图标', 'image')
            ->addFormField('url', '跳转地址', 'text', ['tip'=>$this->getUrlTip()])
            ->addFormField('intro', '导航说明', 'textarea')
            ->addFormField('sort', '排序', 'number', ['tip'=>'数字越大排序越靠前', 'value'=>0])
            ->addFormField('is_show', '是否显示', 'radio', ['options'=>[0=>'不显示',1=>'显示'],'value'=>1])
            ->addValidate('title', 'require', '标题必须')
            ->addAuto('mpid', get_mpid())
			->addAuto('pid', $pid)
            ->setModel('weisite_navigation')
            ->setAddSuccessUrl(create_addon_url('navigationList', ['pid'=>$pid]))
            ->common_add();
    }

    // 编辑导航
    public function navigationEdit() {
		$pid = I('pid', 1, 'intval');
        $this->setMetaTitle('编辑导航')
            ->addSubNav('返回导航列表', create_addon_url('navigationList'), '')
            ->addSubNav('编辑导航', '', 'active')
            ->addFormField('title', '导航标题', 'text')
            ->addFormField('pid', '父级ID', 'hidden', ['options'=>'callback', 'callback_name'=>'getNavs','callback_params'=>I('get.pid'), 'value'=>I('get.pid')])
            ->addFormField('icon', '导航图标', 'image')
			->addFormField('selected_icon', '选中时的导航图标', 'image')
            ->addFormField('url', '跳转地址', 'text', ['tip'=>$this->getUrlTip()])
            ->addFormField('intro', '导航说明', 'textarea')
            ->addFormField('sort', '排序', 'number', ['tip'=>'数字越大排序越靠前', 'value'=>0])
            ->addFormField('is_show', '是否显示', 'radio', ['options'=>[0=>'不显示',1=>'显示'],'value'=>1])
			->addFormField('pid', '导航类型', 'hidden', ['value'=>$pid])
            ->addValidate('title', 'require', '标题必须')
            ->setModel('weisite_navigation')
            ->setFindMap(['mpid'=>get_mpid(), 'id'=>I('get.id')])
            ->setEditMap(['mpid'=>get_mpid(), 'id'=>I('get.id')])
            ->setEditSuccessUrl(create_addon_url('navigationList', ['pid'=>$pid]))
            ->common_edit();
    }

    // 删除导航
    public function navigationDelete() {
	    $id = I('id', 0, 'intval');
	    $pid = I('pid', 1, 'intval');
	    if (empty($id)) {
	        $this->error('请求参数有误');
        }
	    $this->setModel('weisite_navigation')
            ->setDeleteMap([
                'mpid'=>get_mpid(),
                'id'=>$id
            ])
            ->setDeleteSuccessUrl(create_addon_url('navigationList', ['pid'=>$pid]))
            ->common_delete();
    }

    // 模板列表
    public function templateList() {
	    $this->setMetaTitle('模板列表')
            ->addSubNav('模板列表', '', 'active')
            ->addButton('添加模板', create_addon_url('templateAdd'), 'btn btn-primary')
            ->common_lists();
    }


}