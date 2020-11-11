<?php
/**
 * Neve functions.php file
 *
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      17/08/2018
 *
 * @package Neve
 */

define( 'NEVE_VERSION', '2.8.2' );
define( 'NEVE_INC_DIR', trailingslashit( get_template_directory() ) . 'inc/' );
define( 'NEVE_ASSETS_URL', trailingslashit( get_template_directory_uri() ) . 'assets/' );

if ( ! defined( 'NEVE_DEBUG' ) ) {
	define( 'NEVE_DEBUG', false );
}
define( 'NEVE_NEW_DYNAMIC_STYLE', true );
/**
 * Themeisle SDK filter.
 *
 * @param array $products products array.
 *
 * @return array
 */
function neve_filter_sdk( $products ) {
	$products[] = get_template_directory() . '/style.css';

	return $products;
}

add_filter( 'themeisle_sdk_products', 'neve_filter_sdk' );

add_filter( 'themeisle_onboarding_phprequired_text', 'neve_get_php_notice_text' );

/**
 * Get php version notice text.
 *
 * @return string
 */
function neve_get_php_notice_text() {
	$message = sprintf(
	/* translators: %s message to upgrade PHP to the latest version */
		__( "Hey, we've noticed that you're running an outdated version of PHP which is no longer supported. Make sure your site is fast and secure, by %s. Neve's minimal requirement is PHP 5.4.0.", 'neve' ),
		sprintf(
		/* translators: %s message to upgrade PHP to the latest version */
			'<a href="https://wordpress.org/support/upgrade-php/">%s</a>',
			__( 'upgrading PHP to the latest version', 'neve' )
		)
	);

	return wp_kses_post( $message );
}

/**
 * Adds notice for PHP < 5.3.29 hosts.
 */
