<?php get_header(); ?>

<div class="parent container">

    <div class="entry-content col-sm-9 col-xs-12">

    <h1>Archive</h1>

        <?php get_template_part('templates/content'); ?>
        <?php get_template_part('templates/content', 'comments'); ?>

    </div>

    <div class="sidebar-content col-sm-3 col-xs-12">
        <?php get_sidebar(); ?>
    </div>

</div><!-- ./content -->
<?php get_footer();