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
$products = afc::getProductAttributeAccessories($id_product, $id_product_attribute);
$accessories = array();
foreach ($products as $key => $p) {
    $product = new Product($p['id_product_2'], false, Context::getContext()->language->id, Context::getContext()->shop->id);
    $attributes = $product->getAttributeCombinationsById($p['id_product_attribute_2'], Context::getContext()->language->id);
    //image
    $product->attributes = $attributes;
    $tmp = array();
    if ($attributes)
        foreach ($attributes as $attribute) {
            $tmp['without'][] = $attribute['attribute_name'];
            $tmp['with'][] = $attribute['group_name'] . ' ' . $attribute['attribute_name'];
        }

    $product->attributes_names = isset($tmp['without']) ? implode(', ', $tmp['without']) : '';
    $product->attributes_group_names = isset($tmp['with']) ? implode(', ', $tmp['with']) : '';

    $product->images = Image::getImages(Context::getContext()->language->id, $p['id_product_2'], $p['id_product_attribute_2']);
    if (!$product->images)
        $product->images = array(0 => Product::getCover($p['id_product_2']));
    $product->id_product = $p['id_product_2'];
    $product->id_product_attribute = $id_product_attribute;
    $tmp = array();
    foreach ($attributes as $row) {
        $tmp[] = str_replace(Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR'), '_', Tools::link_rewrite(str_replace(array(',', '.'), '-', $row['group_name']))) . Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR') . Tools::link_rewrite(str_replace(array(',', '.'), '-', $row['attribute_name']));
    }
    $product->url_hash = "#/" . implode('/', $tmp);
    $accessories[] = $product;
}

if (empty($accessories)) {
    echo Tools::jsonEncode(array(
        'response' => 'false'
    ));
    return;
}

Context::getContext()->smarty->assign(array('accessories' => $accessories));
//return $this->context->smarty->fetch(dirname(__FILE__).'/views/templates/admin/configuration.tpl');
echo Tools::jsonEncode(array(
    'response' => 'ok',
    'template' => Context::getContext()->smarty->fetch(dirname(__FILE__) . '/views/templates/hook/product_footer_template.tpl')
));

die();
?>