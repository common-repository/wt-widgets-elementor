// jQuery(document).ready(function() {
//   jQuery('.wtwe-hero-slider-wrapper').slick({
//       dots: false,
//       infinite: true,
//       autoplay: false,
//       speed: 300,
//       slidesToShow: 1,
//       slidesToScroll: 1,
//     });
//   });

jQuery(document).ready(function($) {
    if( typeof elementorFrontend != 'undefined') {
      elementorFrontend.hooks.addAction('frontend/element_ready/wp-travel-hero-slider.default', function($scope) {
        var heroSlider = $scope.find('.wtwe-hero-slider-wrapper.design-1');
          // Your custom script here
          heroSlider.slick({
            dots: false,
            infinite: true,
            fade: true,
            cssEase: 'linear',
            autoplay: false,
            speed: 300,
            slidesToShow: 1,
            slidesToScroll: 1,
          });
      });
    }

    // SECOND DESIGN
    if (typeof elementorFrontend !== 'undefined') {
      elementorFrontend.hooks.addAction('frontend/element_ready/wp-travel-hero-slider.default', function ($scope) {
          var heroSliderOne = $scope.find('.wtwe-hero-slider-wrapper.design-2');

          heroSliderOne.slick({
              dots: false,
              infinite: true,
              fade: true,
              cssEase: 'linear',
              autoplay: false,
              speed: 300,
              slidesToShow: 1,
              slidesToScroll: 1
          });
      });
  }
  
});