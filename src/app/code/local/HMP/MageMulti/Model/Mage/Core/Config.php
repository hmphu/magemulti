<?php
/**
 * @Author: Phu Hoang
 * @Date:   2016-11-24 17:23:07
 * @Last Modified by:   Phu Hoang
 * @Last Modified time: 2016-11-25 18:25:30
 */

class HMP_MageMulti_Model_Mage_Core_Config extends Mage_Core_Model_Config
{
    /**
     * Class construct
     *
     * @param mixed $sourceData
     */
    public function __construct($sourceData = null)
    {
        parent::__construct($sourceData);
        $this->_options = new HMP_MageMulti_Model_Mage_Core_Config_Options($sourceData);
    }

    /**
     * Load base system configuration (config.xml and local.xml files)
     *
     * @return HMP_MageMulti_Model_Mage_Core_Config
     */
    public function loadBase()
    {
        $etcDir = $this->getOptions()->getEtcDir();
        $files = glob($etcDir . DS . '*.xml');
        $files = array_merge($files, glob(MageMulti::getEtcDir() . DS . '*.xml'));
        
        if (in_array($etcDir . DS . 'local.xml', $files)){
            $key = array_search($etcDir . DS . 'local.xml', $files);
            unset($files[$key]);
        }
        
        $this->loadFile(current($files));
        while ($file = next($files)) {
            $merge = clone $this->_prototype;
            $merge->loadFile($file);
            $this->extend($merge);
        }

        if(in_array(MageMulti::getLocalXmlFile(), $files)){
            $this->_isLocalConfigLoaded = true;
        }
        
        return $this;
    }

    /**
     * Load modules configuration
     *
     * @return HMP_MageMulti_Model_Mage_Core_Config
     */
    public function loadModules()
    {
        Varien_Profiler::start('config/load-modules');
        $this->_loadDeclaredModules();

        $resourceConfig = sprintf('config.%s.xml', $this->_getResourceConnectionModel('core'));
        $this->loadModulesConfiguration(array('config.xml',$resourceConfig), $this);

        /**
         * Prevent local.xml directives overwriting
         */
        $mergeConfig = clone $this->_prototype;
        $this->_isLocalConfigLoaded = $mergeConfig->loadFile(MageMulti::getLocalXmlFile());
        if ($this->_isLocalConfigLoaded) {
            $this->extend($mergeConfig);
        }

        $this->applyExtends();
        Varien_Profiler::stop('config/load-modules');
        return $this;
    }

    /**
     * Retrive Declared Module file list
     *
     * @return array
     */
    protected function _getDeclaredModuleFiles()
    {
        $etcDir = $this->getOptions()->getEtcDir();
        $moduleFiles = glob($etcDir . DS . 'modules' . DS . '*.xml');
        $moduleFiles = array_merge(
            $moduleFiles,
            glob(MageMulti::getEtcDir() . DS . 'modules' . DS . '*.xml')
        );

        if (!$moduleFiles) {
            return false;
        }

        $collectModuleFiles = array(
            'base'   => array(),
            'mage'   => array(),
            'custom' => array()
        );

        foreach ($moduleFiles as $v) {
            $name = explode(DIRECTORY_SEPARATOR, $v);
            $name = substr($name[count($name) - 1], 0, -4);

            if ($name == 'Mage_All') {
                $collectModuleFiles['base'][] = $v;
            } elseif (substr($name, 0, 5) == 'Mage_') {
                $collectModuleFiles['mage'][] = $v;
            } else {
                $collectModuleFiles['custom'][] = $v;
            }
        }

        return array_merge(
            $collectModuleFiles['base'],
            $collectModuleFiles['mage'],
            $collectModuleFiles['custom']
        );
    }
}
