jQuery(function ($) {


    /**
     * Responsive Menu
     */
    $('.responsive-menu').click(function () {
        // reveal the mobile menu / hide the mobile menu
        var menu = $('#poppy-menu');
        menu.slideToggle();

        if ($('#secondary-menu').hasClass('fixed')) {
            $('#secondary-menu').removeClass('fixed');
            $('body').removeAttr('style');
        } else {
        }
    });

    $('.menu-item-has-children').click(function () {
        $(this).toggleClass('open');

        if ($(this).children('ul,p').is(':hidden') == true) {
            $(this).children('ul,p').show();
            //$(this).children('ul,p').slideDown('slow');
            return false
        }
    });

    $(document).on("scroll", function (e) {

        if( $(window).width() < 460 ) { 
            if ($(this).scrollTop() > 180 && !$('#poppy-menu').is(":visible")) {
                $('#secondary-menu').addClass('fixed');
                // $('body').attr('style', 'padding-top: ' + $('#secondary-menu').height() + 'px');
            } else {
                $('#secondary-menu').removeClass('fixed');
                // $('body').removeAttr('style');
            }
        } else {
            if ($(this).scrollTop() > 158 && ( $(window).width() > 1200 || ($(window).width() <= 1200 && !$('#poppy-menu').is(":visible")))) {
                $('#secondary-menu').addClass('fixed');
                $('body').attr('style', 'padding-top: ' + $('#secondary-menu').height() + 'px');
            } else {
                $('#secondary-menu').removeClass('fixed');
                $('body').removeAttr('style');
            }
        }
    });

    $(window).resize(function () {
        var menu = $('#poppy-menu');

        if ($(this).width() > 1200) {
            menu.show();
            menu.css('overflow', 'visible');
        } else {
            // menu.hide();
        }
    });


    /**
     *  Responsive Embedded Youtube/Vimeo Videos
     *  https://css-tricks.com/NetMag/FluidWidthVideo/Article-FluidWidthVideo.php
     */
    // Find all YouTube videos
    var $allVideos = $("iframe[src^='//player.vimeo.com'], iframe[src^='//www.youtube.com']"),

    // The element that is fluid width
        $fluidEl = $("body");

    // Figure out and save aspect ratio for each video
    $allVideos.each(function () {

        $(this)
            .data('aspectRatio', this.height / this.width)

            // and remove the hard coded width/height
            .removeAttr('height')
            .removeAttr('width');

    });

    // When the window is resized
    $(window).resize(function () {

        var newWidth = $fluidEl.width();

        // Resize all videos according to their own aspect ratio
        $allVideos.each(function () {

            var $el = $(this);
            $el
                .width(newWidth)
                .height(newWidth * $el.data('aspectRatio'));

        });

        // Kick off one resize to fix all videos on page load
    }).resize();


    /**
     * Equal height
     */
    $('.equal-height').matchHeight();
    $('.match').matchHeight();

    /** Adjust the height and width of the label */
    if ($('.label.box').length) {
        $('.label.box').height($('.label.box .label-text').height() + 40);
        $('.label.box').width($('.label.box .label-text').width() + 40);
    }

});



