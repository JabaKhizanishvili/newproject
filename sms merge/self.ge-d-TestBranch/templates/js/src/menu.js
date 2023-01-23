/*Dropdown_Menu*/

function menumagic() {
  if ($(window).width() < 1200) {
    $("li.menu_item", '.main_menu').click(function () {

      if (!$(this).hasClass("menu_item_clicked")) {
        $(this).addClass("menu_item_clicked");
        $('ul', this).slideDown({duration: 200});
      } else {
        $(this).removeClass("menu_item_clicked");
        $(this).removeClass("menu_item_active");
        $('ul', this).slideUp({duration: 200});
      }

    });
  }
}

$(document).ready(function () {
  menumagic();
  $("li.menu_item").has("ul").addClass("havechild");

  $('.info_logout').click(function () {

    if (!$('.info_logout ul').hasClass("activated")) {
      $('.info_logout ul').addClass('activated');
    } else {
      $(".info_logout ul").removeClass('activated');
    }

  });

});

window.addEventListener('resize load', menumagic, false);


/*Dropdown_Menu*/