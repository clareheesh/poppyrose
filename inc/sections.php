<?php

function get_sections()
{

    $sections = get_field('sections');

    if (have_rows('sections')) :
        while (have_rows('sections')) : the_row();

            $layout = get_row_layout();

            $result[] = call_user_func('get_' . $layout);

        endwhile;
    endif;
}

function get_text()
{

    $heading = get_sub_field('heading');
    $content = get_sub_field('content');

    ?>

    <div class="section section-text">
        <?= $heading ? '<h2>' . $heading . '</h2>' : ''; ?>

        <?= $content ? wpautop($content) : ''; ?>
    </div>

    <?php

//    return array(
//        'heading' => $heading,
//        'content' => $content,
//    );
}

function get_gallery()
{

    $gallery = get_sub_field('gallery');

//    return array(
//        'gallery' => $gallery,
//    );
}

function get_front_page_banner()
{

    $image_layout = get_sub_field('image_layout');
    $image = get_sub_field('image');
    $image_label = get_sub_field('image_label');
    $text = get_sub_field('text');
    $button = get_sub_field('button');
    $button_link = get_sub_field('button_link');

//    return array(
//        'image_layout' => $image_layout,
//        'image' => $image,
//        'image_label' => $image_label,
//        'text' => $text,
//        'button' => $button,
//        'button_link' => $button_link
//    );
}





/****** Save repeater fields in an order submission ***/
