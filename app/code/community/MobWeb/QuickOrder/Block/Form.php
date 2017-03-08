<?php
/**
 * Description
 *
 * @author    Louis Bataillard <info@mobweb.ch>
 * @package    MobWeb_QuickOrder
 * @copyright    Copyright (c) MobWeb GmbH (https://mobweb.ch)
 */

class MobWeb_QuickOrder_Block_Form extends Mage_Core_Block_Template
{
    /**
     * Returns the instructions as defined in the configuration.
     *
     * @return String $instructions
     */
    public function getInstructions()
    {
        return Mage::getStoreConfig('mobweb_quickorder/instruction_text/instruction_text');
    }

    /**
     * Returns the number of lines to be shown in the quick order form.
     *
     * @return String $instructions
     */
    public function getNumberOfLines()
    {
        $numberOfLines = Mage::getStoreConfig('mobweb_quickorder/configuration/number_of_lines');
        if ($numberOfLines && is_numeric($numberOfLines)) {
            return $numberOfLines;
        }

        return 20;
    }

    /**
     * Returns the URL for the form action
     *
     * @return String $formAction
     */
    public function getFormAction()
    {
        return $this->getUrl('mobweb_quickorder/index/post');
    }
}