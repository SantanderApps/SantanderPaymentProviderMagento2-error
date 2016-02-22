<?php

/**
 * LowLevelApiHelper
 *
 * @file LowLevelApiHelper.php
 * @author Consid AB <henrik.soderlind@consid.se>
 * @created 2015-nov-30
 */

namespace Santander\EasyContract\Helper;

use Santander\LowLevelAPI\App;
use Santander\LowLevelAPI\Base\APIConnectorInterface;
use Santander\LowLevelAPI\Base\Config;
use Santander\Easycontract\Helper\Constant;

class LowLevelApiHelper implements APIConnectorInterface
{
    /**
     *
     * @var \Santander\LowLevelAPI\App
     */
    public $api;
    
    /**
     *
     * @var \Santander\LowLevelAPI\Base\Logger 
     */
    public $logger;
    
    /**
     *
     * @var \Magento\Store\Api\Data\StoreInterface 
     */
    protected $_store;
    
    /**
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    
    /**
     *
     * @var \Magento\FrameworkEncryption\EncryptorInterface 
     */
    protected $_encryptor;
    
    public function __construct(
        \Magento\Store\Api\Data\StoreInterface $store,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\FrameworkEncryption\EncryptorInterface $encryptor
    ) 
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_store = $store;
        $this->_encryptor = $encryptor;
        
        if (!App::isRunning()) {
            App::run($this);
            $this->api = App::$api;
            $this->logger = App::$logger;
        }
    }
    
    public function getCode()
    {
        return Constant::CODE;
    }
    
    protected function _getConfigData($field, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->_store->getId();
        }
        
        $path = 'payment/' . $this->getCode() . '/' . $field;
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    
    public function getPlatformData($configKey)
    {
        switch ($configKey) {
            case Config::CONFIG_KEY_TEST_MODE:
                return $this->_getMode() == Constant::STATUS_TEST;
                break;
            case Config::CONFIG_KEY_SANDBOX_MODE:
                return $this->_getMode() == Constant::STATUS_TEST;
                break;
            case Config::CONFIG_KEY_STORE_ID:
                return $this->_getStoreId();
                break;
            case Config::CONFIG_KEY_USERNAME:
                return $this->_getUsername();
                break;
            case Config::CONFIG_KEY_PASSWORD:
                return $this->_getPassword();
                break;
            case Config::CONFIG_KEY_CERTIFICATE:
                return $this->_getCertificate();
                break;
            case Config::CONFIG_KEY_MERCHANT_ID:
                return $this->_getMerchantId();
                break;
            case Config::CONFIG_KEY_LANGUAGE:
                return $this->_getLanguage();
                break;
            case Config::CONFIG_KEY_SITE_EMAIL_ADDRESS:
                return $this->_getSiteMail();
                break;
            case Config::CONFIG_KEY_SITE_NAME:
                return $this->_getSiteName();
                break;
            case Config::CONFIG_KEY_PLATFORM_NAME:
                return $this->_getPlatformName();
                break;
            case Config::CONFIG_KEY_PLATFORM_VERSION:
                return $this->_getPlatformVersion();
                break;
            case Config::CONFIG_KEY_MODULE_VERSION:
                return $this->_getModuleVersion();
                break;
            case Config::CONFIG_KEY_MODULE_INSTALLATION_DATE:
                return $this->_getModuleInstallationDate();
                break;
            case Config::CONFIG_KEY_ENABLE_EXTENDED_LOGGING:
                return $this->_enableExtendedLogging();
                break;
            case Config::CONFIG_KEY_RETURN_URL:
                return $this->_getReturnUrl();
                break;
            case Config::CONFIG_KEY_ACCESS_LOG_EXTERNAL:
                return $this->_getAccessLogExternal();
                break;
        }
    }
    
    private function _getMode()
    {
        return $this->_getConfigData('environment');
    }
    
    private function _getStoreId()
    {
        return $this->_getConfigData('store_id');
    }
    
    private function _getUsername()
    {
        return $this->_getConfigData('username');
    }
    
    private function _getPassword()
    {
        // Fulhack
        $defaultPassword = 'testbutik1';
        $password = $this->_getConfigData('password');
        if ($password == $defaultPassword) {
            return $password;
        }
        else {
          return Mage::helper('core')->decrypt($this->_getConfigData('password'));  
        }
    }
    
    private function _getCertificate()
    {
        return;
    }
    
    private function _getMerchantId()
    {
        return $this->_getConfigData('merchant_id');
    }
    
    private function _getLanguage()
    {
        return Mage::getStoreConfig('general/locale/code', $this->_storeId);
    }
    
    private function _getSiteMail()
    {
        return Mage::getStoreConfig('trans_email/ident_general/email', $this->_storeId);
    }
    
    private function _getSiteName()
    {
        return Mage::app()->getStore()->getName();
    }
    
    private function _getPlatformName()
    {
        return 'Magento';
    }
    
    private function _getPlatformVersion()
    {
        return Mage::getVersion();
    }
    
    private function _getModuleVersion()
    {
        return (string)Mage::getConfig()->getModuleConfig('Santander_EasyContract')->version;
    }
    
    private function _getModuleInstallationDate()
    {
        return;
    }
    
    private function _enableExtendedLogging()
    {
        return TRUE;
    }
    
    private function _getReturnUrl()
    {
        return Mage::getUrl('easycontract/return');
    }
    
    private function _getAccessLogExternal()
    {
        return $this->_getConfigData('access_log_external');
    }
}
