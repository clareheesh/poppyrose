<!DOCTYPE html>
<html <?php language_attributes(); ?> ng-app="mainApp">

<head>

	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>

	<title><?php !is_front_page() ? wp_title('&raquo;', true, 'right') : bloginfo('name'); ?></title>

	<link rel="profile" href="http://gmpg.org/xfn/11"/>
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>"/>

	<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico"/>

	<?php if (is_singular() && get_option('thread_comments'))
		wp_enqueue_script('comment-reply'); ?>
	<?php wp_head(); ?>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-62563793-1', 'auto');
  ga('send', 'pageview');

</script>
</head>

<body <?php body_class(is_front_page() ? 'animated fadeIn' : ''); ?>>
<header>

	<div class="header-background">
		<nav id="mainnav" role="navigation">
			<div class="container container-header">

				<?php $logo = get_field('logo', 'option'); ?>
				<?php $logo = wp_get_attachment_image_src($logo, 'medium'); ?>
				<?php $logo = $logo ? $logo[0] : false; ?>
				<?php if ($logo) : ?>
				<div class="logo-container">
					<a href="<?= site_urL(); ?>"><img class="animated fadeIn" src="<?= $logo; ?>" id="logo" alt="Poppy Rose Logo"/></a>
					<?php endif; ?>
				</div>

				<div class="responsive-menu"><a href="#"><i class="fa fa-bars fa-lg"></i> Menu</a></div>

				<?php wp_nav_menu(array(

					'theme_location' => 'primary',
					'menu_class' => 'navbar-header',
					'menu_id' => 'sf-navigation',
					'container' => 'div',
					'container_id' => 'sf-menu'
				)); ?>

			</div>
		</nav>
	</div>

	<div id="secondary-menu">

		<div class="container container-header">

			<div class="responsive-menu"><a href="#"><i class="fa fa-bars fa-lg"></i> Menu</a></div>

			<?php wp_nav_menu(array(
				'menu_class' => 'navbar-header',
				'menu_id' => 'poppy-navigation',
				'container' => 'div',
				'container_id' => 'poppy-menu'
			)); ?>

		</div>
	</div>

</header>