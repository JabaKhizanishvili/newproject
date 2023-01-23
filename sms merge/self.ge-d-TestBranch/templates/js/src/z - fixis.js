$(document).ready(() => {

  if ($("body").width() < 990 && $('.filter-block').height() > 350)
  {
    $(".filter-block").addClass('hide');
    $('<button id="filter-minimize">ფილტრი</button>').appendTo('.page_title');
  }

  $('#filter-minimize').click(function () {
    if ($(".filter-block").css('display') == 'none')
    {
      $(".filter-block").removeClass('hide');
      $(this).css({'background': '#00756A', 'color': '#ffffff'});
    } else
    {
      $(".filter-block").addClass('hide');
      $(this).css({'background': '#ffffff', 'color': '#00756A'});
    }
  });
  $('.collapsed').click(function () {
    if ($("body").width() < 600)
    {
      if ($('.l_header_block').length > 0)
      {
        $('.header_block').removeClass('l_header_block').addClass('navfill');
      } else
      {
        $('.header_block').addClass('l_header_block').removeClass('navfill');
      }
    }
  });
  if ($.cookie("HelpBox") == 1)
  {
    $('.msg02').show();
    $('.msg0 .bi-eye').hide();
    $('.msg0 .bi-eye-slash').show();
  }

  $('.msg0 .bi-eye-slash').click(function () {
    $('.msg02').hide();
    $('.msg0 .bi-eye').show();
    $(this).hide();
    $.cookie("HelpBox", 0);
  });
  $('.msg0 .bi-eye').click(function () {
    $('.msg02').show();
    $('.msg0 .bi-eye-slash').show();
    $(this).hide();
    $.cookie("HelpBox", 1);
  });
  $('.radiochild').click(function () {
    $(this).parent().children().removeClass('activeRadio').children('.checkmark').children('span').removeClass('bi bi-check');
    $(this).addClass('activeRadio').children('.checkmark').children('span').addClass('bi bi-check');
  });
//  var responsive_table_Width = $('#responsive_table .table').width();
//  $('#responsive_table').scroll(function () {
//    var left = $('#responsive_table').scrollLeft();
//    if (left < responsive_table_Width)
//    {
//      $('.footer_block').css({'padding-left': left + 'px'});
//    }
//  });

  $(".radiodiv").map(function (x) {
    if ($(this).width() < 623 && $('.radiodiv').eq(x).children('.radiochild').length > 2)
    {
      $(this).css({'flex-wrap': 'wrap'});
    }
  });
  if ($('.maindate').children().length < 2)
  {
    $('.server-time').css({'border-radius': '10px'});
  }

  if ($('.uploadblock').children('img').attr('src'))
  {
    $('.rightDiagLine').remove();
    $('.leftDiagLine').remove();
  }

//მენიუს ელემენტები
  $('.level_0').each(function () {
    var rel = $(this).data('rel');
    if ($(this).parent().children('.itemrow_' + rel).not(this).length > 0 || $(this).children('.itemrow_' + rel).length > 0)
    {
      $(this).parent().children('.itemrow_' + rel).not(this).hide().css({'margin-left': '19px'});
      $(this).children('.itemrow_' + rel).not(this).hide().css({'margin-left': '18px'});
      $('<span class="bi bi-chevron-down showLevels"></span>').insertAfter($(this).children('label'));
    }
  });
  $('.showLevels').click(function () {
    var rel = $(this).parent().data('rel');
    if ($(this).attr('class').split(' ').includes('bi-chevron-down'))
    {
      $(this).parent().parent().children('.itemrow_' + rel).not(this).not('.sub_' + rel).show();
      $(this).parent().children('.itemrow_' + rel).not(this).not('.sub_' + rel).show();
      $(this).removeClass('bi-chevron-down').addClass('bi-chevron-up');
    } else
    {
      $(this).parent().parent().children('.itemrow_' + rel).not($(this).parent()).hide();
      $(this).parent().children('.itemrow_' + rel).not($(this).parent()).hide();
      $(this).removeClass('bi-chevron-up').addClass('bi-chevron-down');
      $('.itemrow_' + rel + ' .bi').removeClass('bi-chevron-up').addClass('bi-chevron-down');
    }
  });
  //შეტყობინების ბლოკი ქვემოთ
  if ($('.msg0').length > 0 && $("body").width() < 990)
  {
    $('.msg0').parent().css({'order': '2'}).parent().css({'display': 'flex', 'flex-direction': 'column'});
  }

  $('.radioparent input').click(function () {
    var p = $(this).parent('.radio');
    var s = p.children('input').prop('checked');
    var rel = '.itemrow_' + p.attr('data-rel');
    $(this).click();
    $(rel).each(function () {
      if (s)
      {
        $(this).children('input').prop('checked', true);
      } else {
        $(this).children('input').prop('checked', false);
      }
    });
  });
//Boards... in generation js.

//გვერდითა მენიუ
  $('.close-menu').click(function () {

    if ($('.menu-block').width() > 100)
    {
      side_menu(0);
    } else
    {
      side_menu(1);
    }
  });
  WAIT('.menu-block .havechild', () => {

    $('.menu-block .havechild').hover(function () {

      var scroll = $(window).scrollTop();
      var top = $(this).offset().top - scroll;
      var height = $(window).height();
      var sub = $(this).children('.submenu').height();
      var sum = height - top;
      var last = sum - sub;
      if (last < 0)
      {
        $('.submenu', this).css('margin-top', 'calc(' + last + 'px - 10vh)');
      }

    }, () => {
      $('.submenu').css('margin-top', '-50px');
    }
    );
  });
  function WAIT(search, code) {
    const checkDiv = setInterval(() =>
    {
      if ($(search).length > 0)
      {
        clearInterval(checkDiv);
        code();
      }
    }, 100);
    setTimeout(() => {
      clearInterval(checkDiv);
    }, 1000);
  }
  function LOG(rame)
  {
    console.log(rame);
  }

  $('.close-menu').hover(function () {
    $('.user_drop2').toggleClass('open');
  });
//Side Menu Bar Fixing
  function sideFix()
  {
    if ($('.main_menu_left').length > 0 && $(window).width() > 1200)
    {
      var height = $(window).height() - 100;
      var ul = $('.main_menu_left').height();
      if (ul < height && $(window).scrollTop() > 90)
      {
        $('.menu_block_in').css('position', 'fixed');
        $('.menu_block_in').css('top', '0px');
        $('.submenu').css('left', ' calc(100% - 5px)');
        if ($(window).scrollTop() > 90)
        {
          $('.i_img1').attr('src', $('.main_logo img').attr('src'));
          if ($('.menu-block').width() < 100)
          {
            $('.menu_block_img').css('width', '50px');
          } else
          {
            $('.menu_block_img').css('width', '260px');
          }
          $('.menu_block_img').css('display', 'flex');
        }
      } else
      {
        $('.menu_block_in').css('position', 'unset');
        $('.menu_block_in').css('top', 'unset');
        $('.submenu').css('left', ' calc(100% - 5px)');
        if ($(window).scrollTop() < 90)
        {
          $('.menu_block_img').hide();
        }
      }
    }
  }

  $(window).resize(function () {
    sideFix();
  });
  $(window).scroll(() => {
    sideFix();
  });
  // calendar mode
  $('input').focus(function () {
    var calendar = $(this).parent().parent().children('.bfh-datepicker-calendar');
    var top = $(this).offset().top;
    var sum = top + calendar.height();
    var foot = $('.footer_wrapper').offset().top;
    if (sum > foot)
    {
      calendar.addClass('p_unset');
    } else
    {
      calendar.removeClass('p_unset');
    }
  });

  $(document).on('shown.bfhdatepicker', function (e) {
    var calendar = $('.bfh-datepicker-calendar').last();
    var top = $(calendar).offset().top + 200;
    var sum = top + calendar.height();
    var foot = $('.footer_wrapper').offset().top;
    console.log(sum, foot)
    if (sum > foot)
    {
      calendar.addClass('p_unset');
    } else
    {
      calendar.removeClass('p_unset');
    }
  });
//chosen-drop
  WAIT('.chosen-container', () => {
    $('.chosen-container').click(function () {
      var calendar = $(this).children('.chosen-drop');
      var top = $(this).offset().top;
      var sum = top + calendar.height();
      var foot = $('.footer_wrapper').offset().top;
      if (sum > foot)
      {
        calendar.addClass('p_unset');
      } else
      {
        calendar.removeClass('p_unset');
      }
    });
  });
  $('.holdtab').click(function () {

    var name = $(this).attr('view');
    $('.holidsall .holdtabchild').addClass('hide');
    $('#' + name).removeClass('hide');
    console.log(name);
    $(this).removeClass('disabled');
    $('.holdtab').not(this).addClass('disabled');
  });
  $("#CONSOLE_option").click(function () {
    $(this).css('color', 'yellow');
    setTimeout(() => {
      $(this).css('color', '#00c2cc');
    }, 500);
    var copytext = $('#CONSOLE_option_val').val().replace(/^\s+|\s+$/gm, '');
    navigator.clipboard.writeText(copytext);
  });
  $('#CONSOLE_option_val').keypress(function () {
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if (keycode == '13')
    {
      var command = $(this).val();
      var tab = 0;
      if (command.slice(-1) == '>')
      {
        command = command.replace('>', '');
        tab = 1;
      }
      var url = 'https://' + document.domain + '/?option=' + command;
      if (command)
      {
        if (tab == 1)
        {
          window.open(url, '_blank').focus();
        } else {
          window.location.assign(url);
        }

      } else
      {
        $($(this)).css('background', '#532121');
        setTimeout(() => {
          $($(this)).css('background', '#252525');
        }, 500);
      }
    }
  });
  $('#CONSOLE_command_val').keypress(function () {
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if (keycode == '13')
    {
      var cmd = $(this);
      var command = cmd.val();
      if (command && confirm('Command: ' + command))
      {
        $(cmd).val(command + ' ...');
        var url = '?option=s&service=console&command=' + command;
        $.get(url, function (echo)
        {
          var response = JSON.parse(echo.split('}')[0] + '}');
          var status = response.status;
          var action = status.split('=')[0];
          if (action == 'NEW_TAB')
          {
            var url = status.split('=')[1];
            window.open(url, '_blank').focus();
            status = '';
          }
          if (action == 'REDIRECT')
          {
            var url = status.split('=')[1];
            window.location.assign(url);
            status = '';
          }
          $(cmd).val(status);
        });
      } else
      {
        $(cmd).css('background', '#532121');
        setTimeout(() => {
          $(cmd).css('background', '#252525');
        }, 500);
      }
    }
  });
  var hrgraph_header_fix = '.hrgraph_header_fix .tk_header';
  if ($(hrgraph_header_fix).length > 0)
  {
    $(hrgraph_header_fix).css('top', '0px');
    $(window).scroll(() => {
      var scroll = $(window).scrollTop();
      var top = $('.hrgraph_header_fix').offset().top - 84;
      scroll -= top;
      $(hrgraph_header_fix).css('top', scroll + 'px');
    });
  }

  $('#CONSOLE_close').click(function () {
    if ($.cookie('CONSOLE') == 0)
    {
      $(this).children('i').removeClass('bi-chevron-compact-up');
      $(this).children('i').addClass('bi-chevron-compact-down');
      $('.CONSOLE').show();
      $.cookie('CONSOLE', 1);
    } else {
      $(this).children('i').removeClass('bi-chevron-compact-down');
      $(this).children('i').addClass('bi-chevron-compact-up');
      $('.CONSOLE').hide();
      $.cookie('CONSOLE', 0);
    }

  });
});
function CheckDate()
{
  var valid = true;
  if ($('.bfh-datepicker input').length > 0) {
    $('.bfh-datepicker input').each(function () {
      var val = $(this).val().split('-');
      var day = (val[0] <= 31) ? val[0] : '';
      var month = (val[1] <= 12) ? val[1] : '';
      var year = (val[2] > 1800) ? val[2] : '';
      var all = day + month + year;
      if ((all != '' && Number(all) > 0 && (!day || !month || !year)) || all != '' && Number(all) < 1)
      {
        $(this).val('');
        if (valid)
        {
          SetError(GlobalAlertCheckData);
          valid = false;
        }
      }
    });
  }
  if (!valid)
  {
    return false;
  }
  return true;
}

