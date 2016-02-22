<?php

/**
 * Verifydetails
 *
 * @file Verifydetails.php
 * @author Consid AB <henrik.soderlind@consid.se>
 * @created 2015-dec-03
 */

namespace Santander\Easycontract\Controller\Adminhtml;

class Verifydetails extends \Magento\Backend\App\Action
{
    /**
     * @var \Santander\EasyContract\Helper\LowLevelApiHelper 
     */
    protected $helper;
    
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Santander\EasyContract\Helper\LowLevelApiHelper $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->resultJsonFactory = $resultJsonFactory;
    }
    
    public function execute()
    {
        $result = $this->helper->api->testConnection();
        $response = [
            'success' => $result,
            'message' => $result
            ? $this->helper->api->_('Success! The test connection with the web service works great. Your account details is correct.')
            : $this->helper->api->_('Error! The test connection with the web service failed. It seems like your account details are incorrect. Make sure that they are correct, if it still doesn\'t work please {contactUs}.', [
                'contactUs' => '<a href="' . Santander::$api->config->getClientSiteUrl() . '" target="_blank"><u>contact us</u></a>'
            ]),
        ];
        
        /* @var $resultJson \Magento\Framework\Controller\Result\Json */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}
