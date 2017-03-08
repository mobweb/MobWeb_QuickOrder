<?php
/**
 * @author    Louis Bataillard <info@mobweb.ch>
 * @package    MobWeb_QuickOrder
 * @copyright    Copyright (c) MobWeb GmbH (https://mobweb.ch)
 */

class MobWeb_QuickOrder_Helper_Data extends Mage_Core_Helper_Abstract
{
    public $log_file = 'mobweb_quickorder.log';

    public function log($msg)
    {
        Mage::log($msg, NULL, $this->log_file);
    }
}