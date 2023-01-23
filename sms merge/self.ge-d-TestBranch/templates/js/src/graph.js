var CurrentCell = '';
$(document).ready(function () {
  /**
   * Add Cell Click Function
   */
  $('.tk_i_in').click(function () {
    $('.hoverRowFix').removeClass('hoverRowFix');
    $(this).addClass('hoverRowFix');
    var $workerId = $(this).attr('class').match(/\bworker[0-9]+/g);
    var $worker = $workerId[0].replace('worker', 'tr_');
    var $DayId = $(this).attr('class').match(/\bday[0-9]+/g);
    var $Day = $DayId[0].replace('day', 'head_');
    $('#' + $worker).addClass('hoverRowFix');
    $('#' + $Day).addClass('hoverRowFix');
    $(this).addClass('hoverRowFix');
    CurrentCell = this;
    showTimes(this);
  });

  $('.tk_head_in').click(function () {
    $('.hoverRowFix').removeClass('hoverRowFix');
    $(this).addClass('hoverRowFix');
    var $DayId = $(this).attr('id');
    var $Day = $DayId.replace('head_', 'day');
    $('.' + $Day).addClass('hoverRowFix');
    $(this).addClass('hoverRowFix');
    CurrentCell = this;
    showTimes(this);
  });

  $('.tk_user_in').hover(
          function () {
            var $worker = $(this).attr('id').replace('tr_', 'worker');
            $('.' + $worker).addClass('hoverRow');
            $(this).addClass('hoverRow');
          },
          function () {
            $(this).removeClass('hoverRow');
            $('.hoverRow', '.tk_i').removeClass('hoverRow');
          });

  $('.tk_i_in, .tk_i_in_d').hover(
          function () {
            var $workerId = $(this).attr('class').match(/\bworker[0-9]+/g);
            var $worker = $workerId[0].replace('worker', 'tr_');
            var $workerID = $worker.replace('tr_', 'worker');
            var $DayId = $(this).attr('class').match(/\bday[0-9]+/g);
            var $Day = $DayId[0].replace('day', 'head_');
            $('#' + $worker).addClass('hoverRow');
            $('#' + $Day).addClass('hoverRow');
            if (GlobalGraphCrossHover)
            {
              $('.' + $DayId).addClass('hoverRow2');
              $('.' + $workerID).addClass('hoverRow2');
            }
//            console.log($('.' + $worker));

            $(this).addClass('hoverRow');
          },
          function () {
            $(this).removeClass('hoverRow');
            $('.hoverRow', '.tk_user').removeClass('hoverRow');
            $('.hoverRow', '.tk_head').removeClass('hoverRow');
            $('.hoverRow', '.tk_i').removeClass('hoverRow');
            if (GlobalGraphCrossHover)
            {
              $('.hoverRow2', '.tk_i').removeClass('hoverRow2');
            }
          });

  setHeaderHover('.tk_head_in, .tk_head_in_t');

  $('.tk_graph_time').click(function () {
    var $cell = $(CurrentCell);
    var $color = rgb2hex($(this).css('background-color'));
    var $html = $(this).html();
    var $ID = $(this).attr('id').replace('g_', '');
    if ($('input', $cell).length)
    {
      sendCellData($cell, $color, $html, $ID);
    } else
    {
      var $Class = $cell.attr('id').replace('head_', 'day');
      $('.' + $Class, '.tk_day_data').each(function () {
        if (!$(this).hasClass('tk_i_in_d'))
        {
          sendCellData(this, $color, $html, $ID);
        }
      });
    }
    hideTimes();
  });
});

function ProcessBG($obj)
{
  var $trID = 'tr_' + $($obj).attr('id');
  if ($($obj).val() === '-1')
  {
    $('#' + $trID).addClass('graph_red');
    $('#' + $trID).removeClass('graph_green');
  } else
  {
    $('#' + $trID).addClass('graph_green');
    $('#' + $trID).removeClass('graph_red');
  }
}

function rgb2hex(rgb) {
  rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
  function hex(x) {
    return ("0" + parseInt(x).toString(16)).slice(-2);
  }
  return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}

