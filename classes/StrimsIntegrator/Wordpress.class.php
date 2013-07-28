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
 * Obsługa akcji wordpress, zbiór metod dla obsługi wordpressa,
 * pobieranie danych z bazy wp
 */
class StrimsIntegratorWordpress extends StrimsIntegratorBase
{
    /**
     * Opcje wtyczki
     * @var array
     */
    protected $plugin_options = Array(
        'username', 
        'password', 
        'default_strim',
        'auto_publish'
    );
    
    /**
     * Prefix dla opcji wtyczki.
     * Opcja "username" to w WP tak naprawdę "strims_integrator_username"
     * @var string
     */
    protected $plugin_options_prefix = "strims_integrator_";
    
    /**
     * Akcje WP które wtyczka obsługuje
     * TODO: to mogłoby samo się uzupełniać w konstruktorze
     * @var array
     */
    protected $plugin_actions = Array(
        'admin_menu',
        'admin_init',
        'admin_notices',
        'publish_post',
        'init',
        'add_meta_boxes',
        'wp_ajax_strims_post'
    );
    
    /**
     * Dodaje wiadomość dla admina
     * @param string $text
     * @param string $class
     */
    public function add_admin_message($text, $class = 'updated')
    {
        $admin_messages = &$_SESSION['si_admin_messages'];
        if (!is_array($admin_messages)) {
            $admin_messages = Array();
        }
        $admin_messages[] = Array(
            'text' => "Strims integrator: {$text}",
            'class' => $class
        );
    }
    
    /**
     * Dopisuje zdarzenia do akcji wordpresa
     */
    public function register_actions()
    {        
        foreach($this->plugin_actions as $action) {                                                
            add_action($action, function() use($action) {
                StrimsIntegratorWordpress::get_instance()->trigger_action($action, func_get_args());                
            });
        }        
    }
    
    /**
     * Obsługa akcji Wordpress
     * @param string $action nazwa akcji np. "admin_menu"
     * @param array $arguments argumenty akcji
     */
    public function trigger_action($action, $arguments)
    {
        $method_name = "action_{$action}";
        if (!method_exists($this, $method_name)) {
            throw Exception("StrimsIntegrator: Undefined action {$action}");
        }
        call_user_func_array(Array($this, $method_name), $arguments);
    }
    
    /**
     * Zwraca pojedyńczą opcję wtyczki
     * @param string $option nazwa opcji np. 'username'
     * @return string
     */
    public function get_option($option)
    {
        return get_option($this->plugin_options_prefix . $option);
    }
    
    /**
     * Zwraca wszystkie opcje wtyczki
     * @return array
     */
    public function get_options()
    {
        $options = Array();
        foreach($this->plugin_options as $option) {
            $options[$option] = $this->get_option($option);            
        }        
        return $options;
    }
    
    /*************************************************************
     * Akcje Wordpress
     *************************************************************/
    
    /**
     * @see http://codex.wordpress.org/Function_Reference/register_activation_hook
     */    
    public function activate_plugin()
    {
        foreach($this->plugin_options as $option) {
            add_option($this->plugin_options_prefix . $option, '', '', 'yes');            
        }        
    }
    
    /**
     * @see http://codex.wordpress.org/Function_Reference/register_deactivation_hook
     */    
    public function deactivate_plugin()
    {
        foreach($this->plugin_options as $option) {
            delete_option($this->plugin_options_prefix . $option);
        }
    }
    
    /**
     * @see http://codex.wordpress.org/Plugin_API/Action_Reference/init
     */
    protected function action_init()
    {
        if (!session_id()) {
            session_start();
        }
    }    
    
    /**
     * @see http://codex.wordpress.org/Plugin_API/Action_Reference/admin_menu
     */
    protected function action_admin_menu()
    {   
        add_options_page(
            'Strims Integrator', 
            'Strims Integrator', 
            'administrator',
            'strims-integrator', 
            function() {
                StrimsIntegrator::get_instance()->display_plugin_options_edit();
            }
        );
    }
    
    /**
     * @see http://codex.wordpress.org/Plugin_API/Action_Reference/admin_init
     */
    protected function action_admin_init()
    {
        foreach ($this->plugin_options as $option) {
            register_setting('si-options', $this->plugin_options_prefix . $option);
        }
    }    
    
    /**
     * @see http://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
     */
    protected function action_admin_notices()
    {
        $admin_messages = $_SESSION['si_admin_messages'];
        if (empty($admin_messages)) {
            return;
        }
        foreach ($admin_messages as $message) {
            echo $this->load_view('message', Array('message' => $message['text'], 'class' => $message['class']));
        }
        $_SESSION['si_admin_messages'] = Array();
    }
    
    /**
     * @see http://codex.wordpress.org/Plugin_API/Action_Reference/publish_post
     */
    protected function action_publish_post($post_ID)
    {
        // na to powinna być osobna metoda w StrimsIntegrator z obsługą wiadomości
        if ($this->get_option('auto_publish')) {
            StrimsIntegrator::get_instance()->post_link($post_ID);
        }
    }
    
    /**
     * @see http://codex.wordpress.org/Plugin_API/Action_Reference/add_meta_boxes
     */
    protected function action_add_meta_boxes()
    {
        add_meta_box( 
            'si_metabox',
            'Strims.pl integrator',
            function($post_ID) {
                StrimsIntegrator::get_instance()->display_post_metabox($post_ID);
            },
            'post',
            'side',
            'high' 
        );
    }
    
    /**
     * @see http://codex.wordpress.org/Plugin_API/Action_Reference/wp_ajax_%28action%29
     */
    protected function action_wp_ajax_strims_post()
    {
        StrimsIntegrator::get_instance()->ajax_post_link();        
    }
}
