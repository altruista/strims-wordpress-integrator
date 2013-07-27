<?php 
/**
 * Automatyczne dodawanie treści do strims.pl przy dodawaniu posta.
 * Integracja wordpress z strims.pl
 *
 * @author      http://strims.pl/u/altruista 
 * @link        https://github.com/altruista/strims-wordpress-integrator/
 * @license     http://www.gnu.org/licenses/gpl.txt
 */

/**
 * Bazowa klasa wtyczki
 */
abstract class StrimsIntegratorBase
{    
    /**
     * Strims PHP Api
     * @var Strims 
     */
    private static $_api;
    
    /**
     * Konstruktor
     */
    public function __construct()
    {
        if (!class_exists('Strims')) {
            require_once STRIMS_INTEGRATOR_PLUGIN_DIR . "/classes/Strims.class.php";
        }        
    }
    
    /**
     * Tworzy obiekt StrimsIntegrator
     * @return StrimsIntegrator
     */
    public static function get_instance()
    {
        static $instance = null;
        if(!empty($instance)) {
            return $instance;
        }
        $instance = new static();
        return $instance;
    }
    
    /**
     * Tworzy i zwraca strims-php-api
     * @return Strims
     */
    public function API()
    {
        if(!empty($this->_api)) {
            return $this->_api;
        }
        $this->_api= new Strims(Array(
            'cookie_file' => tempnam(sys_get_temp_dir(), 'strims_cookies')            
        ));
        return $this->_api;
    }
    
    /**
     * Wczytuje widok i zwraca HTML
     * @param string $view_name nazwa widoku
     * @param array $data dane dla widoku
     * @return string
     */
    protected function load_view($view_name, $data = array())
    {
        extract($data);
        ob_start();
        include STRIMS_INTEGRATOR_PLUGIN_DIR . "/views/{$view_name}.php";
        return ob_get_clean();        
    }    
}