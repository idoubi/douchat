<?php 

namespace Mp\Model;
use Think\Model;

/**
 * 积分记录模型
 * @author 艾逗笔<765532665@qq.com>
 */
class MpScoreRecordModel extends Model {

	/**
	 * 增加积分记录
	 * @param $value 积分值
	 * @param $remark 积分说明
	 * @param $type 积分类型，score：积分，money：金钱
	 * @param $flag 积分标识 自定义积分标识，例如：fans_bind、view_artice等
	 * @param $source 积分来源 system标识系统增加的积分，addon标识在插件中增加的积分
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function add_score($value,$remark='',$type='score',$flag='',$source='addon') {
		$mpid = get_mpid();
		$openid = get_openid();
		if (!$mpid || !$openid || !$value) {
			return false;
		}
		if (empty($flag)) {
			$flag = get_addon();
		}
		$insert['mpid'] = $mpid;
		$insert['openid'] = $openid;
		$insert['value'] = intval($value);
		$insert['remark'] = $remark;
		$insert['type'] = $type;
		$insert['flag'] = $flag;
		$insert['source'] = $source;
		if (!M('mp_score_record')->add($insert)) {
			return false;
		} else {
			M('mp_fans')->where(array('mpid'=>$mpid,'openid'=>$openid))->setInc($type, $value);
			return true;
		}
	}
}

?>