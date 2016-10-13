<?php

namespace Addons\IdouDemo\Controller;
use Mp\Controller\ApiController;

/**
 * 功能演示响应控制器
 * @author 艾逗笔
 */
class RespondController extends ApiController {

    // 微信交互
    public function wechat($message = array()) {
        $msgtype = $message['msgtype'];
        switch ($msgtype) {
            case 'text':
                $content = $message['content'];
                switch ($content) {
                    case '回复文本':
                        reply_text('这是一条文本消息');
                        break;
                    case '回复单图文':
                        $articles[0] = array(
                            'Title' => '单图文标题',
                            'Description' => '单图文描述',
                            'PicUrl' => 'https://mp.weixin.qq.com/wiki/static/assets/dc5de672083b2ec495408b00b96c9aab.png',
                            'Url' => 'http://bbs.docuhat.cc/'
                        );
                        reply_news($articles);
                        break;
                    case '回复多图文':
                        $articles[0] = array(
                            'Title' => '图文标题1',
                            'Description' => '图文描述1',
                            'PicUrl' => 'https://mp.weixin.qq.com/wiki/static/assets/dc5de672083b2ec495408b00b96c9aab.png',
                            'Url' => 'http://qq.com/'
                        );
                        $articles[1] = array(
                            'Title' => '图文标题2',
                            'Description' => '图文描述2',
                            'PicUrl' => 'https://mp.weixin.qq.com/wiki/static/assets/dc5de672083b2ec495408b00b96c9aab.png',
                            'Url' => 'http://baidu.com/'
                        );
                        $articles[2] = array(
                            'Title' => '图文标题3',
                            'Description' => '图文描述3',
                            'PicUrl' => 'https://mp.weixin.qq.com/wiki/static/assets/dc5de672083b2ec495408b00b96c9aab.png',
                            'Url' => 'http://alibaba.com/'
                        );
                        reply_news($articles);
                        break;
                    case '发送模板消息':
                        # code...
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
    }

}

?>