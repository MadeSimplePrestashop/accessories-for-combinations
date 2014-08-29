<?php

$sql = array();
$sql[] = 'DROP TABLE `' . _DB_PREFIX_ . 'accessory_for_combinations`';
foreach ($sql as $s) {
    if (!Db::getInstance()->Execute($s)) {
        return false;
    }
}
?>