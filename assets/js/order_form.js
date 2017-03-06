jQuery(document).ready(function ($) {

    // Try triggering the click on load to prevent the incorrect total load?

    // Change the variation dropdown box depending on the option selected
    $('.variation-box').click( function() {
        var val = $(this).attr('data-target-val');
        var in_stock = parseInt($(this).attr('data-available'));
        $('.selected').removeClass('selected');
        $(this).addClass('selected');
        $('select option[value="' + val + '"]').prop('selected', 'selected').change();

        if(!in_stock) {
            $('.frm_form_fields').hide();
            $('.unavailable_message').fadeIn();
        } else {
            $('.frm_form_fields').fadeIn();
            $('.unavailable_message').hide();
        }

        return false;
    })
});