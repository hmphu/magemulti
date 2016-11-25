<?php
/**
 * @Author: Phu Hoang
 * @Date:   2016-11-25 15:35:28
 * @Last Modified by:   Phu Hoang
 * @Last Modified time: 2016-11-25 18:25:30
 */

// Change current directory to the directory of current script
chdir(dirname(__DIR__));
require 'app/Mage.php';
require 'app/MageMulti.php';

if (!isset($argv[1])) {
    Mage::throwException('Error: CLIENT_CODE not specified');
}
$code = $argv[1];
$_SERVER['CLIENT_CODE'] = $code;
if (!MageMulti::isInstalled()) {
    echo "Application is not installed yet, please complete install wizard first.";
    exit;
}

// Only for urls
// Don't remove this
$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_FILENAME']);

Mage::app('admin', 'store', MageMulti::getRunOptions($code))->setUseSessionInUrl(false);

umask(0);

$disabledFuncs = explode(',', ini_get('disable_functions'));
$isShellDisabled = is_array($disabledFuncs) ? in_array('shell_exec', $disabledFuncs) : true;
$isShellDisabled = (stripos(PHP_OS, 'win') === false) ? $isShellDisabled : true;

try {
    if (stripos(PHP_OS, 'win') === false) {
        $options = getopt('m::');
        if (isset($argv[2])) {
            switch ($argv[2]) {
                case 'always':
                case 'default':
                    $cronMode = $argv[2];
                    break;
                default:
                    Mage::throwException('Unrecognized cron mode was defined');
            }
        }else if (!$isShellDisabled) {
            $fileName = basename(__FILE__);
            $baseDir = dirname(__FILE__);
            shell_exec("/bin/sh $baseDir/mcron.sh $code $fileName default 1 > /dev/null 2>&1 &");
            shell_exec("/bin/sh $baseDir/mcron.sh $code $fileName always 1 > /dev/null 2>&1 &");
            exit;
        }
    }

    Mage::getConfig()->init()->loadEventObservers('crontab');
    Mage::app()->addEventArea('crontab');
    if ($isShellDisabled) {
        Mage::dispatchEvent('always');
        Mage::dispatchEvent('default');
    } else {
        Mage::dispatchEvent($cronMode);
    }
} catch (Exception $e) {
    Mage::printException($e);
    exit(1);
}