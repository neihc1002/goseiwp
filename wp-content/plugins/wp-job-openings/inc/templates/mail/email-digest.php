<?php
/**
 * Mail template for daily email digest.
 *
 * Override this by copying it to currenttheme/wp-job-openings/mail/email-digest.php
 *
 * @package wp-job-openings
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require AWSM_Job_Openings::get_template_path( 'header.php', 'mail' );

?>

<table style="width: 100%;">
	<tr>
		<td class="main-content-in-1">
			<div style="padding: 0 10px; text-align: center; max-width: 576px; margin: 0 auto;">
				<h2><?php esc_html_e( 'Here’s a quick overview of your job listings', 'wp-job-openings' ); ?></h2>
				<p>
					<?php
						/* translators: %s: Site title */
						printf( esc_html__( 'A snapshot of how your job listings in %s performed', 'wp-job-openings' ), '{site-title}' );
					?>
				</p>
			</div>
		</td>
	</tr>
	<tr>
		<td class="main-content-in-2"<?php echo empty( $applications ) ? ' style="border-bottom: 0;"' : ''; ?>>
			<div style="padding: 0 15px; text-align: center; max-width: 512px; margin: 0 auto;">
				<?php
					$overview_data = AWSM_Job_Openings::get_overview_data();
				?>
				<ul>
					<li>
						<span><?php echo esc_html( $overview_data['active_jobs'] ); ?></span>
						<?php esc_html_e( 'Active Jobs', 'wp-job-openings' ); ?>
					</li>
					<li>
						<span><?php echo esc_html( $overview_data['new_applications'] ); ?></span>
						<?php esc_html_e( 'New Applications', 'wp-job-openings' ); ?>
					</li>
					<li>
						<span><?php echo esc_html( $overview_data['total_applications'] ); ?></span>
						<?php esc_html_e( 'Total Applications', 'wp-job-openings' ); ?>
					</li>
				</ul>
			</div>
		</td>
	</tr>
	<?php
	if ( ! empty( $applications ) ) :
		?>
		<tr>
			<td class="main-content-in-3">
				<div style="text-align: center; max-width: 576px; margin: 0 auto;">
					<h3><?php esc_html_e( 'Recent Applications', 'wp-job-openings' ); ?></h3>
					<table class="job-table" style="font-size: 14px; width: 100%;">
						<thead>
							<tr style="background-color: #F3F5F8; color: #1F3130;">
								<th style="width:25%;"><?php esc_html_e( 'Name', 'wp-job-openings' ); ?></th>
								<th style="width:35%;"><?php esc_html_e( 'Job', 'wp-job-openings' ); ?></th>
								<th style="width:25%;"><?php esc_html_e( 'Applied on', 'wp-job-openings' ); ?></th>
								<th style="width:15%;"></th>
							</tr>
						</thead>
						<tbody>
						<?php
						foreach ( $applications as $application ) :
							?>
									<tr style="border-bottom: 1px solid #D7DFDF;">
										<td><?php echo esc_html( $application->post_title ); ?></td>
										<td><?php echo esc_html( get_the_title( $application->post_parent ) ); ?></td>
										<td><?php echo esc_html( date_i18n( 'j F Y', strtotime( get_the_date( '', $application->ID ) ) ) ); ?></td>
										<td><a href="<?php echo esc_url( AWSM_Job_Openings::get_application_edit_link( $application->ID ) ); ?>"><strong><?php esc_html_e( 'View', 'wp-job-openings' ); ?></strong></a></td>
									</tr>
								<?php
							endforeach;
						?>
						</tbody>
					</table>
					<p style="margin-top: 40px;"><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_application' ) ); ?>" class="btn btn-primary"><?php esc_html_e( 'View All Applications', 'wp-job-openings' ); ?></a></p>
				</div>
			</td>
		</tr>
		<?php
		endif;
	?>
</table>

<?php
require AWSM_Job_Openings::get_template_path( 'footer.php', 'mail' );
