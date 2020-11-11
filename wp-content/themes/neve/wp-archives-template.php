<?php
/**
 * Template Name: Archives list page
 *
 * Custom archives template for wordpress that lists all the posts grouped under month/year. I use this on http://blog.fuss.in/archives/  
 * 
 * To use:
 * 1. Paste this file into wp-content/themes/<theme-name>/page-templates/
 * 2. Select page template as "Archives List Page" on the right side bar in "New Page", page.
 * 3. insert this short tag "[archives]", wherever you want the list to be displayed.
 * 
 * Note:
 * - You might need to edit line 30, to match your base path
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

// Fetch all posts and construct html
ob_start();
$myposts = get_posts('numberposts=-1&offset=$debut');
$mypost_data = array();
$group_order = array();
$group_links = array();
foreach( $myposts as $post ) {
  setup_postdata($post);
  $post_data = array(
      'group' => get_the_date('M, Y'),
      'group_link' => get_the_date('/Y/m/'),
      'date' => get_the_date('j M Y'),
      'Y' => get_the_date('Y'),
      'm' => get_the_date('m'),
      'M' => get_the_date('M'),
      'd' => get_the_date('d'),
      'permalink' => get_permalink(),
      'title' => get_the_title(),
  );
  $group = $post_data['group'];
  if( !in_array( $group, $group_order ) )
    $group_order[] = $group;
  if( !isset( $mypost_data[ $group ] ) )
    $mypost_data[ $group ] = array();

  $group_links[ $group ] = $post_data['group_link'];
  $mypost_data[ $group ][] = $post_data;
}

$html = '';
foreach( $group_order as $group ) {
  $cnt = count( $mypost_data[ $group ] );
  $link = $group_links[ $group ];
  $group_name = $group;
  if( $link )
    $group_name = "<a href='$link'>$group_name</a>";
  $html .= "<ul>$group_name ($cnt)";
  $html .= "<ul>";
  foreach( $mypost_data[ $group ] as $i => $post_data ) {
     $html .= "<li><span class='archive-title' style='width:85px;display:inline-block'>$post_data[date] :</span><a href='$post_data[permalink]'>$post_data[title]</a></li>";
  }
  $html .= "</ul>";
  $html .= "</ul>";
}
  $html .= "";
$archive_content = $html;
//$archive_content = "<ul style=''>$archive_content</ul>";
get_header(); ?>

  <div id="primary" class="site-content" style="margin-top:0">
		<div id="content" role="main">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php 
				    ob_start();
				    get_template_part( 'content', 'page' );
				    $page_content = ob_get_clean();
				    $page_content = str_replace("[archives]",$archive_content,$page_content);
				    echo $page_content;
				?>
				<?php comments_template( '', true ); ?>
			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>