<?php if( ! have_posts() ) : ?>
    <!-- There are no posts -->
    <h4>Sorry, there are no posts to display. </h4>
<?php endif; ?>

<?php while( have_posts() ) : the_post(); ?>
    
    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        
        <?php the_archive_title('<h1 class="entry-title post-title">', '</h1>'); ?>
        
        <div class="post-thumbnail"><?php the_post_thumbnail(); ?></div>
        
        <!-- display excerpts if the page is an archive or search, otherwise display the content -->
        <?php if ( is_archive() || is_search() ) : ?>
            <?php the_excerpt(); ?>
        <?php else: ?>
            <?php the_content('Read More'); ?>
        <?php endif; ?>
        
        <div class="meta">
            <p><small><?php the_tags(); ?></small></p>
            <?php wp_link_pages('before=<div id="page-links">&after=</div>'); ?>
        
        </div>
        
        <?php get_template_part('content', 'comments'); ?>
    
    </div>

<?php endwhile; ?>

<?php the_posts_pagination(); ?>