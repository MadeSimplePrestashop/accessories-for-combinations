<?php

$sql = array();
$sql[] = '
CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'accessory_for_combinations` (
  `id_accessory_for_combinations` int(11) NOT NULL AUTO_INCREMENT,
  `id_product_1` int(11) NOT NULL,
  `id_product_attribute_1` int(11) NOT NULL,
  `id_product_2` int(11) NOT NULL,
  `id_product_attribute_2` int(11) NOT NULL,
  PRIMARY KEY (`id_accessory_for_combinations`)
) ENGINE = ' . _MYSQL_ENGINE_ . '  ';
?>