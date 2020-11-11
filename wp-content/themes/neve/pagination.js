jQuery(function($){

	/*
	 * Load More
	 */
	$('body').on('click', '.misha_loadmore', function(){
        let sefl = $(this) 
        let parent = $(this).parent('.tab-pane');
        let currentPage = parent.data('page')
        console.log(currentPage)
        let nextPage = currentPage+1
        let catId = parent.attr('id')
        console.log(catId)
		$.ajax({
			url : misha_loadmore_params.ajaxurl, // AJAX handler
			data : {
				'action': 'loadmore_post', // the parameter for admin-ajax.php
				'query': misha_loadmore_params.posts, // loop parameters passed by wp_localize_script()
				'page' : currentPage, // current page
                'first_page' : misha_loadmore_params.first_page,
                'cat_id': catId
			},
			type : 'POST',
			beforeSend : function ( xhr ) {
				sefl.text('Loading...'); // some type of preloader
			},
			success : function( data ){
                console.log('dâd',data)
					sefl.remove();
					parent.find('.pagination').before(data);
                    misha_loadmore_params.current_page++;
                    parent.data('page',nextPage)


			}
		});
		return false;
	});

});