function showTimes()
{
  $("#timeGraph").css('display', 'block');
  $(".tk_graph_times_overlay").css('display', 'block');
  $(".tk_graph_times_overlay").css('height', $(document).height());
  $(".tk_graph_times_overlay").css('width', $(document).width());
  $(".tk_graph_times_overlay").css('opacity', 0.2);
  $(".tk_graph_times").css('margin-top', (0 - $(".tk_graph_times").height() / 2));
}

function hideTimes()
{
  $('.hoverRowFix').removeClass('hoverRowFix');
  $("#timeGraph").css('display', 'none');
}
function removeToolTip($Item)
{
  $($Item).removeClass('hoverRowFix');
  $("#timeGraph").css('display', 'none');
}

function SaveCellData($data)
{
  var $Link = '?option=s&service=savecelldata';
  $.ajax({
    type: 'POST',
    url: $Link,
    data: $data,
    success: function (data) {
      console.log(data);

      return data.status;
    },
    dataType: 'json',
    async: false
  });
//
// 
}

function sendCellData($cell, $color, $html, $ID, $Confirm = 0)
{

  var $workerId = $($cell).attr('class').match(/\bworker[0-9]+/g);
  var $worker = $workerId[0].replace('worker', '');
  var $DayId = $($cell).attr('class').match(/\bday[0-9]+/g);
  var $Day = $DayId[0].replace('day', '');
  var $YearId = $($cell).attr('class').match(/\byear[0-9]+/g);
  var $Year = $YearId[0].replace('year', '');
  var $PostData = {worker: $worker, day: $Day, year: $Year, time_id: $ID, confirm: $Confirm};
  var $Link = '?option=s&service=savecelldata';
  $.post($Link, $PostData, function (data) {
    if (data.status == 1)
    {
      $($cell).css('background-color', $color);
      $('.my_tooltip', $cell).replaceWith("<span></span>");
      $('span', $cell).html($html);
      $('input', $cell).val($ID);
      ReloadWeekSum($worker, $Year, $Day);
    } else if (data.status == -1)
    {
      var $Result = confirm(data.message);
      if ($Result)
      {
        return sendCellData($cell, $color, $html, $ID, data.confirm);
      } else {
        return false;
      }

    } else {
      alert(data.message);
    }
  }, 'json');
}
function ReloadWeekSum($worker, $Year, $Day)
{
  var $PostData = {worker: $worker, day: $Day, year: $Year};
  var $Link = '?option=s&service=reloadweeksum&t=' + new Date().getTime();
  $.post($Link, $PostData, function (data) {
    if (data.status == 1)
    {
      var $Selector = '.tk_time_sum_' + $worker + '_' + $Year + '_' + data.day;
      $($Selector).html(data.timesum);
      $($Selector).attr('class', function (i, c) {
        return c.replace(/(^|\s)time_sum_\S+/g, '');
      });
      $($Selector).addClass('time_sum_' + data.class);
    }
  }, 'json');
}

var UpdateTimesInProgress = 0;
function UpdateTimes()
{
  if (UpdateTimesInProgress === 1)
  {
    alert('Please Wait!');
    return false;
  }
  UpdateTimesInProgress = 1;
  var $StartDate = $('[name="start_date"]').val();
  var $EndDate = $('[name="end_date"]').val();
  var $Group = $('[name="group_id"]').val();
  var link = '?option=s&service=gettimedata&start=' + $StartDate + '&end=' + $EndDate + '&group=' + $Group;
  $.get(link, function (response) {
    $('#tk_time_data').html(response);
    UpdateTimesInProgress = 0;
    setHeaderHover('.tk_head_in_t');
    $('a.modal-frame').prettyPhoto();
  });
}


function setHeaderHover($class)
{
  $($class).hover(
          function () {
            var $ID = $(this).attr('id').replace('head_', '').replace('_t', '');
            var $Day = 'day' + $ID;
            $('.' + $Day).addClass('hoverRow');
            $('#head_' + $ID + '_t').addClass('hoverRow');
            $('#head_' + $ID).addClass('hoverRow');
          },
          function () {
            $(this).removeClass('hoverRow');
            $('.hoverRow', '.tk_user').removeClass('hoverRow');
            $('.hoverRow', '.tk_head').removeClass('hoverRow');
            $('.hoverRow', '.tk_i').removeClass('hoverRow');
          });
}
