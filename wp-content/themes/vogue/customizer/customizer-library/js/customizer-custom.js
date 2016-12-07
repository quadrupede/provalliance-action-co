/**
 * Customizer Custom Functionality
 *
 */
( function( $ ) {
    
    $( window ).load( function() {
        
        var vogue_upgrade_button = '<a href="' + upgrade_button.link + '" class="vogue-upgrade-btn" target="_blank">' + upgrade_button.text + '</a><div class="vogue-upgrade-txt">' + upgrade_button.sub_text + '</div>';    
        $( '.preview-notice' ).append( vogue_upgrade_button );
        
        //Show / Hide Color selector for slider setting
        var the_slider_select_value = $( '#customize-control-vogue-slider-type select' ).val();
        vogue_customizer_slider_check( the_slider_select_value );
        
        $( '#customize-control-vogue-slider-type select' ).on( 'change', function() {
            var slider_select_value = $( this ).val();
            vogue_customizer_slider_check( slider_select_value );
        } );
        
        function vogue_customizer_slider_check( slider_select_value ) {
            if ( slider_select_value == 'vogue-slider-default' ) {
                $( '#accordion-section-vogue-slider-section #customize-control-vogue-meta-slider-shortcode' ).hide();
                $( '#accordion-section-vogue-slider-section #customize-control-vogue-slider-cats' ).show();
            } else if ( slider_select_value == 'vogue-meta-slider' ) {
                $( '#accordion-section-vogue-slider-section #customize-control-vogue-slider-cats' ).hide();
                $( '#accordion-section-vogue-slider-section #customize-control-vogue-meta-slider-shortcode' ).show();
            } else {
                $( '#accordion-section-vogue-slider-section #customize-control-vogue-slider-cats' ).hide();
                $( '#accordion-section-vogue-slider-section #customize-control-vogue-meta-slider-shortcode' ).hide();
            }
        }
        
    } );
    
} )( jQuery );