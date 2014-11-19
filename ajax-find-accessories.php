<?php

/**
 * Module Accessories for combinations 
 * 
 * @author 	kuzmany.biz
 * @copyright 	kuzmany.biz/prestashop
 * @license 	kuzmany.biz/prestashop
 * Reminder: You own a single production license. It would only be installed on one online store (or multistore)
 */
require_once(dirname(__FILE__) . '../../../config/config.inc.php');
require_once(dirname(__FILE__) . '../../../init.php');

//if (!Tools::getValue('ajax') || Tools::getValue('token') != sha1(_COOKIE_KEY_.'rpmoreproductscontentmoduleexpand'))
//  die('INVALID TOKEN');
require_once(dirname(__FILE__) . '/models/afc.php');

$id_product = Tools::getValue('id_product');
$id_product_attribute = Tools::getValue('id_product_attribute');
$cache_id = md5(dirname(__FILE__) . 'ipa' . $id_product_attribute . 'ip' . $id_product);
$compile_id = 'afc-' . $id_product_attribute . '-' . $id_product;
$template = dirname(__FILE__) . '/views/templates/hook/product_footer_template.tpl';

$accessories = afc::getAccessories($id_product, $id_product_attribute);
if (empty($accessories)) {
    echo Tools::jsonEncode(array(
        'response' => 'false'
    ));
    return;
}
Context::getContext()->smarty->assign(array('accessories' => $accessories, 'static_token' => Tools::getToken(false)));

echo Tools::jsonEncode(array(
    'response' => 'ok',
    'template' => Context::getContext()->smarty->fetch($template)
));

die();
?>