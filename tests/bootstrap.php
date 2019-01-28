<?php
// fix empty CFG_GLPI on boostrap; see https://github.com/sebastianbergmann/phpunit/issues/325
global $CFG_GLPI;

//define plugin paths
define("PLUGINACTUALTIME_DOC_DIR", __DIR__ . "/generated_test_data");

define('GLPI_ROOT', dirname(dirname(dirname(__DIR__))));
define("GLPI_CONFIG_DIR", GLPI_ROOT . "/tests");
include GLPI_ROOT . "/inc/includes.php";
include_once GLPI_ROOT . '/tests/GLPITestCase.php';
include_once GLPI_ROOT . '/tests/DbTestCase.php';

//install plugin
$plugin = new \Plugin();
$plugin->getFromDBbyDir('actualtime');

//check from prerequisites as Plugin::install() does not!
if (!plugin_actualtime_check_prerequisites()) {
   echo "\nPrerequisites are not met!";
   die(1);
}

if (!$plugin->isInstalled('actualtime')) {
   echo "tem que instalar\n";
   call_user_func([$plugin, 'install'], $plugin->getID());
}
echo "instalado\n";
if (!$plugin->isActivated('actualtime')) {
   echo "tem que ativar\n";
   call_user_func([$plugin, 'activate'], $plugin->getID());
}
echo "ativado\n";
