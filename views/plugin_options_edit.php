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
 * Nie używałem WP Settings API
 * http://codex.wordpress.org/Settings_API
 * bo więcej problemów z tym niż pożytku
 * 
 * Nie ma to jak samemu klepnąć HTML ;]
 */
?>
<div class="wrap">
<h2>Strims Integrator</h2>

<img src="http://strims.pl/media/images/others/logo_main.png" alt="Strims.pl" />

<form method="post" action="options.php">
<?php settings_fields('si-options'); ?>
    <h4>Nazwa użytkownika na Strims.pl:</h4>
    <div style="margin-left:20px">
        <input type="text" name="strims_integrator_username" value="<?php echo htmlentities($form['username']); ?>" />
    </div>
    
    <h4>Hasło na Strims.pl:</h4>
    <div style="margin-left:20px">
        <input type="password" name="strims_integrator_password" value="<?php echo htmlentities($form['password']); ?>" /><br/>        
        (hasło jest zapisane w bazie jako <span style="font-family:monospace">plain-text</span>)
    </div>
    
    <h4>Strim:</h4>
    <div style="margin-left:20px">
        <input type="text" name="strims_integrator_default_strim" value="<?php echo htmlentities($form['default_strim']); ?>" /><br/>
        Strim do którego ma się dodawać treść (np. s/<strong>Ciekawostki</strong>. Podaj nazwę strimu bez "s/")
    </div>
    
    <h4>Automatycznie publikuj treść:</h4>
    <div style="margin-left:20px">
        <select name="strims_integrator_auto_publish">
            <option value="0" <?php if(!$form['auto_publish']) { echo " selected"; } ?>>Dodawaj tylko ręcznie</option>
            <option value="1" <?php if($form['auto_publish']) { echo " selected"; } ?>>Automatyczne dodawanie treści włączone</option>            
        </select><br/>
        Wybierz czy chcesz aby treść automatycznie dodawała się do Strims.pl gdy publikujesz wpis.
    </div>
  
    
    <?php submit_button('Zapisz ustawienia'); ?>

</form>
</div>