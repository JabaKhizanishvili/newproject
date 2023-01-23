var filtered = 0;
//function ToolBarFilter(className)
//{
//  var TB = $('.key_' + className);
//  if ($(TB).hasClass('toolbat_item_active'))
//  {
//    $('.key_' + className).removeClass('toolbat_item_active');
//    $('.live_' + className).css('display', 'none');
//    $.cookies.del('toolbarItem_' + className);
//    filtered--;
//    if (filtered === 0)
//    {
//      $('.item_groups').css('display', 'inline-block');
//    }
//  } else
//  {
//    $('.key_' + className).addClass('toolbat_item_active');
//    if (filtered === 0)
//    {
////      $('.item_groups').css('display', 'none');
//      $('.live_' + className + ' .bi-chevron-down').removeClass('bi-chevron-down').addClass('bi-chevron-right');
//      $('.live_' + className + ' .board-block').css('overflow-y', 'unset');
//      $('.live_' + className + ' legend').addClass('board-min');
//      $('.live_' + className).css({'width': "35px"}).children().not($('.live_' + className + ' legend')).each(function () {
//        $(this).hide();
//      });
//    }
//    filtered++;
//    $.cookies.set('toolbarItem_' + className, className, {path: '/', hoursToLive: 168});
//    $('.live_' + className).css('display', 'inline-block');
//  }
//
//}

function Grouping()
{
  var toolbars = $('.board-block');
  $.each(toolbars, function (i, a) {
    var legend = $('.toolbar_item_lab', a).html();
    var rel = $(a).attr('rel');
    var newClass = 'live_' + rel;
    var items = $('.' + rel, '#list_block_abc');
    $.each(items, function (i, a) {
      $(a).clone(true).appendTo('.' + newClass, '#list_block_groups');
    });
  });
  var cookiesData = $.cookies.filter(/^toolbarItem_/);
  $.each(cookiesData, function (i, a) {
    getMode(a);
  });
  $('.live_item').click(
          function () {
            if ($(this).hasClass('list_block_item_active'))
            {
              $('.list_block_item_active').removeClass('list_block_item_active');
            } else {
              $('.list_block_item_active').removeClass('list_block_item_active');
              $(this).addClass('list_block_item_active');
            }
          }
  );
  RunMiner();
}

function getMode(REL)
{
  $('.board-block').each(function () {
    var rel = $(this).attr('rel');
    if (REL == rel)
    {
      $(this).children('legend').children('.bi-chevron-down').removeClass('bi-chevron-down').addClass('bi-chevron-right');
      $(this).css('overflow-y', 'unset');
      if($(window).width() < 1200)
      {
        $(this).css('height', '30px');
      }
      $(this).children('legend').addClass('board-min');
      $(this).css({'width': "35px"}).children().not($(this).children('legend')).each(function () {
        $(this).hide();
      });
    }
  });
}

function RunMiner()
{
  $('.board-block legend').click(function () {
    var rel = $(this).parent('.board-block').attr('rel');
    if ($(this).children('.bi-chevron-down').length > 0)
    {
      $(this).children('.bi-chevron-down').removeClass('bi-chevron-down').addClass('bi-chevron-right');
      $(this).parent('.board-block').css('overflow-y', 'unset');
      $(this).addClass('board-min').parent().css({'width': "35px"}).children().not($(this)).each(function () {
        $(this).hide();
      });
      if($(window).width() < 1200)
      {
        $(this).parent('.board-block').css('height', '30px');
      }
      $.cookies.set('toolbarItem_' + rel, rel, {path: '/', hoursToLive: 168});
    } else
    {
      $(this).children('.bi-chevron-right').removeClass('bi-chevron-right').addClass('bi-chevron-down');
      $(this).parent('.board-block').css('overflow-y', 'scroll');
      $(this).removeClass('board-min').parent().css({'width': "400px"}).children().not($(this)).each(function () {
        $(this).show();
      });
      if($(window).width() < 1200)
      {
        $(this).parent('.board-block').css('height', '30vw');
      }
      $.cookies.del('toolbarItem_' + rel);
    }
  });
}