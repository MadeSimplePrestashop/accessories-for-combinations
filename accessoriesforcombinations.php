<?php

/**
 * Module Accessories for combinations 
 * 
 * @author 	kuzmany.biz
 * @copyright 	kuzmany.biz/prestashop
 * @license 	kuzmany.biz/prestashop
 * Reminder: You own a single production license. It would only be installed on one online store (or multistore)
 */

if (!defined('_PS_VERSION_'))
	exit;

require_once(dirname(__FILE__) . '/models/afc.php');

class accessoriesforcombinations extends Module {

    const separator = ', ';

    public function __construct() {
        $this->name = 'accessoriesforcombinations';
        $this->tab = 'front_office_features';
        $this->author = 'kuzmany.biz/prestashop';
        $this->module_key = 'fb368f630844011a03b5f0a9a2fd75aa';
        $this->bootstrap = true;
        $this->need_instance = 0;
        $this->version = '1.1';
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
                OR ! $this->registerHook('displayShoppingCartFooter')
        )
            return false;
        Configuration::updateValue('AFC_CART_NBR', 0);
        include_once(dirname(__FILE__) . '/init/install_sql.php');
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
        return true;
    }

    public function getContent() {

        $this->_postProcess();
        return $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm() {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSnazzyavailabilityModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
                . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm() {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Accessories from combinations on cart (crosseling)'),
                        'name' => 'AFC_CART',
                        'is_bool' => true,
                        'hint' => $this->l('Accessories will be viewed bellow the shopping cart as slider.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 1,
                        'type' => 'text',
                        'name' => 'AFC_CART_NBR',
                        'label' => $this->l('Number of accessories in cart'),
                        'hint' => $this->l('Use 0 for unlimited products.'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submit'
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues() {
        return array(
            'AFC_CART' => Configuration::get('AFC_CART', false),
            'AFC_CART_NBR' => Configuration::get('AFC_CART_NBR', 0),
        );
    }

    protected function _postProcess() {

        if (!Tools::isSubmit('submit'))
            return;

        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key)
            Configuration::updateValue($key, Tools::getValue($key));
    }

    public function hookDisplayShoppingCartFooter($params) {

        //no cart
        if (!Configuration::get('AFC_CART'))
            return;

        $this->context->controller->addJqueryPlugin(array('bxslider'));
        $this->context->controller->addJS($this->_path . 'js/afc-cart.js');
        $this->context->controller->addCSS($this->_path . 'css/afc-cart.css');

        $products = isset($params['products']) ? $params['products'] : array();
        if (!is_array($products) || empty($products))
            return;
        $accessories = array();
        foreach ($products as $product) {
            $get_accessories = afc::getAccessories($product['id_product'], $product['id_product_attribute'], $products);
            foreach ($get_accessories as $get_accessorie)
                $accessories[] = $get_accessorie;
        }
        if (!count($accessories))
            return;

        $afc_cart_nbr = Configuration::get('AFC_CART_NBR');
        if ($afc_cart_nbr > 0)
            $accessories = array_slice($accessories, 0, $afc_cart_nbr);

        $template = dirname(__FILE__) . '/views/templates/hook/product_footer_template.tpl';
        $this->context->smarty->assign(array('accessories' => $accessories, 'static_token' => Tools::getToken(false)));
        $this->context->smarty->assign(array('html' => $this->context->smarty->fetch($template)));
        $template = dirname(__FILE__) . '/views/templates/hook/afc-cart.tpl';
        return $this->context->smarty->fetch($template);
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
        foreach (array_reverse($accessories) as $a) {
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
        $this->context->controller->addJqueryPlugin(array('bxslider'));
        $this->context->controller->addJS($this->_path . 'js/imagesloaded.pkgd.min.js');
        $this->context->controller->addJS($this->_path . 'js/afc-product.js');
        $this->context->controller->addCSS($this->_path . 'css/afc-product.css');
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

}

?>