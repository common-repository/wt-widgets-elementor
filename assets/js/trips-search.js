jQuery(function($) {
    if( typeof elementorFrontend != 'undefined') {
        elementorFrontend.hooks.addAction('frontend/element_ready/wp-travel-trip-search-form.default', function() {
            $(document).on('click touch', '.selectDropdown ul li a', function(e) {
                e.preventDefault();
                var dropdown = $(this).parent().parent().parent();
                var active = $(this).parent().hasClass('active');
                var label = active ? dropdown.find('select').attr('placeholder') : $(this).text();

                dropdown.find('option').prop('selected', false);
                dropdown.find('ul li').removeClass('active');

                dropdown.toggleClass('filled', !active);
                dropdown.children('.wtwe-select-wrapper').children('span').text(label);

                if(!active) {
                    dropdown.find('option:contains(' + $(this).text() + ')').prop('selected', true);
                    $(this).parent().addClass('active');
                }

                dropdown.removeClass('open');
            });

            $('.wtwe-select-dropdown > .wtwe-select-wrapper').on('click touch', function(e) {
                var self = $(this).parent();
                $('.wtwe-select-dropdown').removeClass('open');
                self.toggleClass('open');
            });

            $(document).on('click touch', function(e) {
                var dropdown = $('.wtwe-select-dropdown');
                if(dropdown !== e.target && !dropdown.has(e.target).length) {
                    dropdown.removeClass('open');
                }
            });
        });
    }

    $('.wtwe-trips-search-btn').on('click', function() {
        var pathname = $(this).siblings('.wtwe-itinerary-archive-page-url').val();
        if (!pathname) {
            pathname = window.location.pathname;
        }
        query_string = '';
        if ( window.location.search ) {
            query_string = window.location.search;
        }
        var full_url = new URL( pathname );
        var search_params  = full_url.searchParams;

        var data_index = $(this).siblings('.wtwe-filter-data-index').data('index');
        $('.wtwe-search-widget-filters-input' + data_index).each(function() {
            var filterby = $(this).attr('name');
            var filterby_val = $(this).val();
            search_params.set( filterby, filterby_val );
            full_url.search = search_params.toString();
        })
        var new_url = full_url.toString();
        window.location = new_url;
    });
})
