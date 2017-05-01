<?php 

namespace Mp\Controller;
use Think\Controller;
use WechatSdk\Wechat;

/**
 * 公众号接口控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class ApiController extends Controller {

	private $options = array(
    	'token'				=>	'', 				
    	'encodingaeskey'	=>	'', 		
    	'appid'				=>	'', 				
    	'appsecret'			=>	'' 			
    );

    /**
     * 公众号通信入口
     * @author 艾逗笔<765532665@qq.com>
     */
	public function index($id) {
        if (empty($_GET['echostr']) && empty($_GET["signature"]) && empty ($_GET["nonce"])) {   // 禁止在浏览器直接打开接口
            echo 'Access denied';
            exit();
        }
        if (!is_numeric($id)) {     // 接口验证传递的参数支持mpid和token两种
            $id = M('mp')->where(array('token'=>$id))->getField('id');
        }
		$this->mpinfo = $wechatInfo = M('mp')->where(array('id'=>$id))->find();   // 获取公众号信息
		$this->options['token'] = $wechatInfo['valid_token'];
		$this->options['encodingaeskey'] = $wechatInfo['encodingaeskey'];
		if (!empty($_GET['echostr']) && !empty($_GET["signature"]) && !empty ($_GET["nonce"])) {  // 微信接入验证
			$wechatObj = new Wechat($this->options);
	        $wechatObj->valid();
	        exit();
		}

		$wechatObj = new Wechat($this->options);                // 实例化微信接口类
        $wechatObj->getRev();                                   // 微信接口初始化

        $origin_message = $wechatObj->getRevData();             // 获取用户消息
        foreach ($origin_message as $k => &$v) {                // 兼容大小写
            $origin_message[strtolower($k)] = $v;
        }
        $this->message = $origin_message;
        $this->msgtype = $this->message['MsgType'];             // 获取消息类型
        $this->mpid = $this->mpinfo['id'];                      // 获取当前公众号ID
        $this->token = $this->mpinfo['token'];                  // 获取当前公众号token
        $this->openid = $this->message['FromUserName'];         // 获取当前用户openid
        
        get_mpid($this->mpid);                                  // 缓存当前公众号id
        get_openid($this->openid);                              // 缓存用户openid
        
        $this->fans_info = get_fans_info($this->openid);        // 获取粉丝表内的粉丝信息
        $this->mp_settings = D('MpSetting')->get_settings();    // 获取公众号全局设置

        if ($this->event != 'unsubscribe') {
            D('MpFans')->save_fans_info($this->openid);             // 保存粉丝信息
            D('MpMessage')->save_message($this->message);           // 保存消息
        }
        
        if (!empty($this->mp_settings['fans_bind_on']) && !$this->fans_info['is_bind']) {         // 开启了用户绑定且用户还未绑定
            $bind_url = U('Mp/MobileBase/fans_bind@'.C('HTTP_HOST'), array('openid'=>$this->openid, 'mpid'=>$this->mpid));
            reply_text('你需要先<a href="'.$bind_url.'">绑定个人信息</a>才能继续使用其他功能');
            exit();
        }

        switch ($this->msgtype) {
            case 'text':
                $keyword = $this->message['content'];
                $this->respond_keyword($keyword, $this->message);
                break;
            case 'image':
                $this->respond_special('image', $this->message);       
                break;
            case 'voice':
                $this->respond_special('voice', $this->message);            
                break;
            case 'video':
                $this->respond_special('video', $this->message);    
                break;
            case 'shortvideo':
                $this->respond_special('shortvideo', $this->message);
                break;
            case 'location':
                $this->respond_special('location', $this->message);
                break;
            case 'link':
                $this->respond_special('link', $this->message);
                break;
            case 'event':
                $this->end_context();                                   // 事件消息不响应上下文
                $this->event = strtolower($this->message['Event']);     // 获取事件类型
                if ($this->event == 'location') {
                    $this->event = 'report_location';                   // 将上报地理位置事件转化为report_location
                }
                switch ($this->event) {
                    case 'subscribe':
                        $this->respond_event('subscribe', $this->message);
                        break;
                    case 'unsubscribe':
                        $this->respond_event('unsubscribe', $this->message);
                        break;
                    case 'scan':
                        $this->respond_event('scan', $this->message);
                        break;
                    case 'report_location':     // 上报地理位置事件
                        $this->respond_event('report_location', $this->message);
                        break;
                    case 'click':
                        $this->respond_event('click', $this->message);
                        break;
                    case 'view':
                        $this->respond_event('view', $this->message);
                        break;
                    default:
                        # code...
                        break;
                }
                break;
            default:
                # code...
                break;
        }

        if (!$this->get_context()) {
            // 触发未识别回复
            $this->respond_special('unrecognize', $this->message);
        }
        
	}

    /**
     * 设置消息上下文
     * @author 艾逗笔<765532665@qq.com>
     */
    protected function begin_context($expire = 300, $context=array()) {
        $context['addon'] = $this->addon;
        S('context_'.get_openid(), $this->addon, $expire);
        if (count($context) > 0) {
            S('context_'.get_openid().'_context', $context, $expire);       // 缓存上下文消息内容
        }
    }

    /**
     * 保持消息上下文
     * @author 艾逗笔<765532665@qq.com>
     */
    protected function keep_context($expire = 300, $context=array()) {
        $context['addon'] = $this->addon;
        S('context_'.get_openid(), $this->addon, $expire);
        if (count($context) > 0) {
            S('context_'.get_openid().'_context', $context, $expire);       // 缓存上下文消息内容
        }
    }

    /**
     * 删除消息上下文
     * @author 艾逗笔<765532665@qq.com>
     */
    protected function end_context() {
        S('context_'.get_openid(), NULL);
        S('context_'.get_openid().'_context', NULL); 
    }

    /**
     * 获取上下文消息
     * @author 艾逗笔<765532665@qq.com>
     */
    protected function get_context() {
        return S('context_'.get_openid().'_context');
    }

    /**
     * 分发消息到插件进行处理
     * @author 艾逗笔<765532665@qq.com>
     */
    private function respond_addon($addon, $message) {
        if (!D('Addons')->is_addon_forbidden($this->addon, $this->mpid)) {
            $respond = A('Addons://'.$this->addon.'/Respond');
            if (method_exists($respond, 'wechat')) {
                $respond->wechat($this->message);
            }
        }
    }

    /**
     * 响应关键词
     * @author 艾逗笔<765532665@qq.com>
     */
    private function respond_keyword($keyword, $message) {
        if (S('context_'.get_openid())) {       // 消息上下文存在，分发消息到插件进行处理
            $this->in_context = 1;
            $this->addon = S('context_'.get_openid());
            $this->addon_settings = D('AddonSetting')->get_addon_settings($this->addon, $this->mpid);
            $respond = A('Addons://'.$this->addon.'/Respond');
            if (method_exists($respond, 'wechat')) {
                $respond->wechat($this->message);
            }
            exit();
        } else {
            $this->in_context = 0;
        }
        $MpRule = D('MpRule');
        $MpAutoReply = D('MpAutoReply');
        $auto_reply_rule = $MpRule->get_keyword_rule($keyword, 'auto_reply');   // 获取自动回复规则
        if ($auto_reply_rule) {                         // 响应自动回复规则
            $reply_id = $auto_reply_rule['reply_id'];
            $auto_reply = $MpAutoReply->get_auto_reply($reply_id);
            if ($auto_reply['errcode'] == 0) {
                $result = $auto_reply['result'];
                $reply_type = $result['reply_type'];
                switch ($reply_type) {
                    case 'text':
                        $content = $result['content'];
                        reply_text($content);
                        break;
                    case 'image':
                        $articles[0] = array(
                            'Title' => '这是一张图片',
                            'Description' => '点击查看大图',
                            'PicUrl' => $result['image'],
                            'Url' => $result['image']
                        );
                        reply_news($articles);
                        break;
                    case 'news':
                        $articles[0] = array(
                            'Title' => $result['title'],
                            'Description' => $result['description'],
                            'PicUrl' => $result['picurl'],
                            'Url' => $result['url']
                        );
                        reply_news($articles);
                        break;
                    default:
                        # code...
                        break;
                }
            }
            exit();
        }

        $entry_rule = $MpRule->get_keyword_rule($keyword, 'entry');
        if ($entry_rule) {      // 响应封面入口
            $this->addon = $entry_rule['addon'];
            if (!D('Addons')->is_addon_forbidden($this->addon, $this->mpid)) {
                $entry_id = $entry_rule['entry_id'];
                $entry = D('AddonEntry')->get_entry_info($entry_id);
                $articles[0] = array(
                    'Title' => $entry['title'],
                    'Description' => $entry['desc'],
                    'PicUrl' => tomedia($entry['cover']),
                    'Url' => U('/addon/'.$this->addon.'/mobile/'.$entry['act'].'@'.C('HTTP_HOST'), array('mpid'=>$this->mpid, 'openid'=>$this->openid))
                );
                reply_news($articles);
                exit();
            }
        }

        $respond_rule = $MpRule->get_keyword_rule($keyword, 'respond');
        if ($respond_rule) {        // 分发消息到插件进行处理
            $this->addon = $respond_rule['addon'];
            $this->respond_addon($this->addon, $this->message);
            exit();
        }
    }

    /**
     * 响应特殊消息
     * @param $type 特殊消息类型 image/voice/video/shortvideo/location/link
     * @author 艾逗笔<765532665@qq.com>
     */
    public function respond_special($type, $message) {
        if (S('context_'.get_openid())) {       // 消息上下文存在
            $this->in_context = 1;
            $this->addon = S('context_'.get_openid());
            $this->addon_settings = D('AddonSetting')->get_addon_settings($this->addon, $this->mpid);
            $respond = A('Addons://'.$this->addon.'/Respond');
            if (method_exists($respond, 'wechat')) {
                $respond->wechat($this->message);
            }
        } else {
            $this->in_context = 0;
        }
        $auto_reply = D('MpAutoReply')->get_auto_reply_by_type($type);
        $reply_type = $auto_reply['reply_type'];
        switch ($reply_type) {
            case 'keyword':                 // 绑定关键词
                $keyword = $auto_reply['keyword'];
                $this->respond_keyword($keyword);       // 响应关键词
                break;
            case 'addon':
                $this->addon = $auto_reply['addon'];    // 分发到插件进行处理
                $this->respond_addon($this->addon, $this->message);
                break;
            default:
                # code...
                break;
        }
    }

    /**
     * 响应事件
     * @param $type 事件类型 subscribe/unsubscribe/scan/report_location/click/view
     * @author 艾逗笔<765532665@qq.com>
     */
    public function respond_event($type, $message) {
        switch ($type) {
            case 'subscribe':           // 用户关注事件
                if (!M('mp_score_record')->where(array('mpid'=>$this->mpid,'openid'=>$this->openid,'source'=>'system','flag'=>'subscribe'))->find()) {
                    if ($this->mp_settings['fans_init_integral']) {
                        add_score($this->mp_settings['fans_init_integral'],'用户首次关注公众号','score','subscribe','system');
                    }
                    if ($this->mp_settings['fans_init_money']) {
                        add_score($this->mp_settings['fans_init_money'],'用户首次关注公众号','money','subscribe','system');
                    }
                }
                if ($this->message['EventKey'] && $this->message['Ticket']) {
                    $scene_qrcode = M('scene_qrcode')->where(array('mpid'=>$this->mpid,'ticket'=>get_rev_ticket()))->find();
                    if ($scene_qrcode) {
                        $scan_data['mpid'] = $this->mpid;
                        $scan_data['openid'] = $this->openid;
                        $scan_data['qrcode_id'] = $scene_qrcode['id'];
                        $scan_data['scene_name'] = $scene_qrcode['scene_name'];
                        $scan_data['keyword'] = $scene_qrcode['keyword'];
                        $scan_data['scene_id'] = get_rev_scene_id();
                        $scan_data['scan_type'] = 'subscribe';
                        $scan_data['ctime'] = $this->message['CreateTime'];
                        M('scene_qrcode_statistics')->add($scan_data);
                        $keyword = $scene_qrcode['keyword'];
                        $this->respond_keyword($keyword, $this->message);
                    }
                }
                break;
            case 'unsubscribe':     // 用户取消关注时设置is_subscribe为0
                // TODO 
                M('mp_fans')->where(array('openid'=>$this->openid))->setField('is_subscribe', 0);
                break;
            case 'scan':        // 用户扫描二维码时记录扫码信息
                $scene_qrcode = M('scene_qrcode')->where(array('mpid'=>$this->mpid,'ticket'=>get_rev_ticket()))->find();
                if ($scene_qrcode) {
                    $scan_data['mpid'] = $this->mpid;
                    $scan_data['openid'] = $this->openid;
                    $scan_data['qrcode_id'] = $scene_qrcode['id'];
                    $scan_data['scene_name'] = $scene_qrcode['scene_name'];
                    $scan_data['keyword'] = $scene_qrcode['keyword'];
                    $scan_data['scene_id'] = get_rev_scene_id();
                    $scan_data['scan_type'] = 'scan';
                    $scan_data['ctime'] = $this->message['CreateTime'];
                    M('scene_qrcode_statistics')->add($scan_data);
                    $keyword = $scene_qrcode['keyword'];
                    $this->respond_keyword($keyword, $this->message);
                }
                break;
            case 'report_location':     // 用户上报地理位置时，保存用户最新位置信息
                $data['longitude'] = $message['longitude'];
                $data['latitude'] = $message['latitude'];
                $data['location_precision'] = $message['precision'];
                D('MpFans')->save_fans_location($data);         // 保存用户当前所处位置
                break;
            case 'click':
                $keyword = $this->message['EventKey'];
                $this->respond_keyword($keyword, $this->message);
                break;
            case 'view':
                // TODO
                break;
            default:
                # code...
                break;
        }
        $auto_reply = D('MpAutoReply')->get_auto_reply_by_type($type);
        $reply_type = $auto_reply['reply_type'];
        switch ($reply_type) {
            case 'keyword':                 // 绑定关键词
                $keyword = $auto_reply['keyword'];
                $this->respond_keyword($keyword);       // 响应关键词
                break;
            case 'addon':
                $this->addon = $auto_reply['addon'];    // 分发到插件进行处理
                $this->respond_addon($this->addon, $this->message);
                break;
            default:
                # code...
                break;
        }
    }
}



 ?>