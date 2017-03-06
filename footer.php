<footer>

	<div class="container">
		<div class="col-sm-9 col-xs-12">
			<?= str_replace( '[date]', date( 'Y' ), get_field( 'footer_left', 'option' ) ); ?>
		</div>

		<div class="col-sm-3 col-xs-12">
			<!--            --><? //= str_replace( '[date]', date( 'Y' ), get_field( 'footer_right', 'option' ) ); ?>
			<?= do_shortcode( '[formidable id=12 title=false description=true]' ); ?>
		</div>
	</div>

</footer>

<?php wp_footer(); ?>
<script type="text/javascript">
	jQuery(function ($) {

		$('i.fa-shopping-cart').after(' <span> ( <?= WC()->cart->get_cart_contents_count(); ?> ) <?= WC()->cart->get_cart_contents_count() > 0 ? WC()->cart->get_cart_total() : ''; ?></span>');
	});
</script>

</body>

</html>