function side_menu($mode)
{
  if ($mode == 1)
  {
//    $('.l_header_block').addClass('SB_header');
    $('.close-menu').addClass('SB_burger');
    $('.menu-expanded').addClass('SB_page');
    $('.SB_main').removeClass('SB_main').addClass('SB_main_on');
    
    $('#menu-block').css({'width': '250px'});
    $('.menu_block_img').css('width', '260px');
    $('.menu_block_in').css({'width': '260px'});
    $('.main_menu_left .menu_item').css({'border-bottom': '1px solid rgb(67, 71, 120, 1)'});
    $('.main_menu_left .menu_item').last().css({'border-bottom': '1px solid rgb(67, 71, 120, 0)'});
    $('.menu_title').show();

    $.cookies.set('side_menu', 1, {path: '/', hoursToLive: 168});
  } else
  {
//    $('.l_header_block').removeClass('SB_header');
    $('.close-menu').removeClass('SB_burger');
    $('.menu-expanded').removeClass('SB_page');
    $('.SB_main_on').removeClass('SB_main_on').addClass('SB_main');
    
    $('#menu-block').css({'width': '40px'});
    $('.menu_block_in').css({'width': '50px'});
    $('#page-block').css({'width': '100%'});
    $('.main_menu_left .menu_item').css({'border-bottom': '1px solid rgb(67, 71, 120, 0)'});
    $('.menu_block_img').css('width', '50px');
    $('.menu_title').hide();
    $.cookies.set('side_menu', 0, {path: '/', hoursToLive: 168});
  }
}
