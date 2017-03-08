<?php
/**
 * @author    Louis Bataillard <info@mobweb.ch>
 * @package    MobWeb_QuickOrder
 * @copyright    Copyright (c) MobWeb GmbH (https://mobweb.ch)
 */

class MobWeb_QuickOrder_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Checks if the customer is logged in and authenticated to view the current page
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();
    
        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    /**
     * Renders the page
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Validates the submitted form and adds the products to the cart
     */
    public function postAction()
    {
        $helper = Mage::helper('mobweb_quickorder');

        $data = $this->getRequest()->getPost();

        // Prepare the success and error messages arrays
        $successMessages = array();
        $errorMessages = array();

        // Get the reference to the current quote
        $quote = Mage::getSingleton('checkout/session')->getQuote();

        // Loop through the submitted SKUs
        for ($i = 0; $i < count($data['sku']); $i++) {
            $sku = $data['sku'][$i];

            // Skip empty SKUs
            if (!$sku) {
                continue;
            }

            // Try and find the product for the specified SKU
            $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
            if (!$product || !$product->getId()) {
                $errorMessages[] = $helper->__('The product with the SKU "%s" was not found and could not be added to the cart.', $sku);
                continue;
            }

            // If the product is a configurable product, it can not be added to the cart
            if ($product->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                $errorMessages[] = $helper->__('The product with the SKU "%s" is a configurable product which can not be added to the cart directly. Please specify the SKU of one of the product\'s child products.', $sku);
                continue;
            }

            // Verify the quantity entered. If it's illegal, we simply assume a quantity of 1
            if (!isset($data['quantity'][$i])) {
                $data['quantity'][$i] = 1;
            }

            $quantity = $data['quantity'][$i];

            if (!is_numeric($quantity) || $quantity < 1) {
                $quantity = 1;
            }

            // Try and add the product to the cart
            try {
                $quote->addProduct($product, $quantity);
            } catch(Exception $e) {
                $errorMessages[] = $helper->__('The product with the SKU "%s" could not be added to the cart. Please try again.', $sku);
                continue;
            }

            $successMessages[] = $helper->__('The product with the SKU "%s" was successfully added to the cart.', $sku);
        }

        // Save the quote
        try {
            $quote->collectTotals()->save();
        } catch(Exception $e) {
            $errorMessages[] = $helper->__('There was a problem adding your products to the cart. Please try again.');
        }

        // Add the messages
        foreach ($successMessages as $message) {
            Mage::getSingleton('core/session')->addSuccess($message); 
        }

        foreach ($errorMessages as $message) {
            Mage::getSingleton('core/session')->addError($message); 
        }

        // Redirect to the previous page
        $this->_redirect('mobweb_quickorder/index/index');
    }
}  