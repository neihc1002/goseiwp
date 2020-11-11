jQuery(document).ready(function($)
{
	wp_utz_admin_bar_time()
});

function wp_utz_admin_bar_time()
{
	if (jQuery('#wp_utz_admin_bar_time').length)
	{
		var d = new Date();

		setTimeout(function()
		{
			wp_utz_admin_bar_update_time();

			setInterval(function()
			{
				wp_utz_admin_bar_update_time();
			}, 60000);
		}, ((60 - d.getSeconds()) + 1) * 1000);
	}
}

function wp_utz_admin_bar_update_time()
{
	// get Endpoint options
	jQuery.ajax(
	{
		type: "POST"
		,url: wp_utz_obj.ajax_url
		,data: {
			action: 'wp_utz_admin_bar'
		}
	}).done(function( rtn )
	{
		if (rtn.status == 1)
		{
			jQuery('#wp_utz_admin_bar_time').html(rtn.title);
		}
	});
}
