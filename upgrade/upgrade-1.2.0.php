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

function upgrade_module_1_2_0($module) {
    $module->registerHook('productActions');
    $module->registerHook('productExtraLeft');
    $module->registerHook('productExtraRight');
    $module->registerHook('productFooter');
    $module->registerHook('productTab');
    $module->registerHook('productTabContent');

    //set module
    $hm_list = Hook::getHookModuleExecList();
    $is = false;
    foreach ($hm_list as $hook_name => $hl) {
        if ($is != false)
            continue;
        foreach ($hl as $h) {
            foreach ($module->product_hooks as $product_hook) {
                if (strpos($hook_name, strtolower($product_hook)) !== false && $h['module'] == $module->name) {
                    $is = $product_hook;
                }
            }
        }
    }
    if ($is)
        Configuration::updateValue('AFC_HOOK', $is);
    return $module;
}
