<?php

/*
 * 功能：后台中心－退出类
 * Author:资料空白
 * Date:20150902
 */

class LogoutController extends AdminBasicController
{

    public function init()
    {
        parent::init();
        Yaf\Dispatcher::getInstance()->disableView();
        if (!$this->AdminUser) {
            $this->redirect('/admin/login');
            return FALSE;
        }
    }

    public function indexAction()
    {
        Yaf\Session::getInstance()->__unset('AdminUser');
        $this->redirect('/admin/login');
        return FALSE;
    }

}