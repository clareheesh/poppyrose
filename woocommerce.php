<?php get_header(); ?>

<?php if(isset($_REQUEST['reset']) && $_REQUEST['reset']) {
	update_field('allow_orders_today', true);
	add_post_meta(get_the_ID(), 'cron_job', date('d/m/Y H:I a', strtotime('now')));
} ?>

<?php if(isset($_REQUEST['complete']) && $_REQUEST['complete']) {
	mark_orders_complete();
} ?>

	<!----------------------------------------------------------------------------------------------------------------------
	Main Content
	----------------------------------------------------------------------------------------------------------------------->
	<div class="parent container">

		<?php $sidebar = get_field( 'page_sidebar' ); ?>
		<?php $direction = $sidebar ? get_field( 'left_right' ) : false; ?>

		<?php if( $direction == 'left' ) : ?>
			<div class="sidebar-content col-sm-3 col-xs-12">
				<?php get_sidebar(); ?>
			</div>
		<?php endif; ?>

		<div class="entry-content <?= $sidebar ? 'col-sm-9' : ''; ?> col-xs-12">

			<?php woocommerce_content(); ?>

		</div>

		<?php if( $direction == 'right' ) : ?>
			<div class="sidebar-content col-sm-3 col-xs-12">
				<?php get_sidebar(); ?>
			</div>
		<?php endif; ?>

	</div><!-- ./container -->
<?php get_footer();