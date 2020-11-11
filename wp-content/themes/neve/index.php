<?php
/**
 * Index template.
 *
 * @package Neve
 */
$container_class = apply_filters( 'neve_container_class_filter', 'container', 'blog-archive' );

get_header();

?>
	<div class="<?php echo esc_attr( $container_class ); ?> archive-container">
	<?php
	if(is_home()){
		echo do_shortcode('[smartslider3 slider="2"]');
	}
?>
		<div class="row">
			<?php do_action( 'neve_do_sidebar', 'blog-archive', 'left' ); ?>
			<div class="nv-index-posts blog col">
				<?php
				echo '
				<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
				 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
				 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
				 <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
				 <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">';
				do_action( 'neve_before_loop' );
				do_action( 'neve_page_header', 'index' );
				do_action( 'neve_before_posts_loop' );
				
				if ( have_posts() ) {
					/* Start the Loop. */
					echo '<div class="posts-wrapper row">';
					echo '<div id="tabs-1">';
					  $categories = $categories = get_categories( array(
						'orderby' => 'name',
						'order'   => 'ASC',
						'hide_empty' => 0
					) );
					$objectAll = new stdClass();
					$objectAll->cat_name = 'All';
					$objectAll->cat_ID = 0;
					$objectAll->category_count = wp_count_posts()->publish;
					$all = new WP_Term($objectAll);
					$args = json_decode( stripslashes( json_encode( $wp_query->query_vars ) ), true );
					array_unshift($categories,$all);
					usort($categories, function ($a, $b) {return $a->cat_ID > $b->cat_ID;});
					  echo '<ul class="nav nav-tabs" role="tablist">';
					  $index1 = 0;
					  foreach($categories as $category) { 
						$next = '';
						if($index1 == 0){
							$next = 'active';
						}
						
						$args['cat'] = $category->cat_ID;
						$query = new WP_Query($args);
						$count = $query->found_posts;
						$name = '<li class="nav-item">
						<a class="nav-link '.$next.'" data-toggle="tab" href="#'. $category->cat_ID .'">'. $category->cat_name .' (' . $count.')</a>
					  </li>';
					  echo $name;
					  $index1 .=1;
					} 
					  echo '</ul>';
					echo '<div class="tab-content">';
					$index = 0;
					foreach($categories as $category) {
						$next = 'active';
						if($index != 0){
							$next = '';
						}
					
					echo '<div id="'. $category->cat_ID . '" data-page = "1" class="container tab-pane ' . $next .'">';
					$args['cat'] = $category->cat_ID;
					query_posts($args);

					while ( have_posts() ) {
						the_post();
						get_template_part( 'template-parts/content', get_post_type() );

						if ( $pagination_type !== 'infinite' ) {
							if ( $post_index === $hook_after_post && $hook_after_post !== - 1 ) {
								do_action( 'neve_middle_posts_loop' );
							}
							$post_index ++;
						}
					}
					misha_paginator( get_pagenum_link() );
					echo '</div>';
					$index .=1;
				  } 
					echo '</div>';
					echo '</div>';
					if ( ! is_singular() ) {
						do_action( 'neve_do_pagination', 'blog-archive' );
					}
					echo '</div>';
					echo '<script>
					$( function() {
					  $( "#tabs-1" ).tabs();
					} );
					</script>';	
				} else {
					get_template_part( 'template-parts/content', 'none' );
				}
				?>
				<div class="w-100"></div>
				<?php do_action( 'neve_after_posts_loop' ); ?>
			</div>
			<?php do_action( 'neve_do_sidebar', 'blog-archive', 'right' ); ?>
		</div>
	</div>
	
	<?php  
 echo '<script>
 $(".page-numbers").remove();
 </script>';
?>
<?php
get_footer();
