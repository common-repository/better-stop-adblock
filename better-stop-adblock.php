<?php
/*
Plugin Name: Better Stop AdBlock
Plugin URI: http://wordpress.org/plugins/better-stop-adblock/
Description: Rileva e Blocca i Visitatori che usano Adblock o hanno i Javascript disabilitati. Detects and Blocks Visitors who use Adblock or have Javascript disabled.
Version: 2.1
Author: CodeClan
Author URI: http://codeclan.altervista.org/
License: GPL3
 */

define ( 'BSA_ROOT_URL', plugin_dir_url( __FILE__ ) );
function BetterStopAdblock_i18n_init(){load_plugin_textdomain('better-stop-adblock',false,dirname( plugin_basename( __FILE__ ) ) . '/languages');}add_action('admin_init','BetterStopAdblock_i18n_init');
function admin_register_head(){;echo '<link rel="stylesheet" type="text/css" href="'.BSA_ROOT_URL.'css/bsa-styles.css" />';}add_action('admin_head','admin_register_head');
function color_bsa_piker(){echo '<script type="text/javascript" src="'.BSA_ROOT_URL.'bsacolor/bsacolor.js"></script>';} add_action('admin_head','color_bsa_piker');
$custom_url=get_option('bsa-custom-url');$select_url=get_option('bsa-select-url');$ex_value = explode("\r\n", $select_url);$session_time=get_option('bsa-session-time');$pagina="http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
function session_create_on(){
    session_start();// store session data
    if(!isset($_SESSION['on'])){$_SESSION['on']=0;}else {$_SESSION['on']++;}
    if($_SESSION['on']<get_option('bsa-session-time')){add_action('wp_footer','bsa_script',1);}else{}
}
function session_create_off(){
    session_start();// store session data
    if(!isset($_SESSION['off'])){$_SESSION['off']=0;}else {$_SESSION['off']++;}
    if($_SESSION['off']>get_option('bsa-session-time')){add_action('wp_footer','bsa_script',1);}else{}
}
function bsa_script(){require_once ('bsa-var.php');if(get_option('bsa-custom-doctype')=='0'){require ('js/bsa-script-no-doctype.js');}else{require ('js/bsa-script-doctype.js');};}
if (($custom_url==0) AND (in_array($pagina,$ex_value))){if($session_time==0){add_action('wp_footer','bsa_script',1);}else{add_action('get_header','session_create_off',1);}}
if (($custom_url==0) AND (!in_array($pagina,$ex_value))){if($session_time==0){}else{add_action('get_header','session_create_on',1);}}
if (($custom_url==1) AND (in_array($pagina,$ex_value))){if($session_time==0){}else{add_action('get_header','session_create_on',1);}}
if (($custom_url==1) AND (!in_array($pagina,$ex_value))){if($session_time==0){add_action('wp_footer','bsa_script',1);}else{add_action('get_header','session_create_off',1);}}
function bsa_menu(){add_menu_page('Better Stop AdBlock Settings','B. Stop AdBlock','administrator','bsa_settings_page','bsa_settings_page',BSA_ROOT_URL.'img/mini.png');add_submenu_page('bsa_settings_page','Info','Info','administrator','Info','bsa_info_page');add_action('admin_init','register_bsa_settings');}add_action('admin_menu','bsa_menu');function register_bsa_settings(){register_setting('bsa-option','bsa-description','esc_textarea');register_setting('bsa-option','bsa-adv');register_setting('bsa-option','bsa-url');register_setting('bsa-option','bsa-tit');register_setting('bsa-option','bsa-image');register_setting('bsa-option','bsa-time');register_setting('bsa-option','bsa-button');register_setting('bsa-option','bsa-color');register_setting('bsa-option','bsa-color-text');register_setting('bsa-option','bsa-opacity');register_setting('bsa-option','bsa-credits');register_setting('bsa-option','bsa-time-action');register_setting('bsa-option','bsa-session-time');register_setting('bsa-option','bsa-custom-url');register_setting('bsa-option','bsa-select-url');register_setting('bsa-option','bsa-custom-audio');register_setting('bsa-option','bsa-loop-audio');register_setting('bsa-option','bsa-custom-doctype');}
function bsa_settings_page(){?><div class="bsa-wrap">
    <div id="bsa-logo"><?php require_once ('bsa-head.php');?></div>
    <form id="bsa-form1" method="post" action="options.php"><?php settings_fields('bsa-option');?><?php do_settings_fields('bsa-option','');?><table class="bsa-form-table" border="1" bordercolor="#CCCCCC" style="background-color: " width="100%" cellpadding="5" cellspacing="5">
        <tr valign="top">
            <td>
                <img src="<?php echo BSA_ROOT_URL.'img/text.png';?>" alt="text" /><?php _e(' Messaggio. <br /><br />** Compilare il Testo  &gt;&gt;&gt;<br />( Lasciare vuoto per Default )','better-stop-adblock');?></td>
            <td><?php $settings=array('textarea_name'=>'bsa-description','wpautop'=>true,'textarea_rows'=>5,'media_buttons'=>false);wp_editor(html_entity_decode(get_option('bsa-description')!=''?get_option('bsa-description'):'<ol><li>Per favore! Disattivare Adblock per questo Sito!</li><li>Please! Deactivate Adblock For this Site!</li></ol>'),'bsa-description',$settings);?></td>
        </tr>
        <tr>
            <td colspan="2">
                <hr>
                <img src="<?php echo BSA_ROOT_URL.'img/settings.png';?>" alt="settings" /><b><?php _e(' Impostazioni','better-stop-adblock');?></b><hr>
            </td>
        </tr>
        <tr valign="top">
            <td>
                <input type="radio" id="bsa-mes" name="bsa-adv" value="0" <?php if(get_option('bsa-adv')=='0'){echo 'checked="checked"';};?>  /></td>
            <td><?php _e('Messaggio. ','better-stop-adblock');?></td>
        </tr>
        <tr valign="top">
            <td>
                <input type="radio" id="bsa-img" name="bsa-adv" value="1" <?php if(get_option('bsa-adv')=='1'){echo 'checked="checked"';}else if(get_option('bsa-adv')==''){echo 'checked="checked"';};?> /></td>
            <td><?php _e('Immagine. ','better-stop-adblock');?></td>
        </tr>
        <tr valign="top">
            <td>
                <input type="url" id="bsa-input" placeholder="/uploads/image" onfocus="this.placeholder=''" onblur="this.placeholder='/uploads/image'" name="bsa-image" value="<?php echo get_option('bsa-image')!=''?get_option('bsa-image'):BSA_ROOT_URL.'img/logo.png';?>" /></td>
            <td><?php _e('Url Immagine da Mostrare ai Visitatori. ( Lasciare vuoto per Default )','better-stop-adblock');?></td>
        </tr>
        <tr valign="top">
            <td>
                <input type="url" id="bsa-input" placeholder="/uploads/audio" onfocus="this.placeholder=''" onblur="this.placeholder='/uploads/audio'" name="bsa-custom-audio" value="<?php echo get_option('bsa-custom-audio');?>" /><input type="checkbox" id="bsa-urlall" name="bsa-loop-audio" value="checked" <?php if(get_option('bsa-loop-audio')=='checked'){echo 'checked="checked"';}else if(get_option('bsa-loop-audio')=='unchecked'){echo 'checked="unchecked"';};?> />
                <?php _e('Ripeti File','better-stop-adblock');?></td>
            <td><?php _e('Url File Audio','better-stop-adblock');?></td>
        </tr>
        <tr valign="top">
            <td>
                <input type="url" id="bsa-input" placeholder="http://codeclan.altervista.org/" onfocus="this.placeholder=''" onblur="this.placeholder='http://codeclan.altervista.org/'" name="bsa-url" value="<?php echo get_option('bsa-url');?>" /></td>
            <td><?php _e('Link Redirect Visitatori.','better-stop-adblock');?></td>
        </tr>
        <tr valign="top">
            <td>
                <input type="text" id="bsa-input" placeholder="<?php _e('Titolo del Link.','better-stop-adblock');?>" onfocus="this.placeholder=''" onblur="this.placeholder='<?php _e('Titolo del Link','better-stop-adblock');?>'" name="bsa-tit" value="<?php echo get_option('bsa-tit');?>" pattern="^[A-Za-z0-9 ?]*[A-Za-z0-9][A-Za-z0-9 ?]*$" /></td>
            <td><?php _e('Titolo da Assegnare al Link Redirect.','better-stop-adblock');?></td>
        </tr>
        <tr valign="top">
            <td>
                <input type="number" id="bsa-input" placeholder="<?php _e('Tempo in secondi.','better-stop-adblock');?>" onfocus="this.placeholder=''" onblur="this.placeholder='<?php _e('Tempo in secondi','better-stop-adblock');?>'" name="bsa-time" value="<?php echo get_option('bsa-time');?>" pattern="\d+" /></td>
            <td><?php _e('Tempo di attesa prima di chiudere il blocco. <br />( Lasciare vuoto per Disattivare )','better-stop-adblock');?></td>
        </tr>
        <tr valign="top">
            <td>
                <input type="text" id="bsa-input" placeholder="<?php _e('Testo Pulsante di Sblocco.','better-stop-adblock');?>" onfocus="this.placeholder=''" onblur="this.placeholder='<?php _e('Testo Pulsante di Sblocco','better-stop-adblock');?>'" name="bsa-button" value="<?php echo get_option('bsa-button');?>" pattern="^[A-Za-z0-9 ?]*[A-Za-z0-9][A-Za-z0-9 ?]*$" required /></td>
            <td><?php _e('Testo da Assegnare al Pulsante che Chiude la finestra di Blocco.','better-stop-adblock');?></td>
        </tr>
        <tr valign="top">
            <td>
                <input class="color" type="text" id="bsa-input" placeholder="<?php _e('Clicca e seleziona il colore.','better-stop-adblock');?>" onfocus="this.placeholder=''" onblur="this.placeholder='<?php _e('Clicca e seleziona il colore','better-stop-adblock');?>'" name="bsa-color" value="<?php echo get_option('bsa-color')!=''?get_option('bsa-color'):"#FFFFFF";?>" pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$" /></td>
            <td><?php _e('Colore di Fondo della finestra di Blocco. ( Clicca e seleziona il colore )','better-stop-adblock');?></td>
        </tr>
        <tr valign="top">
            <td>
                <input class="color" type="text" id="bsa-input" placeholder="<?php _e('Clicca e seleziona il colore.','better-stop-adblock');?>" onfocus="this.placeholder=''" onblur="this.placeholder='<?php _e('Clicca e seleziona il colore','better-stop-adblock');?>'" name="bsa-color-text" value="<?php echo get_option('bsa-color-text')!=''?get_option('bsa-color-text'):"#000000";?>" pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$" /></td>
            <td><?php _e('Colore del Testo della finestra di Blocco. ( Clicca e seleziona il colore )','better-stop-adblock');?></td>
        </tr>
        <tr valign="top">
            <td>
                <input type="number" id="bsa-input" placeholder="<?php _e('Esempio: 95 (default) ','better-stop-adblock');?>" onfocus="this.placeholder=''" onblur="this.placeholder='<?php _e('Esempio: 95 (default) ','better-stop-adblock');?>'" name="bsa-opacity" value="<?php echo get_option('bsa-opacity')!=''?get_option('bsa-opacity'):"95";?>" min="0" max="99" /></td>
            <td><?php _e('Grado di Trasparenza. ( Lasciare vuoto per Default )','better-stop-adblock');?></td>
        </tr>
        <tr valign="top">
            <td>
                <input type="number" id="bsa-input" placeholder="<?php _e('Tempo millisecondi.','better-stop-adblock');?>" onfocus="this.placeholder=''" onblur="this.placeholder='<?php _e('Tempo millisecondi','better-stop-adblock');?>'" name="bsa-time-action" value="<?php echo get_option('bsa-time-action')!=''?get_option('bsa-time-action'):"450";?>" pattern="\d+" /></td>
            <td style="color: red;"><?php _e('Tempo di attesa prima di Intervenire. ( Lasciare vuoto per Default )','better-stop-adblock');?></td>
        </tr>
        <tr valign="top">
            <td>
                <input type="number" id="bsa-input" placeholder="<?php _e('Abilitazione Sessione.','better-stop-adblock');?>" onfocus="this.placeholder=''" onblur="this.placeholder='<?php _e('Numero Sessione','better-stop-adblock');?>'" name="bsa-session-time" value="<?php echo get_option('bsa-session-time')!=''?get_option('bsa-session-time'):"0";?>" pattern="\d+" /></td>
            <td style="color: green;"><?php _e('Numero di volte per Sessione prima di intervenire. ( Lasciare vuoto per Disattivare )','better-stop-adblock');?></td>
        </tr>
        <tr valign="top">
            <td>
                <input type="radio" id="bsa-urlsingle" name="bsa-custom-url" value="0" <?php if(get_option('bsa-custom-url')=='0'){echo 'checked="checked"';};?>  /></td>
            <td style="color: blue;"><?php _e('Plugin Abilitato sulle Pagine inserite in URL Pagina. <br />( Se URL Pagina &eacute; Vuoto, Disabilita il Plugin su tutto il sito )','better-stop-adblock');?></td>
        </tr>
        <tr valign="top">
            <td>
                <input type="radio" id="bsa-urlall" name="bsa-custom-url" value="1" <?php if(get_option('bsa-custom-url')=='1'){echo 'checked="checked"';}else if(get_option('bsa-custom-url')==''){echo 'checked="checked"';};?> /></td>
            <td style="color: blue;"><?php _e('Plugin Disabilitato sulle Pagine inserite in URL Pagina. <br />( Se URL Pagina &eacute; Vuoto, Abilita il Plugin su tutto il sito )','better-stop-adblock');?></td>
        </tr>
        <tr valign="top">
            <td>
                <textarea id="bsa-input" rows="5" cols="100%" placeholder="http://your-page" onfocus="this.placeholder=''" onblur="this.placeholder='http://your-page'" name="bsa-select-url"><?php echo get_option('bsa-select-url');?></textarea></td>
            <td style="color: blue;"><?php _e('URL Pagina. ( Se vuoto le opzioni riguardano tutto il sito )<br /><br /> Immettere un URL per ogni riga','better-stop-adblock');?></td>
        </tr>
        <tr valign="top">
            <td>
                <input type="radio" id="bsa-no-doctype" name="bsa-custom-doctype" value="0" <?php if(get_option('bsa-custom-doctype')=='0'){echo 'checked="checked"';};?>  /></td>
            <td style="color: black;"><?php _e('No Doctype. <br />( Se il sito non contiene &lt;! DOCTYPE&gt; )','better-stop-adblock');?></td>
        </tr>
        <tr valign="top">
            <td>
                <input type="radio" id="bsa-doctype" name="bsa-custom-doctype" value="1" <?php if(get_option('bsa-custom-doctype')=='1'){echo 'checked="checked"';}else if(get_option('bsa-custom-doctype')==''){echo 'checked="checked"';};?> /></td>
            <td style="color: black;"><?php _e('Doctype. <br />( Se il sito contiene &lt;! DOCTYPE&gt; )','better-stop-adblock');?></td>
        </tr>
        <tr valign="top">
            <td>
                <input type="radio" id="bsa-input-box-off" name="bsa-credits" value="0" <?php if(get_option('bsa-credits')=='0'){echo 'checked="checked"';};?> />
                NO<input type="radio" id="bsa-input-box-on" name="bsa-credits" value="1" <?php if(get_option('bsa-credits')=='1'){echo 'checked="checked"';}else if(get_option('bsa-credits')==''){echo 'checked="checked"';};?> />
                OK</td>
            <td><?php _e('Seleziona se vuoi far apparire il link dell&#39;autore del plugin.','better-stop-adblock');?></td>
        </tr>
    </table>
        <p class="bsa-submit">
            <input type="submit" class="button-primary" value="<?php _e("SALVA");?>" /></p>
    </form>
</div><?php }
function bsa_info_page(){?><div class="bsa-wrap">
    <div id="bsa-logo"><?php require_once ('bsa-head.php');?></div><?php require_once ('bsa-info.php');?></div><?php }