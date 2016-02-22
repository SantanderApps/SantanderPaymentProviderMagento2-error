<?php

/**
 * EasycontractConfigProvider
 *
 * @file EasycontractConfigProvider.php
 * @author Consid AB <henrik.soderlind@consid.se>
 * @created 2015-dec-04
 */

namespace Santander\Easycontract\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;

class EasycontractConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string[]
     */
    protected $methodCode = \Santander\Easycontract\Helper\Constant::CODE;
    
    /**
     * @var \Santander\Easycontract\Model\Easycontract
     */
    protected $method;
    
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;
    
    public function __construct(
        PaymentHelper $paymentHelper,
        Magento\Framework\Escaper $escaper
    ) {
        $this->escaper = $escaper;
        $this->method = $paymentHelper->getMethodInstance($this->methodCode);
    }
    
    public function getConfig()
    {
        return $this->method->isAvailable() ? [
            'payment' => [
                'easycontract' => [
                    'redirectUrl' => $this->getRedirectUrl(),
                ],
            ],
        ] : [];
    }
    
    protected function getRedirectUrl()
    {
        return $this->method->getCheckoutRedirectUrl();
    }
}
