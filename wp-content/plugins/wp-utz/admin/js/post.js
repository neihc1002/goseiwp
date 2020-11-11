jQuery(window).load(function ()
{
	/**
	 * Load on Specific Pages
	 */
	if (jQuery('body').hasClass("post-php") || jQuery('body').hasClass("post-new-php"))
	{
		wp_utz_post_date();
	}
	else if (jQuery('body').hasClass("edit-php"))
	{
		wp_utz_post_list_quickedit();
	}
});

function wp_utz_post_date()
{
	if (wp_utz_obj.affect_input > 0)
	{
		// this is for a non gutenberg post type form
		if (jQuery('#hidden_aa').length)
		{
			jQuery('#hidden_aa').val(wp_utz_post_obj.date_aa);
			jQuery('#hidden_mm').val(wp_utz_post_obj.date_mm);
			jQuery('#hidden_jj').val(wp_utz_post_obj.date_jj);
			jQuery('#hidden_hh').val(wp_utz_post_obj.date_hh);
			jQuery('#hidden_mn').val(wp_utz_post_obj.date_mn);

			jQuery('#aa').val(wp_utz_post_obj.date_aa);
			jQuery('#mm').val(wp_utz_post_obj.date_mm);
			jQuery('#jj').val(wp_utz_post_obj.date_jj);
			jQuery('#hh').val(wp_utz_post_obj.date_hh);
			jQuery('#mn').val(wp_utz_post_obj.date_mn);
		}
		else
		{
			var local_date = wp_utz_post_obj.date_aa+'-'+wp_utz_post_obj.date_mm+'-'+wp_utz_post_obj.date_jj+' '+wp_utz_post_obj.date_hh+':'+wp_utz_post_obj.date_mn+':00';
			wp.data.select('core/editor').getEditedPostAttribute('date');
			wp.data.dispatch('core/editor').editPost({ date: local_date });
		}
	}
	else
	{
		var tz = ' <span id="wp_utz_input_tz">'+wp_utz_obj.stz+'</span>';
		jQuery('.edit-post-post-schedule .edit-post-post-schedule__toggle').after(tz);
		jQuery('#mn').after(tz);
	}
}

function wp_utz_post_list_quickedit()
{
	if (wp_utz_obj.affect_input > 0)
	{
		// select the target node
		var target = document.querySelector('.wp-list-table');

		// create an observer instance
		var observer = new MutationObserver(function(mutations)
		{
			mutations.forEach(function(mutation)
			{
				jQuery('.wp-list-table .quick-edit-row.inline-editor').each(function()
				{
					var post_id = jQuery(this).attr('id').replace('edit-', '');

					if (jQuery(this).find('.inline-edit-date').length > 0)
					{
						var tmp = jQuery('utz_'+post_id+'_aa').text();
						jQuery(this).find('input[name="aa"]').val(jQuery('#utz_'+post_id+'_aa').text());
						jQuery(this).find('input[name="mm"]').val(jQuery('#utz_'+post_id+'_mm').text());
						jQuery(this).find('input[name="jj"]').val(jQuery('#utz_'+post_id+'_jj').text());
						jQuery(this).find('input[name="hh"]').val(jQuery('#utz_'+post_id+'_hh').text());
						jQuery(this).find('input[name="mn"]').val(jQuery('#utz_'+post_id+'_mn').text());
					}
				});
			});
		});

		// configuration of the observer:
		var config = { attributes: true, childList: true, characterData: true };

		// pass in the target node, as well as the observer options
		observer.observe(target, config);
	}
	else
	{
		var tz = ' <span id="wp_utz_input_tz">'+wp_utz_obj.stz+'</span>';

		jQuery('.wp-list-table').on('DOMSubtreeModified', function()
		{
			jQuery('.quick-edit-row').each(function()
			{
				if (jQuery(this).find('#wp_utz_input_tz').length == 0)
				{
					jQuery(this).find('input[name="mn"]').after(tz);
				}
			});
		});
	}
}
