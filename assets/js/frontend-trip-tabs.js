jQuery(function(t){var a,s;console.log("WORINING"),window.location.hash&&(t("ul.resp-tabs-list > li#"+(a=window.location.hash.substring(1))).hasClass("wp-travel-ert")&&((lis=t("ul.resp-tabs-list > li")).removeClass("resp-tab-active"),t("ul.resp-tabs-list > li#"+a).addClass("resp-tab-active"),(tab_cont=t(".tab-list-content")).removeClass("resp-tab-content-active").hide(),t("#"+a+".tab-list-content, #wp-travel-tab-content-"+a+".tab-list-content").addClass("resp-tab-content-active").show()),t(".wp-travel-tab-wrapper").length)&&(a=t(window).width(),s=t(".wp-travel-tab-wrapper").offset().top,a<767&&(s=t(".resp-accordion.resp-tab-active").offset().top),t("html, body").animate({scrollTop:s},1200))});