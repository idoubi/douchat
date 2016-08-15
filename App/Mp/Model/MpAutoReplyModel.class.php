<?php 

namespace Mp\Model;
use Think\Model;

/**
 * 公众号自动回复模型
 * @author 艾逗笔<765532665@qq.com>
 */
class MpAutoReplyModel extends Model {

	/**
	 * 获取自动回复内容
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_auto_reply($reply_id) {
		$db_prefix = C('DB_PREFIX');
		$tb_mp_auto_reply = $db_prefix . 'mp_auto_reply AS reply';
		$tb_mp_material = $db_prefix . 'mp_material AS material';
		$tb_mp_rule = $db_prefix . 'mp_rule AS rule';
		$sql = "SELECT reply.id AS reply_id,reply.reply_type AS reply_type,
				material.id AS material_id,material.content AS content,
				material.image AS image,
				material.title AS title,material.picurl AS picurl,material.description AS description,material.url AS url,
				rule.id AS rule_id,rule.keyword AS keyword
				FROM {$tb_mp_auto_reply} 
				LEFT JOIN {$tb_mp_material} ON reply.material_id = material.id
				LEFT JOIN {$tb_mp_rule} ON reply.id = rule.reply_id
				WHERE reply.id = {$reply_id}";
		$result = M()->query($sql);
		if (!$result) {
			$return['errcode'] = 1001;
			$return['errmsg'] = '自动回复规则不存在';
			return $return;
		} else {
			$return['errcode'] = 0;
			$return['result'] = $result[0]; 
			return $return;
		}
	}

	/**
	 * 根据回复触发类型获取回复内容
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function get_auto_reply_by_type($type, $mpid = '') {
		if ($mpid == '') {
			$mpid = get_mpid();
		}
		if (!$type || !$mpid) {
			return false;
		}
		$map['mpid'] = intval($mpid);
		$map['type'] = $type;
		$auto_reply = $this->where($map)->find();
		if (!$auto_reply) {
			return false;
		}
		return $auto_reply;
	}

	/**
	 * 添加自动回复
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function add_auto_reply($type, $data) {
		$keyword = $data['keyword'];		// 关键词
		if (!$keyword) {
			$return['errcode'] = 1001;
			$return['errmsg'] = '关键词不能为空';
			return $return;
		}
		if (D('MpRule')->get_keyword_rule($keyword, 'auto_reply')) {
			$return['errcode'] = 1001;
			$return['errmsg'] = '相同自动回复类型的关键词已存在';
			return $return;
		}
		switch ($type) {
			case 'text':
				$content = $data['content'];		// 文本内容
				if (!$content) {
					$return['errcode'] = 1001;
					$return['errmsg'] = '文本内容必填';
					return $return;
				}
				$insert['content'] = $content;
				break;
			case 'image':
				$image = $data['image'];
				if (!$image) {
					$return['errcode'] = 1001;
					$return['errmsg'] = '回复图片必填';
					return $return;
				}	
				$insert['image'] = $image;	
				break;
			case 'news':
				$title = $data['title'];
				$picurl = $data['picurl'];
				$description = $data['description'];
				$url = $data['url'];
				if (!$title) {
					$return['errcode'] = 1001;
					$return['errmsg'] = '图文标题必填';
					return $return;
				}
				$insert['title'] = $title;
				$insert['picurl'] = $picurl;
				$insert['description'] = $description;
				$insert['url'] = $url;
				break;
			default:
				# code...
				break;
		}
		M()->startTrans();		// 开启事务
		try {
			$insert['mpid'] = get_mpid();
			$insert['type'] = $type;
			$insert['create_time'] = time();
			if (!M('mp_material')->add($insert)) {		// 添加素材内容
				throw new \Exception('添加素材内容失败');
			} else {
				unset($insert);
				$material_id = M()->getLastInsID();		// 获取刚刚插入的素材ID
				$insert['mpid'] = get_mpid();
				$insert['type'] = 'keyword';
				$insert['reply_type'] = $type;
				$insert['material_id'] = $material_id;
				if (!M('mp_auto_reply')->add($insert)) {		// 添加自定义回复
					throw new \Exception('添加自动回复失败');
				} else {
					unset($insert);
					$reply_id = M()->getLastInsID();		// 获取刚刚插入的自动回复内容
					$insert['mpid'] = get_mpid();
					$insert['keyword'] = $keyword;
					$insert['type'] = 'auto_reply';
					$insert['reply_id'] = $reply_id;
					if (!M('mp_rule')->add($insert)) {
						throw new \Exception('添加关键词触发规则失败');
					} else {
						M()->commit();							// 事务提交
						$return['errcode'] = 0;
						$return['errmsg'] = '添加自动回复成功';
						return $return;
					}
				}
			}				
		} catch (\Exception $e) {
			M()->rollback();		// 事务回滚
			$return['errcode'] = 1001;
			$return['errmsg'] = $e->getMessage();
			return $return;							// 返回错误信息
		}
	}

	/**
	 * 编辑自动回复
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function edit_auto_reply($type, $data) {
		$keyword = $data['keyword'];		// 关键词
		if (!$keyword) {
			$return['errcode'] = 1001;
			$return['errmsg'] = '关键词不能为空';
			return $return;
		}
		switch ($type) {
			case 'text':
				$content = $data['content'];		// 文本内容
				if (!$content) {
					$return['errcode'] = 1001;
					$return['errmsg'] = '文本内容必填';
					return $return;
				}
				$update['content'] = $content;
				break;
			case 'image':
				$image = $data['image'];
				if (!$image) {
					$return['errcode'] = 1001;
					$return['errmsg'] = '回复图片必填';
					return $return;
				}	
				$update['image'] = $image;	
				break;
			case 'news':
				$title = $data['title'];
				$picurl = $data['picurl'];
				$description = $data['description'];
				$url = $data['url'];
				if (!$title) {
					$this->error('图文标题必填');
				}
				$update['title'] = $title;
				$update['picurl'] = $picurl;
				$update['description'] = $description;
				$update['url'] = $url;
				break;
			default:
				# code...
				break;
		}
		M()->startTrans();				// 开启事务
		try {
			if (M('mp_material')->where(array('id'=>$data['material_id']))->save($update) === false) {
				throw new \Exception('更新素材失败');
			} else {
				unset($update);
				$update['keyword'] = $data['keyword'];
				if (M('mp_rule')->where(array('id'=>$data['rule_id']))->save($update) === false) {
					throw new \Exception('更新关键词触发规则失败');
				} else {
					M()->commit();
					$return['errcode'] = 0;
					$return['errmsg'] = '更新自动回复成功';
					return $return;
				}
			}
		} catch (\Exception $e) {
			M()->rollback();		// 回滚事务
			$return['errcode'] = 0;
			$return['errmsg'] = $e->getMessage();
			return $return;
		}
	}

	/**
	 * 删除自动回复
	 * @author 艾逗笔<765532665@qq.com>
	 */
	public function delete_auto_reply($type, $data) {
		M()->startTrans();			// 开启事务
		try {
			if (!M('mp_rule')->delete($data['rule_id'])) {
				throw new \Exception('删除关键词触发规则失败');
			} else {
				if (!M('mp_auto_reply')->delete($data['reply_id'])) {
					throw new \Exception('删除自动回复规则失败');
				} else {
					M()->commit();		// 提交事务
					$return['errcode'] = 0;
					$return['errmsg'] = '删除自动回复成功';
					return $return;
				}
			}
		} catch (\Exception $e) {
			M()->rollback();		// 事务回滚
			$return['errcode'] = 1001;
			$return['errmsg'] = $e->getMessage();
			return $return;
		}
	}

}

?>