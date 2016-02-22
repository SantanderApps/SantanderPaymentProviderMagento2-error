<?php

/**
 * Testconnection
 *
 * @file Testconnection.php
 * @author Consid AB <henrik.soderlind@consid.se>
 * @created 2015-dec-03
 */

namespace Santander\Easycontract\Controller\Adminhtml;

/**
 * Adminhtml test connection controller
 */
class Testconnection extends \Magento\Backend\App\Action
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
        $wsdlConnection = $this->helper->api->getTransferInformation();
        $sfConnection = $this->helper->api->getTransferInformation('sf');
        
        $wsdlResult = [
            'success' => $wsdlConnection['result'],
            'message' => $wsdlConnection['result']
            ? $this->helper->api->_('Success! Connected to {host}', [
                '{host}' => parse_url($wsdlConnection['info']['url'], PHP_URL_HOST),
            ])
            : $this->helper->api->_('<p>Error! Failed to connect to {host}.<br>It may be due to some of the following reasons:</p><ul><li>The server is not available at the moment.</li><li>Your server do not have an outbound Internet connection.</li></ul>', [
                '{host}' => parse_url($wsdlConnection['info']['url'], PHP_URL_HOST)
            ]),
        ];
        
        $sfResult = [
            'success' => $sfConnection['result'],
            'message' => $sfConnection['result']
            ? $this->helper->api->_('Success! Connected to {host}', [
                '{host}' => parse_url($sfConnection['info']['url'], PHP_URL_HOST)
            ])
            : $this->helper->api->_('<p>Error! Failed to connect to {host}.<br>It may be due to some of the following reasons:</p><ul><li>The server is not available at the moment.</li><li>Your server do not have an outbound Internet connection.</li></ul>', [
                '{host}' => parse_url($sfConnection['info']['url'], PHP_URL_HOST)
            ]),
        ];
        
        $response = [
            'wsdl' => $wsdlResult,
            'sf' => $sfResult,
        ];
        
        /* @var $resultJson \Magento\Framework\Controller\Result\Json */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}
