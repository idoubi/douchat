<?php

/**
 * 秘钥管理控制器
 * @author 艾逗笔<http://idoubi.cc>
 */
namespace Mp\Controller;
use Mp\Controller\BaseController;

class AccessKeyController extends BaseController {
	
	// 秘钥列表
	public function lists() {
		$this->setMetaTitle('秘钥管理')
			->setModel('access_key')
			->setListMap(['user_id'=>$this->user_id])
			->setTip('此处的秘钥用于Api请求，请妥善保管，定期更换')
			->addCrumb('个人中心', '', '')
			->addCrumb('秘钥管理', '', 'active')
			->addNav('秘钥管理', '', 'active')
			->addButton('创建秘钥', U('add'), 'btn btn-primary')
			->addListItem('ak', 'Access Key(ak)')
			->addListItem('sk', 'Secret Key(sk)')
			->addListItem('status', '状态', 'enum', ['options'=>[
				0 => '停用',
				1 => '使用中'
			]])
			->addListItem('created_at', '创建时间', 'datetime')
			->addListItem('id', '操作', 'custom', ['options'=>[
				[
					'title' => '更新SK',
					'url' => U('updateSk', ['id'=>'{id}']),
					'class' => 'btn btn-sm btn-info'
				],
				[
					'title' => '删除秘钥',
					'url' => U('delete', ['id'=>'{id}']),
					'class' => 'btn btn-sm btn-danger'
				]
			]])
			->common_lists();
	}
	
	// 创建秘钥
	public function add() {
		$id = M('access_key')->add([
			'ak' => get_nonce(32),
			'sk' => get_nonce(43),
			'user_id' => $this->user_id,
			'mpid' => $this->mpid,
			'created_at' => NOW_TIME,
			'updated_at' => NOW_TIME,
			'status' => 1
		]);
		if ($id) {
			$this->success('创建秘钥成功');
		} else {
			$this->error('创建秘钥失败');
		}
	}

	// 更新sk
    public function updateSk() {
	    $sk = get_nonce(43);
	    $res = M('access_key')->where(['user_id'=>$this->user_id, 'id'=>I('get.id')])->setField('sk', $sk);
	    if ($res !== false) {
	        $this->success('更新成功');
        } else {
	        $this->error('更新失败');
        }
    }

    // 删除秘钥
    public function delete() {
	    $this->setModel('access_key')
            ->setDeleteMap(['user_id'=>$this->user_id, 'id'=>I('get.id')])
            ->setDeleteSuccessUrl(U('lists'))
            ->common_delete();
    }
}