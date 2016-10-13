<?php 

namespace Addons\IdouGuestbook\Model;
use Think\Model;

/**
 * 留言数据管理模型
 * @author 艾逗笔<765532665@qq.com>
 */
class IdouGuestbookListModel extends Model {

    /**
     * 自动验证
     * @author 艾逗笔<765532665@qq.com>
     */
    protected $_validate = array(
    	array('nickname', 'require', '用户昵称不能为空'),
    	array('content', 'require', '留言内容不能为空')
    );

    /**
     * 自动完成
     * @author 艾逗笔<765532665@qq.com>
     */
    protected $_auto = array(
   		array('mpid', 'get_mpid', 1, 'function'),
   		array('openid', 'get_openid', 1, 'function'),
   		array('create_time', 'time', 1, 'function')
    );

}

?>