function SetError(text = '')
{
  if (text)
  {
    $('.error_message').remove();
    $('<div class="error_message noscript page-container"><i class="bi bi-x-lg"></i>' + text + '</div>').insertBefore('.page-container');
}
}

function XaddPWExternal($tmpl, $Container, $Data, $Worker)
{
  var $Add = true;
  $('.ncf-field' + $Worker).each(function ($V, $ID) {
    var $Value = $.trim($(this).val());
    if ($Value == '' || $Value == '0')
    {
      $Add = false;
    }
  });
  if (!$Add)
  {
//    alert('გთხოვთ, შეავსოთ ველები!');
//    return false;
  }
  var $ID = GenerateID();
  $Data.id = $ID;
  $($tmpl).tmpl($Data).appendTo($Container);
  SetAlertFeatures($ID);
  setFeatures($Worker + $ID);
}

function setFeatures($ID)
{
//  $("input", '#item_graph_block' + $ID).uniform();
  BindCalendar('ncf-field-date' + $ID);
//  $(".ncf-field-date", '#item_graph_block' + $ID).uniform();
//  $$('#select_image_' + $ID + ' a.modal-button').each(function (el) {
//    el.addEvent('click', function (e) {
//      new Event(e).stop();
//      SqueezeBox.fromElement(el);
//    });
//  });
}

