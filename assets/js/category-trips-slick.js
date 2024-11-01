// if( typeof categoryOptions != 'undefined' ){
//   jQuery(function($) {
//     if( typeof elementorFrontend != 'undefined') {
//       elementorFrontend.hooks.addAction('frontend/element_ready/category-trips.default', function($scope) {
//         var categoryTrips = $scope.find('.wtwe-category-trip-wrapper');
//           categoryTrips.slick({
//             arrows: categoryOptions.showArrows == "yes" ? true :false,
//             dots: false,
//             infinite: categoryOptions.infinite == "yes" ? true : false,
//             autoplay: categoryOptions.autoplay == "yes" ? true : false,
//             speed: 300,
//             delay: categoryOptions.delay,
//             slidesToShow: categoryOptions.tripsToShow,
//             slidesToScroll: 1,
//           });
//       });
//     }
//   });
// }


// console.log( "laksjdlkjaskld" );
// jQuery(function($) {
//   $('.wtwe-category-trip-wrapper').slick({
//     arrows: true,
//     dots: false,
//     infinite: true,
//     autoplay: true,
//     speed: 300,
//     delay: 500,
//     slidesToShow: 4,
//     slidesToScroll: 1,
//   });
// })

jQuery(function($) {
  if( typeof elementorFrontend != 'undefined') {
    elementorFrontend.hooks.addAction('frontend/element_ready/wp-travel-category-trips.default', function($scope) {
      var categoryTrips = $scope.find('.wtwe-category-trip-wrapper');
        categoryTrips.slick({
          autoplay: categoryOptions.autoplay == "yes" ? true : false,
          dots: categoryOptions.dots == "yes" ? true : false,
          infinite: categoryOptions.infinite == "yes" ? true : false,
          speed: 1000,
          delay: categoryOptions.delay,
          slidesToShow: categoryOptions.tripsToShow,
          slidesToScroll: 1,
          responsive: [
            {
                breakpoint:800,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
            // Add more breakpoints and settings as needed
        ]
        });
    });
  }
});