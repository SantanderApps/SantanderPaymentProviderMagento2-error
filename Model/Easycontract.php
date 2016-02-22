<?php

/**
 * Easycontract
 *
 * @file Easycontract.php
 * @author Consid AB <henrik.soderlind@consid.se>
 * @created 2015-nov-26
 */

namespace Santander\Easycontract\Model;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Payment\Model\Method\AbstractMethod;

class Easycontract extends AbstractMethod
{
    /**
     * @var string
     */
    protected $_code;

    /**
     * @var string
     */
    protected $_formBlockType = 'Santander\Easycontract\Block\Form';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Santander\Easycontract\Block\Info';

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_isGateway = true;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_canUseCheckout = true;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_isInitializeNeeded = true;
    
    /**
     *
     * @var \Magento\Sales\Model\OrderFactory 
     */
    protected $_orderFactory;
    
    /**
     *
     * @var \Magento\Sales\Model\Order 
     */
    protected $_order;
    
    /**
     *
     * @var \Santander\Easycontract\Model\Session\Token
     */
    protected $_tokenSession;
    
    /**
     * 
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;
    
    /**
     * @var \Santander\Easycontract\Helper\LowLevelApiHelper
     */
    protected $_helper;
    
    public function __construct(
        \Magento\Framework\Model\Context $context, 
        \Magento\Framework\Registry $registry, 
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory, 
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory, 
        \Magento\Payment\Helper\Data $paymentData, 
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, 
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Santander\Easycontract\Model\Session\Token $tokenSession,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Santander\EasyContract\Helper\LowLevelApiHelper $helper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, 
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, 
        array $data = []
    ) {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $paymentData, $scopeConfig, $logger, $resource, $resourceCollection, $data);
        $this->_helper = $helper;
        $this->_orderFactory = $orderFactory;
        $this->_tokenSession = $tokenSession;
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * @see \Magento\Payment\Model\Method\AbstractMethod::isAvailable()
     */
    public function isAvailable(CartInterface $quote = null)
    {
        if ($quote === null) {
            return parent::isAvailable();
        }
        
        return (parent::isAvailable($quote) && $this->_getToken($quote));
    }
    
    public function getCheckoutRedirectUrl()
    {
        return $this->_urlBuilder->getUrl('easycontract/redirect');
    }
    
    protected function _getToken(CartInterface $quote)
    {
        $reservedOrderId = $quote->getReservedOrderId();
        $grandTotal = $quote->getGrandTotal();
        $token = $this->_helper->api->getToken($reservedOrderId, $grandTotal);
        
        if (!$token) {
            throw new \Exception($this->_helper->api->_('An error occured while communicating with Santander Consumer Bank. Try again or choose another payment method.'));
            return false;
        } elseif (!$token->isOk) {
            throw new \Exception($token->errorMessage);
            return false;
        }
        
        $this->_tokenSession->setToken($token, $reservedOrderId);
        return true;
    }
    
    /**
     * Get an order
     * @param int $orderId
     * @return \Magento\Sales\Model\Order
     * @throws Exception
     */
    protected function _getOrder($orderId)
    {
        $this->_order = $this->_orderFactory->create()->loadByIncrementId($orderId);
        if (!$this->_order->getId()) {
            throw new \Exception(sprintf('Wrong order ID: "%s".', $orderId));
        }
        return $this->_order;
    }
}
