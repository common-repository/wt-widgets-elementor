jQuery(window).on('elementor/frontend/init', function() {
  elementorFrontend.hooks.addAction('frontend/element_ready/wp-travel-hero-slider.default', function($scope) {
        var autoplaySwitch = $scope.find('#elementor-control-default-c946');

        autoplaySwitch.on('change', function() {
            var autoplayEnabled = jQuery(this).prop('checked');
            // Get the Slick Slider element within the widget
            var sliderElement = $scope.find('.wtwe-hero-slider-wrapper');

            // Update Slick Slider's autoplay option
            if (autoplayEnabled) {
                sliderElement.slick('slickPlay');
            } else {
                sliderElement.slick('slickPause');
            }
        });
    });
});
