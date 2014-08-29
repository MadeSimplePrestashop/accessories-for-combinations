<?php

/**
 * Module Accessories for combinations 
 * @copyright 	kuzmany.biz/prestashop
 * Reminder: You own a single production license. It would only be installed on one online store (or multistore)
 */
require_once(dirname(__FILE__) . '/models/afc.php');

class accessoriesforcombinations extends Module {

    const separator = ', ';

    public function __construct() {
        $this->name = 'accessoriesforcombinations';
        $this->version = '1.0';
        $this->tab = 'front_office_features';
        $this->author = 'kuzmany.biz/prestashop';
        $this->module_key = 'fb368f630844011a03b5f0a9a2fd75aa';
        parent::__construct();

        $this->displayName = $this->l('Accessories for combinations');
        $this->description = $this->l('Manage accessories for combinations');
    }

    public function install() {
        $this->version = 0; // To force execution of upgrade files

        if (!parent::install())
            return false;

        if (
                !$this->registerHook('extraRight')
                OR ! $this->registerHook('actionProductUpdate')
                OR ! $this->registerHook('displayBackOfficeFooter')
        )
            return false;

        include_once(dirname(__FILE__) . '/init/install_sql.php');
        $this->runSql($sql);

        return true;
    }

    public function uninstall() {
        if (!parent::uninstall())
            return false;

        if (
                !$this->registerHook('extraRight')
                OR ! $this->unregisterHook('actionProductUpdate')
                OR ! $this->unregisterHook('displayBackOfficeFooter')
        )
            return false;

        include_once(dirname(__FILE__) . '/init/uninstall_sql.php');
        $this->runSql($sql);
        return true;
    }

    public function hookActionProductUpdate($params) {
        //prevent duplicate
        if (Cache::retrieve(__FUNCTION__ . 'c'))
            return;

        if (!Tools::getIsset('id_product_attribute'))
            return;

        $id_product_1 = Tools::getValue('id_product');
        $id_product_attribute_1 = Tools::getValue('id_product_attribute');

        $afc_id_product_search = Tools::getValue('afc_id_product_search');
        $afc_id_product = Tools::getValue('afc_id_product');

        if (!is_array($afc_id_product_search) || !is_array($afc_id_product))
            return;

        $afc_id_product_attribute = Tools::getValue('afc_id_product_attribute');
        afc::deleteFromAccessories($id_product_1, $id_product_attribute_1);
        foreach ($afc_id_product_search as $key => $product_name) {
            if (empty($product_name) || empty($afc_id_product[$key]))
                continue;
            $sql = 'INSERT INTO `' . _DB_PREFIX_ . afc::$definition['table'] . '`
                    VALUES(null,"' . (int) $id_product_1 . '","' . (int) $id_product_attribute_1 . '","' . (int) $afc_id_product[$key] . '","' . (int) $afc_id_product_attribute[$key] . '")';
            Db::getInstance()->execute($sql);
        }

        Cache::store(__FUNCTION__ . 'c', 1);
    }

    public function hookDisplayBackOfficeFooter($params) {
        $id_product = Tools::getValue('id_product');
        if (get_class(Context::getContext()->controller) != 'AdminProductsController' OR $id_product == 0)
            return;

        $accessories = afc::getProductAccessories($id_product);
        $attrs = array();
        foreach (array_reverse($accessories) as $key => $a) {
            $product_name = Product::getProductName($a['id_product_2'], null, Context::getContext()->language->id);
            $attrs[$a['id_product_attribute_1']][] = array('id_product' => $a['id_product_2'], 'id_product_attribute' => $a['id_product_attribute_2'], 'name' => $product_name);
        }
        $this->context->smarty->assign(array(
            'addJS' => ' 
            var afc_accessories = ' . Tools::jsonEncode($attrs) . ';
             var baseUri = "' . __PS_BASE_URI__ . '";
             var baseDir = "' . __PS_BASE_URI__ . '";
             var afc_token = "' . sha1(_COOKIE_KEY_ . $this->module->name) . '";'
        ));

        return $this->display(__FILE__, 'backofficefooter.tpl');
    }

    private function load_template() {
        if (Cache::retrieve(__CLASS__ . 'c'))
            return;
        Cache::store(__CLASS__ . 'c', 1);
        return $this->display(__FILE__, 'product_tab_content.tpl');
    }

    public function hookProductActions($params) {
       return $this->load_template();
    }

    public function hookExtraRight($params) {
        return $this->load_template();
    }

    public function hookExtraLeft($params) {
        return $this->load_template();
    }

    public function hookProductFooter($params) {
        return $this->load_template();
    }

    public function hookProductTab($params) {
        return $this->display(__FILE__, 'product_tab.tpl');
    }

    public function hookProductTabContent($params) {
        return $this->load_template();
    }

    private function runSql($sql) {
        foreach ($sql as $s) {
            if (!Db::getInstance()->Execute($s)) {
                return false;
            }
        }
    }

}

?>