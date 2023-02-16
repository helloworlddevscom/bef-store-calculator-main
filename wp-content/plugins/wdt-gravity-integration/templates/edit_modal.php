<?php defined('ABSPATH') or die('Access denied.'); ?>

<!--Set entry id if for editing or 0 if it's new entry-->
<?php $entryId = isset($_POST['entry_id']) ?  absint($_POST['entry_id'] ) : 0 ?>

<div class="wdt-gf-file-editing-alert" style="display: none">
    <?php _e('Editing files is not supported at the moment', 'wpdatatables'); ?>
</div>

<div class="wdt-gf-edit-header">
    <input type='hidden' name='gform_ajax' value='<?php echo esc_attr( "form_id={$form['id']}&amp;title={$form['title']}&amp;description={$form['description']}&amp;tabindex=0" ) ?>' />
    <input type='hidden' class='gform_hidden' name='gform_submit'  value='<?php echo esc_attr( $form['id'] ) ?>' />
    <input type='hidden' class='gform_hidden' name='<?php echo esc_attr( "is_submit_{$form['id']}") ?>' value='1' />
    <input type='hidden' class='gform_hidden' name='state_<?php echo esc_attr( $form['id'] ) ?>' value='<?php echo \GFFormDisplay::get_state( $form, $field_values = array() ) ?>' />
    <input type='hidden' name='entry_id' id='entry_id' value='<?php echo $entryId ?>' />
    <?php if ($form['requireLogin']) {
        echo wp_nonce_field( 'gform_submit_' . $form['id'], '_gform_submit_nonce_' . $form['id'], true, false );
    } ?>
</div>

<?php
if($hasCaptchaField){
    echo '<div class="captcha-error alert alert-warning alert-dismissible">'.__('Editing Gravity forms which contain Captcha is not supported in wpDataTables. Please disable Captcha.','wpdatatables').'</div>';
} else {
    if ($entryId) {
        //Return form with entry data
        echo \GFEntryDetail::lead_detail_edit($form, \GFAPI::get_entry($entryId));
        
        //Add inline scripts necessery for mask inputs
        wp_enqueue_script('gform_gravityforms');
        \GFFormDisplay::register_form_init_scripts($form, null, true);
        echo \GFFormDisplay::get_form_init_scripts($form);
        echo    "<script type='text/javascript'>" .
            "var current_page = jQuery('#gform_source_page_number_{$form['id']}').val();" .
            "jQuery(document).trigger('gform_post_render', [{$form['id']}, current_page]);" .
            "</script>";
    } else {
        //Return empty form for new data
        echo \RGForms::get_form($form['id'], true, true, false, null, true, 0);
    }
}
?>

<?php $currentSkin = get_option('wdtBaseSkin');
$skinsWithNewEditButtons = ['aqua','purple','dark']; ?>

<!-- Modal footer block with OK and Cancel buttons -->
<div class="wdt-gf-edit-dialog-button-block"
     style="display: block">
    <!-- Hidden input which holds value used as form action -->
    <input type='hidden' name='wdt_home_page' id='wdt_home_page' value='<?php echo rtrim(site_url(), '/') . '/' ?>'  />
    <!--/ Hidden input which holds value used as form action -->
    <button class="btn btn-danger btn-icon-text waves-effect wdt-cancel-edit-button" data-dismiss="modal">
        <i class="zmdi zmdi-close"></i>
        <?php _e('Cancel', 'wpdatatables'); ?>
    </button>
    <button class="wdt-gf-submit btn btn-success btn-icon-text waves-effect wdt-ok-edit-button">
        <i class="zmdi <?php if(in_array($currentSkin, $skinsWithNewEditButtons)) { echo 'zmdi-check-circle'; } else { echo 'zmdi-check-all'; }?>"></i>
        <?php if(in_array($currentSkin, $skinsWithNewEditButtons)) { _e('Submit', 'wpdatatables');} else {_e('OK', 'wpdatatables');} ?>
    </button>
</div>
<!-- /Modal footer block with OK and Cancel buttons -->