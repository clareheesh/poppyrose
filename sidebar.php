<div id="sidebar">

    <?php if( get_field( 'sidebar_content' ) ) : ?>
        <?php the_field( 'sidebar_content' ); ?>
    <?php else : ?>
        <?php if( !function_exists( 'dynamic_sidebar' ) || !dynamic_sidebar( 'sidebar' ) ) : ?>
        <?php endif; ?>
    <?php endif; ?>
    
</div><!-- ./sidebar -->