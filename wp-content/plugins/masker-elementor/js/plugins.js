/**
 * Masker masks your images by creative & extra-ordinary custom shapes.
 * Exclusively on Envato Market: https://1.envato.market/maskerelementor
 *
 * @encoding        UTF-8
 * @version         1.0.0
 * @copyright       Copyright (C) 2018 - 2020 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Envato License https://1.envato.market/KYbje
 * @contributors    Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

( function ( $ ) {
    
    "use strict";
    
    jQuery( document ).ready( function () {
        
        jQuery( '.mdp-masker-elementor-rating-stars' ).find( 'a' ).hover(
            function() {
                jQuery( this ).nextAll( 'a' ).children( 'span' ).removeClass( 'dashicons-star-filled' ).addClass( 'dashicons-star-empty' );
                jQuery( this ).prevAll( 'a' ).children( 'span' ).removeClass( 'dashicons-star-empty' ).addClass( 'dashicons-star-filled' );
                jQuery( this ).children( 'span' ).removeClass( 'dashicons-star-empty' ).addClass( 'dashicons-star-filled' );
            }
        );

    } );

} ( jQuery ) );