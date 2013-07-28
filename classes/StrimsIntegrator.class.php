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
 * StrimsIntegrator - główne akcje wtyczki
 */
class StrimsIntegrator extends StrimsIntegratorWordpress
{        
    /**
     * Strona edycji ustawień
     */
    public function display_plugin_options_edit()
    {
        $data = Array(
            'form' => $this->get_options()
        );                
        echo $this->load_view("plugin_options_edit", $data);        
    }
    
    /**
     * Okienko przy publikacji postu
     */
    public function display_post_metabox($post_ID)
    {
        $data = Array();        
        $post = get_post($post_ID);
        $data['options'] = $this->get_options();
        $data['post_ID'] = $post->ID;
        $data['post_status'] = get_post_status($post_ID);        
        $data['manual_post_url'] = "http://strims.pl/dodaj?".http_build_query(Array(
            'tytul' => $post->post_title,
            'url'   => $post->guid,
            'strim' => $data['options']['default_strim']
        ));        
        
        echo $this->load_view("post_metabox", $data);
    }
    
    /**
     * Obsługa ajax - dodawanie linku
     */
    public function ajax_post_link()
    {
        // jako że to ajax nie chcemy generować żadnych wiadomości
        // TODO: usunąć, methoda post_link() powinna zwracać cały rezultat
        $this->_silent = true;
        
        // dodajemy link
        $result = $this->post_link($_POST['post_ID'], $_POST['strim']);
        
        // wypluwamy rezultat json
        $result = Array('ok' => $result ? 1 : 0);
        echo json_encode($result);
        exit ;
    }    
    
    /**
     * Jeśli TRUE wiadomości nie będą generowane
     * TODO: usunąć, obsługa wiadomości powinna być tylko w action_publish_post
     * @var bool
     */
    private $_silent = false;
    
    /**
     * Główna funkcja dodająca treść na Strims.pl na podstawie wpisu z 
     * TODO: usunąć, obsługa wiadomości powinna być tylko w action_publish_post
     * TODO: to powinno zwracać coś więcej niż TRUE/FALSE
     * @param integer $post_ID id wpisu WP
     * @param string $strim nazwa strimu
     * @return bool
     */
    public function post_link($post_ID, $strim = false)
    {
        // sprawdzamy opcje czy jest dostepny login i haslo
        $options = $this->get_options();
        if (empty($options['username']) || empty($options['password'])) {
            if(!$this->_silent) $this->add_admin_message('Ustaw login i hasło w Ustawienia &gt; Strims Integrator aby automatycznie dodawać treści na Strims.pl');            
            return;
        }        
        
        // logowanie
        if (!$this->API()->login($options['username'], $options['password'])) {
            if(!$this->_silent) $this->add_admin_message('Nie mogę się zalogować do Strims.pl jako ' . $options['username']);
            return;
        }
        
        // póki co wszystko OK,
        // pobieramy wpis z bazy WP
        $post = get_post($post_ID);
        
        $url = $post->guid;        
        //$url = str_replace('localhost', 'google.pl', $url); // do testów na localhost
                
        // wszystko gotowe, dodajemy treść
        $result = $this->API()->post_link($strim ? $strim : $options['default_strim'], $post->post_title, $url);
        
        // ups.
        if ($result == FALSE) {
            if(!$this->_silent) $this->add_admin_message('Nie udało się dodać treści do strimu na strims.pl (już istnieje albo brak uprawnień)');
            return FALSE;
        }
        
        // udało się
        if(!$this->_silent) $this->add_admin_message('Dodano treść do Strims.pl: <a href="http://strims.pl/t/'.$result.'">link</a>');        
        
        return TRUE;
    }

}
