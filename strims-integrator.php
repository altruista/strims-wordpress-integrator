<?php
/*
Plugin Name: Strims integrator
Plugin URI: https://github.com/altruista/strims-wordpress-integrator/
Description: Pochwal się swoim blogiem i zwiększ ilość odwiedzin przez automatyczne dodawanie linków do swojego bloga na strims.pl
Version: 1.0
Author: altruista
Author URI: http://strims.pl/u/altruista
License: GPL
*/

/**
 * Automatyczne dodawanie treści do strims.pl przy dodawaniu posta.
 * Integracja wordpress z strims.pl
 *
 * @author      http://strims.pl/u/altruista 
 * @link        https://github.com/altruista/strims-wordpress-integrator/
 * @license     http://www.gnu.org/licenses/gpl.txt
 */

define('STRIMS_INTEGRATOR_PLUGIN_DIR', dirname(__FILE__));
require_once STRIMS_INTEGRATOR_PLUGIN_DIR . "/classes/StrimsIntegrator/Base.class.php";
require_once STRIMS_INTEGRATOR_PLUGIN_DIR . "/classes/StrimsIntegrator/Wordpress.class.php";
require_once STRIMS_INTEGRATOR_PLUGIN_DIR . "/classes/StrimsIntegrator.class.php";

register_activation_hook(__FILE__, function() {
    StrimsIntegratorWordpress::get_instance()->activate_plugin();    
}); 

register_deactivation_hook(__FILE__, function() {
    StrimsIntegratorWordpress::get_instance()->deactivate_plugin();
}); 

if(!is_admin()) {
    return ;
}

StrimsIntegratorWordpress::get_instance()->register_actions();
