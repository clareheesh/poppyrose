<?php
/** Template Name: FAQ */

get_header(); ?>

<?php if (have_posts()): ?>

	<?php while (have_posts()) : the_post(); ?>

		<?php $questions = get_field('questions'); ?>
		<?php $qs = ''; ?>
		<?php $as = ''; ?>

		<?php if ($questions && (is_array($questions) || is_object($questions))) : ?>

			<?php foreach ($questions as $key => $q) :

				$qs .= '<p><a href="#_' . $key . '">' . $q['question'] . '</a></p>';
				$as .= '<span id="_'.$key.'" class="anchor"></span><div data-attr="_' . $key . '"><h4><strong>'. $q['question'].'</strong></h4>' . $q['answer'] . '<hr></div>';

			endforeach; ?>
		<?php endif; ?>

		<div class="container">
			<div class="col-xs-12 entry-content">
				<h1><?php the_title(); ?></h1>

				<div>
					<?php the_content(); ?>
				</div>

				<?php if ($qs) : ?>
					<div class="questions">
						<?= $qs; ?>
					</div>
				<?php endif; ?>

				<?php if ($as) : ?>
					<div class="answers">
						<?= $as; ?>
					</div>
				<?php endif ?>
			</div>
		</div>

	<?php endwhile; ?>
<?php endif; ?>

	<script>
		jQuery(function ($) {
			/** Smooth scrolling on anchor links */
			$('a[href*=#]').on('click', function (event) {
				event.preventDefault();
				$('html,body').animate({scrollTop: $(this.hash).offset().top}, 500);
			});
		});
	</script>
<?php
get_footer(); ?>