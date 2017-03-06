
<?php get_header(); ?>
<div class="parent container">

    <?php $sidebar = get_field( 'page_sidebar' ); ?>
    <?php $direction = $sidebar ? get_field( 'left_right' ) : false; ?>

    <?php if( $direction == 'left' ) : ?>
        <div class="sidebar-content col-sm-3 col-xs-12">
            <?php get_sidebar(); ?>
        </div>
    <?php endif; ?>

    <div class="entry-content <?= $sidebar ? 'col-sm-9' : ''; ?> col-xs-12">

        <?php get_template_part( 'templates/content' ); ?>

    </div>

    <?php if( $direction == 'right' ) : ?>
        <div class="sidebar-content col-sm-3 col-xs-12">
            <?php get_sidebar(); ?>
        </div>
    <?php endif; ?>

</div><!-- ./content -->
<?php get_footer();
