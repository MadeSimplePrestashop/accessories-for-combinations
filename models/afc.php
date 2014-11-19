<?php

/**
 * Module Accessories for combinations 
 * @copyright 	kuzmany.biz/prestashop
 * Reminder: You own a single production license. It would only be installed on one online store (or multistore)
 */
class afc extends ObjectModel {

    public $id_accessory_for_combinations;
    public $id_product_1;
    public $id_product_attribute_1;
    public $id_product_2;
    public $id_product_attribute_2;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'accessory_for_combinations',
        'primary' => 'id_accessory_for_combinations',
        'fields' => array(
            'id_product_1' => array('type' => self::TYPE_INT),
            'id_product_attribute_1' => array('type' => self::TYPE_INT),
            'id_product_2' => array('type' => self::TYPE_INT),
            'id_product_attribute_2' => array('type' => self::TYPE_INT)
        )
    );

    public static function getProductAccessories($id_product) {
        $sql = 'SELECT *
				FROM `' . _DB_PREFIX_ . self::$definition['table'] . '`
				WHERE `id_product_1` = ' . (int) $id_product;
        return Db::getInstance()->executeS($sql);
    }

    public static function getProductAttributeAccessories($id_product, $id_product_attribute) {
        $sql = 'SELECT *
				FROM `' . _DB_PREFIX_ . self::$definition['table'] . '`
				WHERE `id_product_1` = ' . (int) $id_product . ' AND id_product_attribute_1= ' . (int) $id_product_attribute;
        return Db::getInstance()->executeS($sql);
    }

    public static function deleteAccessories($id_product_1, $id_product_attribute_1, $id_product_2, $id_product_attribute_2) {
        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . self::$definition['table'] . '` WHERE `id_product_1` = ' . (int) $id_product_1 . ' AND `id_product_attribute_1` = ' . (int) $id_product_attribute_1 . ' AND `id_product_2` = ' . (int) $id_product_2 . ' AND `id_product_attribute_2` = ' . (int) $id_product_attribute_2);
    }

    public static function deleteFromAccessories($id_product, $id_product_attribute) {
        return Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . self::$definition['table'] . '` WHERE `id_product_1` = ' . (int) $id_product . ' AND `id_product_attribute_1` = ' . (int) $id_product_attribute);
    }

    public static function getAccessories($id_product, $id_product_attribute, $blocklist = array()) {
        $products = self::getProductAttributeAccessories($id_product, $id_product_attribute);
        $accessories = array();
        foreach ($products as $key => $p) {
            $block = false;
            foreach ($blocklist as $blockproduct)
                if ($p['id_product_2'] == $blockproduct['id_product'] && $p['id_product_attribute_2'] == $blockproduct['id_product_attribute'])
                    $block = true;
            if ($block)
                continue;

            $productObject = new Product($p['id_product_2'], false, Context::getContext()->language->id, Context::getContext()->shop->id);
            $product = Tools::jsonDecode(Tools::jsonEncode($productObject), true);
            $product['id_product'] = $p['id_product_2'];
            $product['id_product_attribute'] = $p['id_product_attribute_2'];
            $product = Product::getProductProperties(Context::getContext()->language->id, $product);
            $product = Tools::jsonDecode(Tools::jsonEncode($product));
            $attributes = $productObject->getAttributeCombinationsById($p['id_product_attribute_2'], Context::getContext()->language->id);
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
            $product->allow_oosp = Product::isAvailableWhenOutOfStock($product->out_of_stock);
            $product->images = Image::getImages(Context::getContext()->language->id, $p['id_product_2'], $p['id_product_attribute_2']);
            if (!$product->images)
                $product->images = array(0 => Product::getCover($p['id_product_2']));
            $tmp = array();
            foreach ($attributes as $row) {
                $tmp[] = str_replace(Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR'), '_', Tools::link_rewrite(str_replace(array(',', '.'), '-', $row['group_name']))) . Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR') . Tools::link_rewrite(str_replace(array(',', '.'), '-', $row['attribute_name']));
            }
            $product->url_hash = "#/" . implode('/', $tmp);
            $accessories[] = $product;
        }
        return $accessories;
    }

}

?>