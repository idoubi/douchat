<?php 

namespace Admin\Controller;
use Think\Controller;

/**
 * 用户管理控制器
 * @author 艾逗笔<765532665@qq.com>
 */
class UserController extends BaseController {

    /**
     * 添加用户
     * @author 艾逗笔<765532665@qq.com>
     */
    public function add() {
        if (IS_POST) {
            $_validate = array(
                array('username', 'require', '用户名不能为空', 1, 'regex', 3),
                array('username', '', '用户已存在', 2, 'unique', 1),
                array('username', '6,20', '用户名长度在6至20个字符之间', 2, 'length', 1),
                array('password', 'require', '密码不能为空', 1, 'regex', 1),
                array('password', '6,20', '密码长度在6至20个字符之间', 2, 'length', 1),
                array('confirm_password', 'require', '确认密码不能为空', 0, 'regex', 1),
                array('confirm_password', 'password', '确认密码不正确', 0, 'confirm', 1),
                array('email', 'require', '邮箱不能为空', 1, 'regex', 3),
                array('email', 'email', '邮箱格式不正确', 2, 'regex', 3),
                array('email', '', '邮箱已存在', 2, 'unique', 1)
            );
            $_auto = array(
                array('password', 'md5', 3, 'function'),
                array('nickname', 'username', 1, 'field'),
                array('register_time', 'time', 1, 'function')
            );
            $User = M('user');
            $User->setProperty('_validate', $_validate);
            $User->setProperty('_auto', $_auto);
            if (!$User->create()) {
                $this->error($User->getError());
            } else {
                $user_id = $User->add();
                if ($user_id) {
                    foreach (I('roles') as $k => $v) {
                        $data['user_id'] = $user_id;
                        $data['role_id'] = $v;
                        $datas[] = $data;
                    }
                    M('rbac_role_user')->addAll($datas);
                    $this->success('添加用户成功', U('lists'));
                } else {
                    $this->error('添加用户失败');
                }
            }
        } else {
            $this->addCrumb('系统管理', U('Index/index'), '')
                 ->addCrumb('用户管理', U('User/lists'), '')
                 ->addCrumb('添加用户', '', 'active')
                 ->addNav('添加用户', '', 'active')
                 ->setModel('user')
                 ->addFormField('username', '用户名', 'text')
                 ->addFormField('password', '密码', 'password')
                 ->addFormField('confirm_password', '确认密码', 'password')
                 ->addFormField('email', '邮箱', 'email')
                 ->addFormField('roles', '用户角色', 'checkbox', array('options'=>'callback','callback_name'=>'get_user_roles'))
                 ->common_add();
        }
    }

    /**
     * 编辑用户
     * @author 艾逗笔<765532665@qq.com>
     */
    public function edit() {
        if (IS_POST) {
            if (I('password')) {
                $password = I('password');
                $confirm_password = I('confirm_password');
                if (strlen($password) < 6 || strlen($password) > 20) {
                    $this->error('密码长度在6至20个字符之间');
                }
                if (!$confirm_password) {
                    $this->error('确认密码不能为空');
                }
                if ($password !== $confirm_password) {
                    $this->error('确认密码不正确');
                }
                $data['password'] = md5($password);
            }
            $_validate[] = array('email', 'require', '邮箱不能为空', 1, 'regex', 3);
            $_validate[] = array('email', 'email', '邮箱格式不正确', 2, 'regex', 3);
        
            $User = M('user');
            $User->setProperty('_validate', $_validate);
            $User->setProperty('_auto', $_auto);

            $data['nickname'] = I('nickname');
            $data['email'] = I('email');
            $data['id'] = I('get.id');
            C('TOKEN_ON', false);
            if (!$User->create($data)) {
                $this->error($User->getError());
            } else {
                $User->save($data);
            }

            M('rbac_role_user')->where(array('user_id'=>I('get.id')))->delete();
            if (I('roles')) {
                foreach (I('roles') as $k => $v) {
                    $data2['user_id'] = I('get.id');
                    $data2['role_id'] = $v;
                    $datas2[] = $data2;
                }
                M('rbac_role_user')->addAll($datas2);
            }
            $this->success('修改用户信息成功', U('lists'));
        } else {
            $results = M('rbac_role_user')->field('role_id')->where(array('user_id'=>I('id')))->select();
            foreach ($results as $k => $v) {
                $roles[] = $v['role_id'];
            }
            $user = M('user')->find(I('id'));
            $user['roles'] = json_encode($roles);
            $this->addCrumb('系统管理', U('Index/index'), '')
                 ->addCrumb('用户管理', U('User/lists'), '')
                 ->addCrumb('编辑用户', '', 'active')
                 ->addNav('编辑用户', '', 'active')
                 ->setModel('user')
                 ->addFormField('username', '用户名', 'text', array('attr'=>'disabled'))
                 ->addFormField('nickname', '昵称', 'text')
                 ->addFormField('password', '新密码', 'password', array('tip'=>'如果不修改密码请勿填写'))
                 ->addFormField('confirm_password', '确认密码', 'password')
                 ->addFormField('email', '邮箱', 'email')
                 ->addFormField('roles', '用户角色', 'checkbox', array('options'=>'callback','callback_name'=>'get_user_roles'))
                 ->setFormData($user)
                 ->common_edit();
        }
    }

    /**
     * 用户列表
     * @author 艾逗笔<765532665@qq.com>
     */
    public function lists() {
        $this->addCrumb('系统管理', U('Index/index'), '')
             ->addCrumb('用户管理', U('User/lists'), '')
             ->addCrumb('用户列表', '', 'active')
             ->addNav('用户列表', '', 'active')
             ->addButton('添加用户', U('add'), 'btn btn-primary')
             ->setModel('user')
             ->addListItem('username', '用户名')
             ->addListItem('nickname', '昵称')
             ->addListItem('email', '邮箱')
             ->addListItem('id', '用户角色', 'callback', array('callback_name'=>'parse_roles'))
             ->addListItem('register_time', '注册时间', 'function', array('function_name'=>'date','params'=>'Y-m-d H:i:s,###'))
             ->addListItem('id', '操作', 'custom', array('options'=>array('edit'=>array('编辑',U('edit',array('id'=>'{id}')),'btn btn-primary btn-sm icon-edit',''),'delete'=>array('删除',U('delete',array('id'=>'{id}')),'btn btn-danger btn-sm icon-delete', ''))))
             ->common_lists();
    }

    /**
     * 删除用户
     * @author 艾逗笔<765532665@qq.com>
     */
    public function delete() {
        $user_id = I('id');
        M('rbac_role_user')->where(array('user_id'=>$user_id))->delete();
        M('mp')->where(array('user_id'=>$user_id))->delete();
        $res = M('user')->delete($user_id);
        if (!$res) {
            $this->error('删除用户失败');
        } else {
            $this->success('删除用户成功', U('lists'));
        }
    }

    /**
     * 获取用户角色
     * @author 艾逗笔<765532665@qq.com>
     */
    public function get_user_roles() {
        $results = M('rbac_role')->select();
        foreach ($results as $k => $v) {
            $roles[$v['id']] = $v['name'];
        }
        return $roles;
    }

    /**
     * 格式化显示用户所属角色
     * @author 艾逗笔<765532665@qq.com>
     */
    public function parse_roles($user_id) {
        $role_users = M('rbac_role_user')->field('role_id')->where(array('user_id'=>$user_id))->select();
        foreach ($role_users as $k => $v) {
            $role = M('rbac_role')->where(array('id'=>$v['role_id']))->getField('name');
            $roles[] = $role;
        }
        return implode(',', $roles);
    } 

}


?>