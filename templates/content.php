<?php if( ! have_posts() ) : ?>
    <!-- There are no posts -->
    <h4>Sorry, there are no posts to display. </h4>
<?php endif; ?>

<?php while( have_posts() ) : the_post(); ?>

    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        
        <h1 class="entry-title post-title"><?php the_title(); ?></h1>

        <div class="post-thumbnail"><?php the_post_thumbnail(); ?></div>
        
        <!-- display excerpts if the page is an archive or search, otherwise display the content -->
        <?php get_sections(); ?>
        <?php the_content(); ?>

    </div>

<?php endwhile; ?>