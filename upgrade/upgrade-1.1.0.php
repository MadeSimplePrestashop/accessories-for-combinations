<?php

/**
 * Module Sliders Everywhere
 * 
 * @author 	kuzmany.biz
 * @copyright 	kuzmany.biz/prestashop
 * @license 	kuzmany.biz/prestashop
 * Reminder: You own a single production license. It would only be installed on one online store (or multistore)
 */
if (!defined('_PS_VERSION_'))
    exit;


function upgrade_module_1_1_0($module) {
    Configuration::updateValue('AFC_CART_NBR', 0);
    $module->registerHook('displayShoppingCartFooter');
    return $module;
}
