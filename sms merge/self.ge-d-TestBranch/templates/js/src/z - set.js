$(document).ready(function () {
  if (GlobalGeoKBD)
  {
    $('.kbd').geokbd();
  }
  var $HeaderWidth = 0;
  var $HeaderAWidth = 0;

  $('.tk_header').children('.tk_head').each(function () {
    $HeaderWidth += $(this).width();
  });
  $('.tk_header_a').children('.tk_head').each(function () {
    $HeaderAWidth += $(this).width();
  });
  $('.tk_scroll_in').width($HeaderWidth);
  $('.tk_scroll_in_a').width($HeaderAWidth);
  $('.tk_scroll').disableTextSelect();
  $('.tk_scroll_a').disableTextSelect();
  $("a.photo_zoom").prettyPhoto();
  $("a.modaliframe").prettyPhoto();

  $.reject({
    reject: {
//      safari: true, // Apple Safari  
//      chrome: true, // Google Chrome  
      msie: true, // Microsoft Internet Explorer  
      opera: true, // Opera  
      konqueror: true, // Konqueror (Linux)   G
      unknown: true // Everything else  
//      all: true
    },
    header: GlobalAlertBrowser,
    paragraph1: GlobalAlertBrowserWork,
    paragraph2: '',
    // Allow closing of window  
    close: true,
    // Message displayed below closing link  
    closeMessage: '',
    closeLink: GlobalAlertClose,
    display: ['firefox', 'chrome'],
    beforeReject: function () {
      this.closeCookie = true;
    }
  });
  $(".search-select").chosen({search_contains: true});
//  $('input', '.chosen-search').geokbd();
  $('input', '.chosen-container').geokbd();

  $('.FloatingScrollbar').floatingScroll();
  $('.fixed-table').floatingScroll();
  $('.double-scroll').doubleScroll();
  $("#SelfSliderIndicators").swipe({
    swipe: function (event, direction, distance, duration, fingerCount, fingerData) {
      if (direction == 'left') {
        $(this).carousel('next');
      }
      if (direction == 'right')
      {
        $(this).carousel('prev');
      }
    },
    allowPageScroll: "vertical"

  });
});
