<?php
/**
 * @Author: Phu Hoang
 * @Date:   2016-11-24 15:21:07
 * @Last Modified by:   Phu Hoang
 * @Last Modified time: 2016-11-25 18:25:30
 */
class HMP_MageMulti_Model_Mage_Install_Installer_Config extends Mage_Install_Model_Installer_Config{
    public function __construct()
    {
        parent::__construct();
        $this->_localConfigFile = Mage::getBaseDir() . DS . MageMulti::CLIENT_DIR . DS . MageMulti::getClientCode() . DS . 'etc'. DS . 'local.xml';
    }
}