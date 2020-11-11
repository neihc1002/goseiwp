

jQuery('.trp-ls-shortcode-current-language').click(function () {
             jQuery( '.trp-ls-shortcode-current-language' ).addClass('trp-ls-clicked');
             jQuery( '.trp-ls-shortcode-language' ).addClass('trp-ls-clicked');
        });

        jQuery('.trp-ls-shortcode-language').click(function () {
            jQuery( '.trp-ls-shortcode-current-language' ).removeClass('trp-ls-clicked');
            jQuery( '.trp-ls-shortcode-language' ).removeClass('trp-ls-clicked');
        });