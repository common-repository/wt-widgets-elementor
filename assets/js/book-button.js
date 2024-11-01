jQuery(function($) {
    $("#wp-travel-one-page-checkout-enables").addClass('wp-block-button');
    $("#wp-travel-one-page-checkout-enables button").addClass('wp-block-button__link wtwe-book-button');
    $('#wptravel-blocks-book-button.wtwe-book-trip').click(() => {
        $('#wp-travel-tab-wrapper #slider-tab .wp-travel.tab-list.resp-tabs-list li.booking.wp-travel-booking-form').click();
        const modal = $('.ReactModal__Body--open .ReactModalPortal');
        if( typeof modal != 'undefined' ) {
            modal.css('display', 'block');
        }
    });
  });

  