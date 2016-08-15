<?php

namespace Install\Controller;
use Think\Controller;
use Think\Storage;

class IndexController extends Controller {

    //安装首页
    public function index() {
        if(Storage::has('./Data/install.lock')){
            $this->error('已经安装过，请删除Data/install.lock再安装');
        }
        session('error', false);
        $env = check_env();                         // 环境检测
        if (IS_WRITE) {                             // 目录文件读写检测
            $dirfile = check_dirfile();
            $this->assign('dirfile', $dirfile);
        }
        $func = check_func();                       //函数检测
        session('step', 1);                         
        $this->assign('env', $env);
        $this->assign('func', $func);
        $this->display();
    }

    //安装完成
    public function complete(){
        $step = session('step');

        if(!$step){
            $this->redirect('index');
        } elseif($step != 3) {
            $this->redirect("Install/step{$step}");
        }

        // 写入安装锁定文件
        Storage::put('./Data/install.lock', 'lock');
        if(!session('update')){
            //创建配置文件
            $this->assign('info',session('config_file'));
        }
        session('step', null);
        session('error', null);
        session('update',null);
        $this->display();
    }
}
