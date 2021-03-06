jQuery(document).ready(function(){
    jQuery(document).on('click', '#wcml_translations_message', function( e ){
        e.preventDefault();
        jQuery.ajax({
            type : "post",
            url : ajaxurl,
            data : {
                action: "hide_wcml_translations_message",
                wcml_nonce: jQuery('#wcml_hide_languages_notice').val()
            },
            success: function(response) {
                jQuery('#wcml_translations_message').remove();
            }
        });
    });

    jQuery(document).on('click', '#icl_save_language_selection', function( ){

        jQuery('#icl_avail_languages_picker li input').each( function(){
            if( jQuery(this).is(':checked') ){
                if( jQuery('#default_language_'+jQuery(this).val()).length === 0 ){
                    jQuery('<p class="icl_ajx_response" style="display: block">'+wcml_settings.warn+'</p>').insertBefore('#icl_ajx_response');
                    return false;
                }
            }
        });
    });

});