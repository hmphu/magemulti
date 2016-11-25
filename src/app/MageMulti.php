<?php
/**
 * @Author: Phu Hoang
 * @Date:   2016-11-24 17:18:59
 * @Last Modified by:   Phu Hoang
 * @Last Modified time: 2016-11-25 18:25:30
 */

final class MageMulti
{
    const CONFIG_CLASS = 'HMP_MageMulti_Model_Mage_Core_Config';
    const CLIENT_DIR = 'clients';

    /**
     * The etc dir for this CLIENT_CODE
     */
    protected static $_etcDir;

    /**
     * The "local.xml" for this CLIENT_CODE
     */
    protected static $_localXmlFile;

    /**
     * The Multi Client code
     */
    protected static $_clientCode;

    /**
     * Set developer mode if applicable
     */
    public function __construct()
    {
        if (self::isDev()) {
            Mage::setIsDeveloperMode(true);
            ini_set('display_errors', 1);
        }
    }

    /**
     * Get the Multi Client code
     *
     * @return string
     */
    public static function getClientCode()
    {
        if (self::$_clientCode === null) {
            if (!isset($_SERVER['CLIENT_CODE'])) {
                Mage::throwException('CLIENT_CODE code not set');
            }
            self::$_clientCode = $_SERVER['CLIENT_CODE'];
        }
        return self::$_clientCode;
    }

    /**
     * Determine if this environment is non-production
     *
     * @return boolean
     */
    public static function isDev()
    {
        return isset($_SERVER['ENV']) && $_SERVER['ENV'] == 'dev';
    }

    /**
     * Get the run code
     *
     * @return string
     */
    public static function getRunCode()
    {
        return 'default';
    }

    /**
     * Get the run type
     *
     * @return string
     */
    public static function getRunType()
    {
        return 'store';
    }

    /**
     * Get the run options
     *
     * @return array
     */
    public static function getRunOptions($code = false)
    {
        if ($code !== false) {
            self::$_clientCode = $code;
        }

        return array(
            'config_model' => self::CONFIG_CLASS,
            'is_installed' => static::isInstalled(),
            'client_code'  => $code ?: self::getClientCode()
        );
    }

    /**
     * Check if current site is installed or not
     * @return boolean
     */
    public static function isInstalled(){
        $localxml = BP . DS . self::CLIENT_DIR . DS . self::getClientCode() . DS . 'etc'. DS . 'local.xml';
        
        if(!file_exists($localxml))
            return false;

        $xmlObj = new Varien_Simplexml_Config($localxml); 
        $date = (string)$xmlObj->getNode('global/install/date');
        if(!strtotime($date))
            return false;
        
        return true;
    }

    /**
     * Get the etc dir for this CLIENT_CODE
     *
     * @return string
     */
    public static function getEtcDir()
    {
        if (self::$_etcDir === null) {
            self::$_etcDir = Mage::getConfig()->getOptions()->getLocalEtcDir();
        }
        return self::$_etcDir;
    }

    /**
     * Get the "local.xml" for this CLIENT_CODE
     *
     * @return string
     */
    public static function getLocalXmlFile()
    {
        if (self::$_localXmlFile === null) {
            self::$_localXmlFile = self::getEtcDir() . DS . 'local.xml';
        }
        return self::$_localXmlFile;
    }
}