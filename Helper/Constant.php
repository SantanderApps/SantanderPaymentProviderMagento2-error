<?php

/**
 * Constant
 *
 * @file Constant.php
 * @author Consid AB <henrik.soderlind@consid.se>
 * @created 2015-nov-30
 */

namespace Santander\Easycontract\Helper;

class Constant
{
    // Environment modes
    const STATUS_TEST = 'test';
    const STATUS_LIVE = 'live';
    
    // Payment gateway code
    const CODE = 'easycontract';
    
    // Token session key
    const TOKEN_SESSION_KEY = 'SANTANDER_TOKEN';
}
