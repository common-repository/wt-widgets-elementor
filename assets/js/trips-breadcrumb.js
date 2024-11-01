jQuery(function($) {
    if (typeof elementorFrontend !== 'undefined') {
        elementorFrontend.hooks.addAction('frontend/element_ready/wp-travel-breadcrumb.default', function($scope, $) {
            // Function to dynamically add the wtwe-trips-breadcrumbs-seperator
            function addBreadcrumbSeparator() {
                var breadcrumbWrapper = $scope.find('.wtwe-breadcrumb-wrapper');

                // Check if breadcrumb wrapper exists
                if (breadcrumbWrapper.length) {
                    var separator = breadcrumbWrapper.data('separator');

                    // Validate the separator value
                    if (typeof separator !== 'string' || separator.trim() === '') {
                        console.error('Invalid separator value:', separator);
                        return;
                    }

                    // Find all breadcrumb items except the last one
                    var breadcrumbItems = breadcrumbWrapper.find('.trail-items .trail-item:not(.trail-end)');

                    // Add separator to each breadcrumb item
                    breadcrumbItems.each(function() {
                        var $item = $(this);
                        // Check if the separator span already exists to avoid duplication
                        if (!$item.find('.separator').length) {
                            var separatorSpan = $('<span>', {
                                class: 'separator',
                                text: separator
                            });

                            // Append the separator span to the breadcrumb item
                            $item.append(separatorSpan);
                        }
                    });
                } else {
                    console.error('Breadcrumb wrapper not found.');
                }
            }
            addBreadcrumbSeparator();
        });
    } else {
        console.error('Elementor frontend object is not defined.');
    }
    
});



// jQuery(function($) {
//     if (typeof elementorFrontend != 'undefined') {
//         elementorFrontend.hooks.addAction('frontend/element_ready/wp-travel-breadcrumb.default', function() {
//             // JavaScript code to add the separator dynamically
//             var breadcrumbWrapper = document.querySelector('.wtwe-breadcrumb-wrapper');
//             if (breadcrumbWrapper) {
//                 var separator = breadcrumbWrapper.getAttribute('data-separator');
//                 var breadcrumbItems = breadcrumbWrapper.querySelectorAll('.trail-items .trail-item:not(.trail-end)');
//                 breadcrumbItems.forEach(function(item) {
//                     var separatorSpan = document.createElement('span');
//                     separatorSpan.className = 'separator';
//                     separatorSpan.textContent = separator;
//                     item.appendChild(separatorSpan);
//                 });
//             }
//         });
//     }
// });
