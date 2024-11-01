jQuery(function($) {
    // Delete that line if you don't want the first Div to be displayed by default
    // $("div:first").css("display", "block");

    $(".wtwe-faq-accordion-label").click(function () {
      $(this).next().slideToggle(200);
      $(".wtwe-faq-accordion-answer").not($(this).next()).slideUp(200);
      
      // how to rotate the icon JUST h3>i
    // $("i").css({'transform':'rotate(180deg)'});
      
    });
    
  });