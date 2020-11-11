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
		<div class="row">
			<?php do_action( 'neve_do_sidebar', 'blog-archive', 'left' ); ?>
			<div class="nv-index-posts blog col">
				<?php
				echo '<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
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
					
				// 	echo '<div class="container">
				// 	<h2>Toggleable Tabs</h2>
				// 	<br>
				// 	<!-- Nav tabs -->
				// 	<ul class="nav nav-tabs" role="tablist">
				// 	  <li class="nav-item">
				// 		<a class="nav-link active" data-toggle="tab" href="#home">Home</a>
				// 	  </li>
				// 	  <li class="nav-item">
				// 		<a class="nav-link" data-toggle="tab" href="#menu1">Menu 1</a>
				// 	  </li>
				// 	  <li class="nav-item">
				// 		<a class="nav-link" data-toggle="tab" href="#menu2">Menu 2</a>
				// 	  </li>
				// 	</ul>
				
				// 	<!-- Tab panes -->
				// 	<div class="tab-content">
				// 	  <div id="home" class="container tab-pane active"><br>
				// 		<h3>HOME</h3>
				// 		<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
				// 	  </div>
				// 	  <div id="menu1" class="container tab-pane fade"><br>
				// 		<h3>Menu 1</h3>
				// 		<p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
				// 	  </div>
				// 	  <div id="menu2" class="container tab-pane fade"><br>
				// 		<h3>Menu 2</h3>
				// 		<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.</p>
				// 	  </div>
				// 	</div>
				//   </div>';
					  $categories = $categories = get_categories( array(
						'orderby' => 'name',
						'order'   => 'ASC'
					) );
					  echo '<ul class="nav nav-tabs" role="tablist">';
					  $index1 = 0;
					  foreach($categories as $category) { 
						$next = '';
						if($index1 == 0){
							$next = 'active';
						}
						$name = '<li class="nav-item">
						<a class="nav-link '.$next.'" data-toggle="tab" href="#'. $category->cat_ID .'">'. $category->cat_name . '</a>
					  </li>';
					  echo $name;
					  $index1 .=1;
						//echo $category->cat_name . ' '; 
					} 
					  echo '</ul>';
					$pagination_type = get_theme_mod( 'neve_pagination_type', 'number' );
					if ( $pagination_type !== 'infinite' ) {
						global $wp_query;

						$posts_on_current_page = $wp_query->post_count;
						$hook_after_post       = -1;

						if ( $posts_on_current_page >= 2 ) {
							$hook_after_post = intval( ceil( $posts_on_current_page / 2 ) );
						}
						$post_index = 1;
					}
					echo '<div class="tab-content">';
					$index = 0;
					foreach($categories as $category) {
						$next = 'active';
						if($index != 0){
							$next = '';
						}
					
					echo '<div id="'. $category->cat_ID . '" class="container tab-pane ' . $next .'">';
					query_posts('cat=' . $category->cat_ID . '&showposts=5');
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
					echo '</div>';
					$index .=1;
					  //echo $category->cat_name . ' '; 
				  } 
					echo '</div>';
					// query_posts('cat=5&showposts=5');
					// while ( have_posts() ) {
					// 	the_post();
					// 	get_template_part( 'template-parts/content', get_post_type() );

					// 	if ( $pagination_type !== 'infinite' ) {
					// 		if ( $post_index === $hook_after_post && $hook_after_post !== - 1 ) {
					// 			do_action( 'neve_middle_posts_loop' );
					// 		}
					// 		$post_index ++;
					// 	}
					// }
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
get_footer();
