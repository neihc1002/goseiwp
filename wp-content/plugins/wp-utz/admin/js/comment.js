jQuery(window).load(function ()
{
	/**
	 * Load on Specific Pages
	 */
	if (jQuery('body').hasClass("comment-php") || jQuery('body').hasClass("comment-new-php"))
	{
		wp_utz_comment_date();
	}
});

function wp_utz_comment_date()
{
	console.log(wp_utz_comment_obj);
	if (wp_utz_obj.affect_input > 0)
	{
		if (jQuery('#hidden_aa').length)
		{
			jQuery('#hidden_aa').val(wp_utz_comment_obj.date_aa);
			jQuery('#hidden_mm').val(wp_utz_comment_obj.date_mm);
			jQuery('#hidden_jj').val(wp_utz_comment_obj.date_jj);
			jQuery('#hidden_hh').val(wp_utz_comment_obj.date_hh);
			jQuery('#hidden_mn').val(wp_utz_comment_obj.date_mn);

			jQuery('#aa').val(wp_utz_comment_obj.date_aa);
			jQuery('#mm').val(wp_utz_comment_obj.date_mm);
			jQuery('#jj').val(wp_utz_comment_obj.date_jj);
			jQuery('#hh').val(wp_utz_comment_obj.date_hh);
			jQuery('#mn').val(wp_utz_comment_obj.date_mn);
		}
	}
	else
	{
		var tz = ' <span id="wp_utz_input_tz">'+wp_utz_obj.stz+'</span>';
		jQuery('#mn').after(tz);
	}
}