function GenerateID() {
  var length = 10,
          charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
          retVal = "";
  for (var i = 0, n = charset.length; i < length; ++i) {
    retVal += charset.charAt(Math.floor(Math.random() * n));
  }
  return retVal;
}

function  SetAlertFeatures($ID)
{
  return $ID;
  $('#ncf-field-name' + $ID).geokbd();
  $('#ncf-field-company' + $ID).geokbd();
  $('#ncf-field-mobile' + $ID).mask('Z000-000-000KKKKK', {
    translation: {
      'Z': {
        pattern: /[\+]/, optional: true
      },
      'K': {
        pattern: /[0-9]/, optional: true
      }
    }
  });
}

//Bind Picker
var pickadateItems = new Array;
function BindCalendar(id)
{
  $('.' + id).each(function () {
    var $datepicker;

    $datepicker = {
      icon: 'bi bi-calendar4',
      align: 'left',
      input: 'form-control',
      placeholder: '',
      name: $(this).attr('data-name'),
      date: '',
      format: 'd-m-y',
      min: '',
      max: '',
      weekday: '',
      close: true
    };

    $(this).bfhdatepicker($datepicker);
  });
}

function bindCheck(element)
{
  var classs = '#' + $(element).attr('ch_date');
  console.log(classs);

  if ($(element).css('color') == 'rgb(211, 211, 211)')
  {
    $(element).css('color', 'green');
    $(classs).children('bfh-datepicker').prop('disabled', false);
    $(classs).show();
  } else {
    $(element).css('color', 'rgb(211, 211, 211)');
    $(classs).children('bfh-datepicker').prop('disabled', true);
    $(classs).hide();
  }

}
