<?php

/**
 * Token
 *
 * @file Token.php
 * @author Consid AB <henrik.soderlind@consid.se>
 * @created 2015-dec-03
 */

namespace Santander\Easycontract\Model\Session;

class Token extends \Magento\Framework\Session\SessionManager
{
    /**
     * Write token to session
     * @param \Santander\LowLevelAPI\Model\Token $token
     * @param int $orderNumber
     * @return \Santander\Easycontract\Model\Session\Token
     * @throws Exception
     */
    public function setToken($token, $orderNumber)
    {
        if (!$token) {
            throw new Exception('Token can not be empty');
        }
        
        if (!$orderNumber) {
            throw new Exception('OrderNumber can not be empty');
        }
        
        $data = [
            'token' => $token,
            'orderNumber' => $orderNumber,
        ];
        $data = serialize($data);
        
        $this->storage->setData(\Santander\Easycontract\Helper\Constant::TOKEN_SESSION_KEY, $data);
        return $this;
    }
    
    /**
     * Get token from session
     * @return \Santander\Easycontract\Data\Token
     */
    public function getToken()
    {
        $sessionData = $this->storage->getData(\Santander\Easycontract\Helper\Constant::TOKEN_SESSION_KEY);
        
        if (!$sessionData) {
            return null;
        }
        
        $unserialized = unserialize($sessionData);
        
        if (!$unserialized) {
            return null;
        }
        
        $token = new \Santander\Easycontract\Data\Token();
        $token->token = $unserialized['token'];
        $token->orderNumber = $unserialized['orderNumber'];
        return $token;
    }
    
    /**
     * Clear token session
     * @return \Santander\Easycontract\Model\Session\Token
     */
    public function clearToken()
    {
        $this->storage->unsetData(\Santander\Easycontract\Helper\Constant::TOKEN_SESSION_KEY);
        return $this;
    }
}
