<?php if (function_exists( 'wp_list_comments' )) : ?>

    <?php if ( post_password_required() ) {
    	echo '<p class="nocomments">This post is password protected. Enter the password to view comments.</p>';
    	return;
    } ?>

    <?php if ( have_comments() ) : ?>

        <?php comment_form(); ?>
        
        <h4 id="comments"><?php comments_number('No Comments', 'One Comment', '% Comments' );?></h4>
        <ul class="commentlist">
        	<?php wp_list_comments(); ?></ul>
        <div class="navigation">
            <div class="alignleft"><?php previous_comments_link() ?></div>
            <div class="alignright"><?php next_comments_link() ?></div>
        </div>
        <?php else : // this is displayed if there are no comments so far ?>
        	<?php if ( comments_open() ) :
        		// If comments are open, but there are no comments.
        	else : // comments are closed
        	endif;

        paginate_comments_links($args);

    endif; ?>

<?php else : ?>

    <?php if ($comments) : ?>
        <?php $comment_count = get_comment_count($post->ID); echo $comment_count['approved']; ?> Comments
        <ul class="commentlist">
            <?php foreach( $comments as $comment ) :
                // stuff to display the comment in an LI here
            endforeach;
        ?></ul>
        <?php else :
        if ('open' == $post->comment_status) :
        	// If comments are open, but there are no comments.
        else :
        	// comments are closed
        endif;
    endif; ?>

<?php endif;