<?php

/*
 * 功能：安装升级模块
 * Author:资料空白
 * Date:20180702
 */

class UpgradeController extends AdminBasicController
{
	private $all_version;
	public function init()
    {
        parent::init();
		$this->all_version = ['1.0.0','1.0.2','1.0.3','1.0.4','1.0.5'];
    }

    public function indexAction()
    {
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $this->redirect("/admin/login");
            return FALSE;
        }
		if(file_exists(INSTALL_LOCK)){
			$version = @file_get_contents(INSTALL_LOCK);
			$version = strlen(trim($version))>0?$version:'1.0.0';
			if(version_compare(trim($version), trim(VERSION), '<' )){
				$data = array();
				$update_version = $this->_getUpdateVersion($version);
				if($update_version==''){
					$data['update_version'] = $update_version!=''?$update_version:'未知的版本';
					$data['upgrade_desc'] = "抱歉,我表示很难理解你为什么能看到这条信息";
					$data['upgrade_sql'] = '';
				}else{
					$data['update_version'] = $update_version;
					$desc = @file_get_contents(INSTALL_PATH.'/'.$update_version.'/upgrade.txt');
					$data['upgrade_desc'] = $desc;
					if(file_exists(INSTALL_PATH.'/'.$update_version.'/upgrade.sql')){
						$data['upgrade_sql'] = INSTALL_PATH.'/'.$update_version.'/upgrade.sql';
					}else{
						$data['upgrade_sql'] = '';
					}
				}
				$data['version'] = $version;
				$this->getView()->assign($data);
			}else{
				$this->redirect("/admin/");
				return FALSE;
			}
		}else{
			$this->redirect("/install/");
			return FALSE;
		}
    }
	
	public function ajaxAction()
	{
        if ($this->AdminUser==FALSE AND empty($this->AdminUser)) {
            $data = array('code' => 1000, 'msg' => '请登录');
			Helper::response($data);
        }
		$method = $this->getPost('method',false);
		$data = array();
		
		if($method AND $method=='upgrade'){
            try {
				$version = @file_get_contents(INSTALL_LOCK);
				$version = strlen(trim($version))>0?$version:'1.0.0';
				if(version_compare(trim($version), trim(VERSION), '<' )){
					$update_version = $this->_getUpdateVersion($version);
					if($update_version==''){
						$data = array('code' => 1, 'msg' =>"版本信息异常");
						Helper::response($data);
					}
				}else{
					$data = array('code' => 1, 'msg' =>"请勿重复升级");
					Helper::response($data);
				}
				
				$upgrade_sql = INSTALL_PATH.'/'.$update_version.'/upgrade.sql';
				
				if(file_exists($upgrade_sql) AND is_readable($upgrade_sql)){
					$sql = @file_get_contents($upgrade_sql);
					if(!$sql){
						$data = array('code' => 1003, 'msg' =>"无法读取".$upgrade_sql."文件,请检查文件是否存在且有读权限");
						Helper::response($data);
					}
				}else{
					$data = array('code' => 1004, 'msg' =>"无法读取".$upgrade_sql."文件,请检查文件是否存在且有读权限");
					Helper::response($data);
				}
				
				if (!is_writable(INSTALL_LOCK)){
					$data = array('code' => 1006, 'msg' =>"无法写入文件".INSTALL_LOCK.",请检查是否有写权限");
					Helper::response($data);
				}
				
				$m_config = $this->load('config');
                $m_config->Query($sql);
				
				$result = @file_put_contents(INSTALL_LOCK,$update_version,LOCK_EX);
				if (!$result){
					$data = array('code' => 1004, 'msg' =>"无法写入安装锁定到".INSTALL_LOCK."文件，请检查是否有写权限");
				}
				$data = array('code' => 1, 'msg' =>"SUCCESS");
            } catch (\Exception $e) {
				$data = array('code' => 1001, 'msg' =>"失败:".$e->getMessage());
            }
		}else{
			$data = array('code' => 1000, 'msg' => '丢失参数');
		}
		Helper::response($data);
	}
	
	//获取下一版本号
	private function _getUpdateVersion($version){
		$offset=array_search($version,$this->all_version);
		$k = $offset+1;
		if(isset($this->all_version[$k])){
			return $this->all_version[$k];
		}else{
			return '';
		}
	}
}