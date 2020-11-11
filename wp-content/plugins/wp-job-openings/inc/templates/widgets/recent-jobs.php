<?php
/**
 * Template part for recent jobs widget
 *
 * Override this by copying it to currenttheme/wp-job-openings/widgets/recent-jobs.php
 *
 * @package wp-job-openings
 * @since 1.4
 * @version 1.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$awsm_filters  = get_option( 'awsm_jobs_filter' );
$listing_specs = get_option( 'awsm_jobs_listing_specs' );
?>

<div class="awsm-job-wrap">
	<div class="awsm-job-listings awsm-lists">
		<?php
		/**
		 * before_awsm_recent_jobs_widget_loop hook
		 *
		 * Fires before The Loop for recent jobs widget
		 *
		 * @since 1.4
		 */
		do_action( 'before_awsm_recent_jobs_widget_loop', $args, $instance );

		while ( $query->have_posts() ) :
			$query->the_post();
			$job_details = get_awsm_job_details();
			?>
			<div class="awsm-list-item" id="awsm-list-item-<?php echo esc_attr( $job_details['id'] ); ?>">
				<div class="awsm-job-item">
					<div class="awsm-list-left-col">
						<?php
							/**
							 * before_awsm_recent_jobs_widget_left_col_content hook
							 *
							 * @since 1.4
							 */
							do_action( 'before_awsm_recent_jobs_widget_left_col_content', $args, $instance );
						?>

						<h2 class="awsm-job-post-title">
							<?php
								$job_title = sprintf( '<span>%1$s</span>', esc_html( $job_details['title'] ));
								echo apply_filters( 'awsm_jobs_listing_title', $job_title, 'list' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
						</h2>

						<?php
							/**
							 * after_awsm_recent_jobs_widget_left_col_content hook
							 *
							 * @since 1.4
							 */
							do_action( 'after_awsm_recent_jobs_widget_left_col_content', $args, $instance );
						?>
					</div>

					<div class="awsm-list-right-col">
						<?php
							/**
							 * before_awsm_recent_jobs_widget_right_col_content hook
							 *
							 * @since 1.4
							 */
							do_action( 'before_awsm_recent_jobs_widget_right_col_content', $args, $instance );

						//if ( $show_spec ) {
							//awsm_job_listing_spec_content( $job_details['id'], $awsm_filters, $listing_specs );
						//}
						
						awsm_job_listing_spec_content_1( $job_details['posted_date']);

						if ( $show_more ) {
							awsm_job_more_details( $job_details['permalink'], 'list' );
						}

							/**
							 * after_awsm_recent_jobs_widget_right_col_content hook
							 *
							 * @since 1.4
							 */
							do_action( 'after_awsm_recent_jobs_widget_right_col_content', $args, $instance );
						?>
					</div>
				</div>
			</div>
			<?php
		endwhile;

		wp_reset_postdata();

		/**
		 * after_awsm_recent_jobs_widget_loop hook
		 *
		 * Fires after The Loop for recent jobs widget
		 *
		 * @since 1.4
		 */
		do_action( 'after_awsm_recent_jobs_widget_loop', $args, $instance );
		?>
	</div>
</div>