function neve_php_support() {
	printf( '<div class="error"><p>%1$s</p></div>', neve_get_php_notice_text() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

if ( version_compare( PHP_VERSION, '5.3.29' ) <= 0 ) {
	/**
	 * Add notice for PHP upgrade.
	 */
	add_filter( 'template_include', '__return_null', 99 );
	switch_theme( WP_DEFAULT_THEME );
	unset( $_GET['activated'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	add_action( 'admin_notices', 'neve_php_support' );

	return;
}

require_once 'globals/migrations.php';
require_once 'globals/utilities.php';
require_once 'globals/hooks.php';
require_once 'globals/sanitize-functions.php';
require_once get_template_directory() . '/start.php';


require_once get_template_directory() . '/header-footer-grid/loader.php';



function cat_count( $atts ) {
	$count = 0;
   if ( isset( $atts['cat'] ) ) {
	   $cats = $atts['cat'];
	   $category = get_category_by_slug( $cats );
	   if ( !is_object( $category ) ) {return;}
	   $count = $category->category_count;
	   } else 
	   {
		   $count = wp_count_posts( 'post' )->publish;
		   }
   
   return $count;
}

function change_blog_image_size() {
	remove_image_size( 'neve-blog' );
	add_image_size( 'neve-blog', 930, 620, true );
}
add_action( 'after_setup_theme', 'change_blog_image_size' );

add_shortcode( 'category_count', 'cat_count' );

/**
 * Registers the shortcode [wpp_views_count].
 *
 * @author Hector Cabrera (https://cabrerahector.com)
 * @return string
 */


function getPostViews($postID){
    $count_key = 'views';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "";
    }
    return $count;
}

function setPostViews($postID) {
    $count_key = 'views';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}

add_action( 'wp_enqueue_scripts', 'misha_script_and_styles', 1 );

function misha_script_and_styles() {
	global $wp_query;


	wp_enqueue_style( 'parent_style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'twentyseventeen-style', get_stylesheet_uri(), array('parent_style'), time() );


	// register our main script but do not enqueue it yet
	wp_register_script( 'misha_scripts', get_stylesheet_directory_uri() . '/pagination.js', array('jquery'), time() );

	// now the most interesting part
	// we have to pass parameters to myloadmore.js script but we can get the parameters values only in PHP
	// you can define variables directly in your HTML but I decided that the most proper way is wp_localize_script()
	wp_localize_script( 'misha_scripts', 'misha_loadmore_params', array(
		'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
		'posts' => json_encode( $wp_query->query_vars ), // everything about your loop is here
		'current_page' => $wp_query->query_vars['paged'] ? $wp_query->query_vars['paged'] : 1,
		'max_page' => $wp_query->max_num_pages,
		'first_page' => get_pagenum_link(1)
	) );
	
 	wp_enqueue_script( 'misha_scripts' );
}


add_action('wp_ajax_loadmore_post', 'misha_loadmore_ajax_handler'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_loadmore_post', 'misha_loadmore_ajax_handler'); // wp_ajax_nopriv_{action}

function misha_loadmore_ajax_handler(){
	// prepare our arguments for the query
	$args = json_decode( stripslashes( $_POST['query'] ), true );
	$args['paged'] = $_POST['page'] + 1; // we need next page to be loaded
	$args['post_status'] = 'publish';
	$args['cat'] =  $_POST['cat_id'];

	// it is always better to use WP_Query but not here
	query_posts( $args );

	if( have_posts() ) :

		// run the loop
		while( have_posts() ): the_post();

			get_template_part( 'template-parts/content', get_post_type() );

		endwhile;
		misha_paginator( $_POST['first_page'] );
	endif;
	die; // here we exit the script and even no wp_reset_query() required!
}




function misha_paginator( $first_page_url ){

	// the function works only with $wp_query that's why we must use query_posts() instead of WP_Query()
	global $wp_query;

	// remove the trailing slash if necessary
	$first_page_url = untrailingslashit( $first_page_url );


	// it is time to separate our URL from search query
	$first_page_url_exploded = array(); // set it to empty array
	$first_page_url_exploded = explode("/?", $first_page_url);
	// by default a search query is empty
	$search_query = '';
	// if the second array element exists
	if( isset( $first_page_url_exploded[1] ) ) {
		$search_query = "/?" . $first_page_url_exploded[1];
		$first_page_url = $first_page_url_exploded[0];
	}

	// get parameters from $wp_query object
	// how much posts to display per page (DO NOT SET CUSTOM VALUE HERE!!!)
	$posts_per_page = (int) $wp_query->query_vars['posts_per_page'];
	// current page
	$current_page = (int) $wp_query->query_vars['paged'];
	// the overall amount of pages
	$max_page = $wp_query->max_num_pages;

	// we don't have to display pagination or load more button in this case
	if( $max_page <= 1 ) return;

	// set the current page to 1 if not exists
	if( empty( $current_page ) || $current_page == 0) $current_page = 1;

	// you can play with this parameter - how much links to display in pagination
	$links_in_the_middle = 4;
	$links_in_the_middle_minus_1 = $links_in_the_middle-1;

	// the code below is required to display the pagination properly for large amount of pages
	// I mean 1 ... 10, 12, 13 .. 100
	// $first_link_in_the_middle is 10
	// $last_link_in_the_middle is 13
	$first_link_in_the_middle = $current_page - floor( $links_in_the_middle_minus_1/2 );
	$last_link_in_the_middle = $current_page + ceil( $links_in_the_middle_minus_1/2 );

	// some calculations with $first_link_in_the_middle and $last_link_in_the_middle
	if( $first_link_in_the_middle <= 0 ) $first_link_in_the_middle = 1;
	if( ( $last_link_in_the_middle - $first_link_in_the_middle ) != $links_in_the_middle_minus_1 ) { $last_link_in_the_middle = $first_link_in_the_middle + $links_in_the_middle_minus_1; }
	if( $last_link_in_the_middle > $max_page ) { $first_link_in_the_middle = $max_page - $links_in_the_middle_minus_1; $last_link_in_the_middle = (int) $max_page; }
	if( $first_link_in_the_middle <= 0 ) $first_link_in_the_middle = 1;

	// begin to generate HTML of the pagination
	$pagination = '<nav id="misha_pagination" class="navigation pagination" role="navigation" style="display:none"><div class="nav-links">';

	// when to display "..." and the first page before it
	if ($first_link_in_the_middle >= 2 && $links_in_the_middle < $max_page) {
		$pagination.= '<a href="'. $first_page_url . $search_query . '" class="page-numbers">1</a>'; // first page

		if( $first_link_in_the_middle != 2 )
			$pagination .= '<span class="page-numbers extend">...</span>';

	}

	// arrow left (previous page)
	if ($current_page != 1)
		//$pagination.= '<a href="'. $first_page_url . '/page/' . ($current_page-1) . $search_query . '" class="prev page-numbers">' . twentyseventeen_get_svg( array( 'icon' => 'arrow-left' ) ) . '</a>';
		$pagination.= '<a href="'. $first_page_url . '/page/' . ($current_page-1) . $search_query . '" class="prev page-numbers"></a>';


	// loop page links in the middle between "..." and "..."
	for($i = $first_link_in_the_middle; $i <= $last_link_in_the_middle; $i++) {
		if($i == $current_page) {
			$pagination.= '<span class="page-numbers current">'.$i.'</span>';
		} else {
			$pagination .= '<a href="'. $first_page_url . '/page/' . $i . $search_query .'" class="page-numbers">'.$i.'</a>';
		}
	}

	// arrow right (next page)
	if ($current_page != $last_link_in_the_middle )
		//$pagination.= '<a href="'. $first_page_url . '/page/' . ($current_page+1) . $search_query .'" class="next page-numbers">' . twentyseventeen_get_svg( array( 'icon' => 'arrow-right' ) ) . '</a>';
		$pagination.= '<a href="'. $first_page_url . '/page/' . ($current_page-1) . $search_query . '" class="prev page-numbers"></a>';


	// when to display "..." and the last page after it
	if ( $last_link_in_the_middle < $max_page ) {

		if( $last_link_in_the_middle != ($max_page-1) )
			$pagination .= '<span class="page-numbers extend">...</span>';
		
		$pagination .= '<a href="'. $first_page_url . '/page/' . $max_page . $search_query .'" class="page-numbers">'. $max_page .'</a>';
	}

	// end HTML
	$pagination.= "</div></nav>\n";

	// haha, this is our load more posts link
	if( $current_page < $max_page )
		$pagination.= '<div id="misha_loadmore" class="misha_loadmore">More posts</div>';

	// replace first page before printing it
	echo str_replace(array("/page/1?", "/page/1\""), array("?", "\""), $pagination);
}

// Remove issues with prefetching adding extra views
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

function wpp_views_count_func() {
    if ( function_exists('getPostViews') ) {
        $views_count = getPostViews(get_the_ID());
        return $views_count;
    }
    return '';
}
add_shortcode( 'wpp_views_count', 'wpp_views_count_func' );