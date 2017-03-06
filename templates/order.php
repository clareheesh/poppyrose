<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>

		<?php $id = get_the_ID(); ?>

		<!-- Order Title is the Order ID -->
		<h1><?php get_the_title(); ?></h1>

		<!-- We want:
		- the number of bunches,
		- details about those bunches (within an accordion?)
		- the buyer details
		- the payment details
		- the order status
		- any notes about the order
		- date of the order
		-->

		<p><strong>Order Status</strong></p>
		<p><?= get_post_meta($id, 'order_status', true); ?></p>

		<p><strong>Flowers Information</strong></p>

		<a href="#unformatted_data" data-toggle="collapse" role="button" class="btn btn-primary">Show Original Unformatted Data</a>
		<div id="unformatted_data" class="collapse">
			<div class="well">
				<?php the_content(); ?>
			</div>
		</div>

	<?php endwhile; ?>
<?php endif; ?>
