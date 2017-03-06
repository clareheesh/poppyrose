<?php get_header(); ?>

	<!----------------------------------------------------------------------------------------------------------------------
	Main Content
	----------------------------------------------------------------------------------------------------------------------->
	<div class="parent container">

		<?php $sidebar = get_field('page_sidebar'); ?>
		<?php $direction = $sidebar ? get_field('left_right') : false; ?>

		<?php if ($direction == 'left') : ?>
			<div class="sidebar-content col-sm-3 col-xs-12">
				<?php get_sidebar(); ?>
			</div>
		<?php endif; ?>

		<div class="entry-content <?= $sidebar ? 'col-sm-9' : ''; ?> col-xs-12">

			<?php while (have_posts()) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<div class="post-thumbnail"><?php the_post_thumbnail(); ?></div>

					<!-- display excerpts if the page is an archive or search, otherwise display the content -->
					<?php the_content(); ?>
					<?php get_sections(); ?>

				</div>

			<?php endwhile; ?>

		</div>

		<?php if ($direction == 'right') : ?>
			<div class="sidebar-content col-sm-3 col-xs-12">
				<?php get_sidebar(); ?>
			</div>
		<?php endif; ?>

	</div><!-- ./container -->
<?php get_footer();