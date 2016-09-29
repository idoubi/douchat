<?php

namespace Mp\Controller;
use Mp\Controller\BaseController;
use WechatSdk\Wechat;

/**
 * 素材管理控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class MaterialController extends BaseController {

    /**
     * 文本素材列表
     * @author 艾逗笔<765532665@qq.com>
     */
    public function text() {
        $count = M('mp_material')->where(array('mpid'=>get_mpid(),'type'=>'text'))->count();
        $page = max(1, intval(I('p')));
        $per = 50;
        $lists = M('mp_material')->where(array('mpid'=>get_mpid(),'type'=>'text'))->order('create_time desc')->page($page.','.$per)->select();
        $pagination = pagination($count, $per);
        $this->addCrumb('公众号管理', U('Index/index'), '')
             ->addCrumb('素材管理', U('Material/text'), '')
             ->addCrumb('文本素材', '', 'active')
             ->addNav('文本素材', '', 'active')
             ->addNav('图片素材', U('Material/image'), '')
             ->addNav('图文素材', U('Material/news'), '')
             ->addButton('添加文本素材', U('Material/add?type=text'), 'btn btn-primary')
             ->assign('lists', $lists)
             ->assign('pagination', $pagination)
             ->display();
    }

	/**
	 * 图片素材列表
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function image() {
        $page = max(1, intval(I('p')));
        $count = M('mp_material')->where(array('mpid'=>get_mpid(),'type'=>'image'))->count();
        $per = 50;
        $lists = M('mp_material')->where(array('mpid'=>get_mpid(),'type'=>'image'))->order('create_time desc')->page($page.','.$per)->select();
        $pagination = pagination($count, $per);
        $this->addCrumb('公众号管理', U('Index/index'), '')
             ->addCrumb('素材管理', U('Material/text'), '')
             ->addCrumb('图片素材', '', 'active')
             ->addNav('文本素材', U('Material/text'), '')
             ->addNav('图片素材', '', 'active')
             ->addNav('图文素材', U('Material/news'), '')
             ->addButton('添加图片素材', U('Material/add?type=image'), 'btn btn-primary')
             ->addButton('一键拉取公众号图片素材', U('Material/pull?type=image'), 'btn btn-success', 'target="_blank"')
             ->assign('lists', $lists)
             ->assign('pagination', $pagination)
             ->display();
	}

    /**
     * 图文素材列表
     * @author 艾逗笔<765532665@qq.com>
     */
    public function news() {
        $page = max(1, intval(I('p')));
        $count = M('mp_material')->where(array('mpid'=>get_mpid(),'type'=>'news'))->count();
        $per = 50;
        $lists = M('mp_material')->where(array('mpid'=>get_mpid(),'type'=>'news'))->order('create_time desc')->page($page.','.$per)->select();
        $pagination = pagination($count, $per);
        $this->addCrumb('公众号管理', U('Index/index'), '')
             ->addCrumb('素材管理', U('Material/text'), '')
             ->addCrumb('图文素材', '', 'active')
             ->addNav('文本素材', U('Material/text'), '')
             ->addNav('图片素材', U('Material/image'), '')
             ->addNav('图文素材', '', 'active')
             ->addButton('添加图文素材', U('Material/add?type=news'), 'btn btn-primary')
             ->assign('lists', $lists)
             ->assign('pagination', $pagination)
             ->display();
    }

    /**
     * 添加素材
     * @author 艾逗笔<765532665@qq.com>
     */
    public function add() {
        $type = I('get.type');
        $type_arr = array('text'=>'文本素材','image'=>'图片素材','news'=>'图文素材');
        $this->addCrumb('公众号管理', U('Index/index'), '')
             ->addCrumb('素材管理', U('Material/text'), '')
             ->addCrumb('添加'.$type_arr[$type], '', 'active')
             ->addNav('添加'.$type_arr[$type], '', 'active');
        switch ($type) {
            case 'text':
                $this->addFormField('content', '文本内容', 'textarea');
                break;
            case 'image':
                $this->addFormField('image', '图片', 'image');    
                break;
            case 'news':
                $this->addFormField('title', '图文标题', 'text')
                     ->addFormField('picurl', '图文封面', 'image')
                     ->addFormField('description', '图文描述', 'textarea')
                     ->addFormField('detail', '图文详情', 'editor')
                     ->addFormField('url', '图文链接', 'text');
                break;
            default:
                # code...
                break;
        }
        $this->setModel('mp_material')
             ->addValidate('content','require','文本内容必填')
             ->addValidate('image','require','图片必须')
             ->addValidate('title','require','图文标题必填')
             ->addAuto('mpid', 'get_mpid', 1, 'function')
             ->addAuto('type', $type, 1, 'string')
             ->addAuto('create_time', 'time', 1, 'function')
             ->setAddSuccessUrl(U('Material/'.$type))
             ->common_add();
    }

    /**
     * 编辑素材
     * @author 艾逗笔<765532665@qq.com>
     */
    public function edit() {
        $material_id = I('get.id');
        $material = M('mp_material')->where(array('mpid'=>get_mpid(),'id'=>$material_id))->find();
        if (!$material) {
            $this->error('素材不存在');
        }
        $type_arr = array('text'=>'文本素材','image'=>'图片素材','news'=>'图文素材');
        $this->addCrumb('公众号管理', U('Index/index'), '')
             ->addCrumb('素材管理', U('Material/text'), '')
             ->addCrumb('编辑'.$type_arr[$material['type']], '', 'active')
             ->addNav('编辑'.$type_arr[$material['type']], '', 'active');
        switch ($material['type']) {
            case 'text':
                $this->addFormField('content', '文本内容', 'textarea');
                break;
            case 'image':
                $this->addFormField('image', '图片', 'image');
                break;
            case 'news':
                $this->addFormField('title', '图文标题', 'text')
                     ->addFormField('picurl', '图文封面', 'image')
                     ->addFormField('description', '图文描述', 'textarea')
                     ->addFormField('detail', '图文详情', 'editor')
                     ->addFormField('url', '图文链接', 'text');
                break;
            default:
                # code...
                break;
        }
        $this->setModel('mp_material')
             ->addValidate('content','require','文本内容必填')
             ->addValidate('image','require','图片必须')
             ->addValidate('title','require','图文标题必填')
             ->setFormData($material)
             ->setEditMap(array('id'=>I('get.id')))
             ->setEditSuccessUrl(U('Material/'.$material['type']))
             ->common_edit();
    }

    /**
     * 删除素材
     * @author 艾逗笔<765532665@qq.com>
     */
    public function delete() {
        $material_id = I('get.id');
        if (!M('mp_material')->where(array('mpid'=>get_mpid(),'id'=>$material_id))->find()) {
            $this->error('素材不存在');
        } else {
            if (!M('mp_material')->where(array('mpid'=>get_mpid(),'id'=>$material_id))->delete()) {
                $this->error('删除素材失败');
            } else {
                $this->success('删除素材成功');
            }
        }
    }

    /**
     * 下载微信素材库里面的图片到本地
     * @author 艾逗笔<765532665@qq.com>
     */
    public function download_image_from_wechat() {
        $wechatInfo = get_mp_info();
        $options = array(
            'token'             =>  $wechatInfo['valid_token'],                 
            'encodingaeskey'    =>  $wechatInfo['encodingaeskey'],      
            'appid'             =>  $wechatInfo['appid'],               
            'appsecret'         =>  $wechatInfo['appsecret']            
        );
        $wechatObj = new Wechat($options);
        $images = $wechatObj->getForeverList('image', 0, 10);
        $upload_time = time();
        $upload_path = './Uploads/Pictures/' . date('Ymd', $upload_time) . '/';
        if (!file_exists($upload_path)) {
            $dirs = explode('/', $upload_path);
            $dir = $dirs[0] . '/';
            for ($i=1, $j=count($dirs)-1; $i<$j; $i++) {
                $dir .= $dirs[$i] . '/';
                if (!is_dir($dir)) {
                    mkdir($dir, 0777);
                }
            }
        }
        foreach ($images['item'] as $k => $v) {
            $file_extension = substr($v['url'], intval(strpos($v['url'], '='))+1);   
            $file_name = md5($v['media_id']) . '.'  . $file_extension;
            $file_path = $upload_path . $file_name;
            if ($v['url']) {
                $file_contents = file_get_contents($v['url']);
            } else {
                $file_contents = $wechatObj->getForeverMedia($v['media_id']);
            }
            $create_time = time();
            $file_size = file_put_contents($file_path, $file_contents);
            $attach['mpid'] = get_mpid();
            $attach['user_id'] = get_user_id();
            $attach['file_name'] = $file_name;
            $attach['file_extension'] = $file_extension;
            $attach['file_size'] = $file_size;
            $attach['file_path'] = $file_path;
            $attach['hash'] = md5_file($file_path);
            $attach['create_time'] = $create_time;
            $attach['item_type'] = 'image';
            if (M('attach')->where(array('mpid'=>get_mpid(),'hash'=>$attach['hash']))->find()) {
                M('attach')->where(array('mpid'=>get_mpid(),'hash'=>$attach['hash']))->save($attach);
            } else {
                M('attach')->add($attach);
            }

            $material['mpid'] = get_mpid();
            $material['type'] = 'image';
            $material['image_name'] = $file_name;
            $material['image_url'] = $file_path;
            $material['media_id'] = $v['media_id'];
            $material['from'] = 'wechat';
            $material['create_time'] = $create_time;
            if (M('mp_material')->where(array('mpid'=>get_mpid(),'type'=>'image','media_id'=>$material['media_id']))->find()) {
                M('mp_material')->where(array('mpid'=>get_mpid(),'type'=>'image','media_id'=>$material['media_id']))->save($material);
            } else {
                M('mp_material')->add($material);
            }
        }
        $this->success('同步素材成功');
    }

    /**
     * 上传附件
     * @author 艾逗笔<765532665@qq.com>
     */
	public function upload() {
        import('Org.Util.UploadFile');
        $upload_time = time();
        $upload_path = './Uploads/Pictures/' . date('Ymd', $upload_time) . '/';
        if (!file_exists($upload_path)) {
            $dirs = explode('/', $upload_path);
            $dir = $dirs[0] . '/';
            for ($i=1, $j=count($dirs)-1; $i<$j; $i++) {
                $dir .= $dirs[$i] . '/';
                if (!is_dir($dir)) {
                    mkdir($dir, 0777);
                }
            }
        }
        $upload = new \UploadFile();
        $upload->maxSize  = 1024*20*1000;
        $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');
        $upload->savePath = $upload_path;
        if(!$upload->upload()) {
            $return['errcode'] = 0;
            $return['errmsg'] = $upload->getErrorMsg();
            $this->ajaxReturn($return);
        }else{
            //上传成功,将信息存入数据库
            $info =  $upload->getUploadFileInfo();
            $data['mpid'] = get_mpid();
            $data['user_id'] = $this->user_id;
            $data['file_name'] = $info[0]['name'];
            $data['file_extension'] = $info[0]['extension'];
            $data['file_size'] = $info[0]['size'];
            $data['file_path'] = $info[0]['savepath'] . $info[0]['savename'];
            $data['hash'] = $info[0]['hash'];
            $data['create_time'] = $upload_time;
            $data['item_type'] = 'image';
			$Attach = D('Attach');
            $attach_id = $Attach->add($data);
            if (!$attach_id) {
                $return['errcode'] = 0;
                $return['errmsg'] = '保存附件失败';
                $this->ajaxReturn($return);
            } else {
                $data['attach_id'] = $attach_id;
                $data['file_path'] = tomedia($data['file_path']);
                $return['errcode'] = 1;
                $return['errmsg'] = '保存附件成功';
                $return['data'] = $data;
                $this->ajaxReturn($return);
            }
        }

	}

    /**
     * 获取图片
     * @author 艾逗笔<765532665@qq.com>
     */
    public function get_image_list(){
        $page_num = (int)I("page_num", 1);
        //默认每次返回18条图片信息数据
        $limit = 18;
        $Attach = D('Attach');
        // if (get_mpid() > 0) {
        //     $map['mpid'] = get_mpid();
        // }
        $map['user_id'] = get_user_id();
        $map['item_type'] = 'image';
        $image_list = $Attach->where($map)->order('id desc')->limit(($page_num-1)*$limit, $limit)->select();
        foreach ($image_list as $k => &$v) {
            $v['file_path'] = tomedia($v['file_path']);
        }
		$total_count = $Attach->where($map)->count();
        $this->ajaxReturn(array(
			'image_list'=>$image_list,
            'pagination_data'=>array('total_pages'=>ceil($total_count/$limit), 'current_page'=>$page_num)
        ), 'JSON');
    }

    /**
     * 删除附件
     * @author 艾逗笔<765532665@qq.com>
     */
    public function delete_attach() {
        $map['mpid'] = get_mpid();
        $map['id'] = I('attach_id');
        $res = M('Attach')->where($map)->delete();
        if ($res) {
            $return['errcode'] = 1;
            $return['errmsg'] = '删除成功';
            $this->ajaxReturn($return);
        }
    }

    // markdown图片上传
    public function markdown_picupload(){
        global $_G;
        import('Org.Util.UploadFile');
        $upload_time = time();
        $upload_path = './Uploads/Pictures/' . date('Ymd', $upload_time) . '/';
        if (!file_exists($upload_path)) {
            $dirs = explode('/', $upload_path);
            $dir = $dirs[0] . '/';
            for ($i=1, $j=count($dirs)-1; $i<$j; $i++) {
                $dir .= $dirs[$i] . '/';
                if (!is_dir($dir)) {
                    mkdir($dir, 0777);
                }
            }
        }
        $upload = new \UploadFile();
        $upload->maxSize  = 1024*20*1000;
        $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');
        $upload->savePath = $upload_path;
        if(!$upload->upload()) {
            $result['success']=0;
            $result['message']="图片上传失败！";
            $result['url']='';
            echo json_encode($result);
        }else{
            //上传成功,将信息存入数据库
            $info =  $upload->getUploadFileInfo();
            $data['mpid'] = get_mpid();
            $data['user_id'] = $this->user_id;
            $data['file_name'] = $info[0]['name'];
            $data['file_extension'] = $info[0]['extension'];
            $data['file_size'] = $info[0]['size'];
            $data['file_path'] = $info[0]['savepath'] . $info[0]['savename'];
            $data['hash'] = $info[0]['hash'];
            $data['create_time'] = $upload_time;
            $data['item_type'] = 'image';
            $Attach = D('Attach');
            $attach_id = $Attach->add($data);
            
            $imgUrl = $info[0]['savepath'] . $info[0]['savename'];
            $imgUrl = tomedia($imgUrl);
            $result['success']=1;
            $result['message']="图片上传成功！";
            $result['url']=$imgUrl;
            echo json_encode($result);
        }
    }

    /**
     * 拉取公众号素材
     */
    public function pull() {
        $this->success('正在拉取中。。。');
    }
}

 ?>
