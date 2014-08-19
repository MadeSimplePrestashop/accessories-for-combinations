<?php

require_once(dirname(__FILE__) . '../../../config/config.inc.php');
require_once(dirname(__FILE__) . '../../../init.php');

//if (!Tools::getValue('ajax') || Tools::getValue('token') != sha1(_COOKIE_KEY_.'rpmoreproductscontentmoduleexpand'))
//  die('INVALID TOKEN');

$id_product = Tools::getValue('id_product');
$error = '';
$combinations = array();

if (!$id_product || !is_numeric($id_product)) {
    echo json_encode(array(
        'response' => 'false'
    ));
    die();
}



$product = new Product((int) $id_product, false, Context::getContext()->language->id, Context::getContext()->shop->id);
$attributes = $product->getAttributeCombinations(Context::getContext()->language->id);

// secound round with don't touching cases
if ($attributes) {
    foreach ($attributes as $attr_key => $row) {
        $combinations[$row['id_product_attribute']]['attributes_values'][$row['id_attribute_group']] = $row['attribute_name'];
        $combinations[$row['id_product_attribute']]['attributes_names'][$row['id_attribute_group']] = $row['attribute_name'];
    }
}
echo json_encode(array(
    'response' => 'ok',
    'combinations' => $combinations
));



die();
?>