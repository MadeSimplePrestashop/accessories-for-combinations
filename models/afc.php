<?php

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

}

?>