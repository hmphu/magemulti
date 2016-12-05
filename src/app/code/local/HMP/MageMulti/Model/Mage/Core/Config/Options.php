<?php
/**
 * @Author: Phu Hoang
 * @Date:   2016-11-24 15:16:25
 * @Last Modified by:   Phu Hoang
 * @Last Modified time: 2016-11-25 18:25:30
 */

class HMP_MageMulti_Model_Mage_Core_Config_Options extends Mage_Core_Model_Config_Options
{
    private $_baseData = array();

    /**
     * Initialize default values of the options
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_baseData = $this->_data;

        $code    = MageMulti::getClientCode();
        $appRoot = Mage::getRoot();
        $root    = dirname($appRoot);
        $this->_data['app_dir']     = $appRoot;
        $this->_data['base_dir']    = $root;
        $this->_data['code_dir']    = $appRoot . DS . 'code';
        $this->_data['design_dir']  = $appRoot . DS . 'design';
        $this->_data['etc_dir']     = $appRoot . DS . 'etc';
        $this->_data['lib_dir']     = $appRoot . DS . 'lib';
        $this->_data['locale_dir']  = $appRoot . DS . 'locale';
        $this->_data['media_dir']   = $root . DS . MageMulti::CLIENT_DIR . DS . $code . DS . 'media';
        $this->_data['skin_dir']    = $root . DS . 'skin';
        $this->_data['var_dir']     = $this->getVarDir();
        $this->_data['tmp_dir']     = $this->_data['var_dir'] . DS . 'tmp';
        $this->_data['cache_dir']   = $this->_data['var_dir'] . DS . 'cache';
        $this->_data['log_dir']     = $this->_data['var_dir'] . DS . 'log';
        $this->_data['session_dir'] = $this->_data['var_dir'] . DS . 'session';
        $this->_data['upload_dir']  = $this->_data['media_dir'] . DS . 'upload';
        $this->_data['export_dir']  = $this->_data['var_dir'] . DS . 'export';

        $this->_checkDirs();
    }

    public function getLocalEtcDir(){
        return $this->_data['base_dir']. DS . MageMulti::CLIENT_DIR . DS . MageMulti::getClientCode(). DS . 'etc'; 
    }

    /**
     * Initital all required directories
     */
    private function _checkDirs(){
        $this->createDirIfNotExists($this->getLocalEtcDir(). DS . 'modules', 0755, true);
        $this->createDirIfNotExists($this->_data['tmp_dir'], 0777, true);
        $this->createDirIfNotExists($this->_data['cache_dir'], 0777, true);
        $this->createDirIfNotExists($this->_data['log_dir'], 0777, true);
        $this->createDirIfNotExists($this->_data['session_dir'], 0777, true);
        $this->createDirIfNotExists($this->_data['upload_dir'], 0777, true);
        $this->createDirIfNotExists($this->_data['export_dir'], 0777, true);
    }

    /**
     * Get var directory path
     * @return string
     */
    public function getVarDir()
    {
        $dir = isset($this->_data['var_dir']) ? $this->_data['var_dir']
            : $this->_data['base_dir'] . DS .  MageMulti::CLIENT_DIR . DS . MageMulti::getClientCode() . DS . 'var' ;
        if (!$this->createDirIfNotExists($dir)) {
            Mage::throwException('Unable to find writable var_dir');
        }
        return $dir;
    }
}
