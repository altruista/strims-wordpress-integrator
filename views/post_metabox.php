<?php 
/**
 * Automatyczne dodawanie treści do strims.pl przy dodawaniu posta.
 * Integracja wordpress z strims.pl
 *
 * @author      http://strims.pl/u/altruista 
 * @link        https://github.com/altruista/strims-wordpress-integrator/
 * @license     http://www.gnu.org/licenses/gpl.txt
 */
?>
<?php if($post_status == 'publish'): ?>
<?php if($options['username'] && $options['password']): ?>
<div id="post-strim">
    Dodaj treść do strimu:
    <div style="padding:10px">
        <strong>s/</strong><input name="strim" type="text" value="<?php echo $options['default_strim']; ?>" />

        <?php submit_button('Dodaj treść'); ?>
    </div>    
</div>
<?php endif; ?>
<a href="<?php echo $manual_post_url; ?>" target="_blank">Dodaj treść ręcznie &raquo;</a>
<? else: ?>
Opublikuj post aby móc go dodać do <a href="http://strims.pl" target="_blank">Strims.pl</a>
<? endif; ?>


<script>
    
    var post_ID = '<?php echo $post_ID; ?>';
        
    jQuery('#submit').click(function(e){
        e.preventDefault();                
        var $btn = jQuery(this);
        if($btn.attr('disabled')) return ;
        var strim = jQuery('#post-strim input[name="strim"]').val();
        strim = jQuery.trim(strim);
        if(!strim) {
            alert("Podaj nazwę strimu");
            return ;
        }
        
        var data = {
            post_ID: post_ID,
            strim: strim,
            action: 'strims_post'
        }      
        $btn.attr('disabled', true);
        jQuery.post(ajaxurl, data, function(response) {
            $btn.attr('disabled', false);
            if(response.ok > 0) {
                alert("Dodano treść.")
            }else{
                alert("Nie można było dodać treści. Treść już istnieje albo brak uprawnień.");
            }
	});
        
        
    });
    
</script>    