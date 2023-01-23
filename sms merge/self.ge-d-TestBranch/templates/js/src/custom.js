var select_heght = 0;
$(document).ready(function ()
{
  $('#username').focus();
//  $("input").not(".no_uniform,.skip_this").uniform();
//  $("select").not(".skip_this,.dropsearch").uniform();
//  $("textarea").not(".skip_this").uniform();
  $("#checknid").click(function () {
    if ($(this).is(':checked'))
    {
      $('.checknid').each(function () {
        if (!$(this).is(':checked'))
        {
          $(this).click();
        }
      });
    } else
    {
      $('.checknid').each(function () {
        if ($(this).is(':checked'))
        {
          $(this).click();
        }
      });
    }
  });
//  $('.page_content table').not('#exportable').wrap('<div id="responsive_table"></div>');

  $(".pasdword-state").click(function () {
    var inputType = $('input', $(this).parent('.form-group')).attr('type');
    if (inputType === 'password') {
      $('input', $(this).parent('.form-group')).attr('type', 'text');
      $('i', this).addClass('bi-eye').removeClass('bi-eye-slash');
    } else {
      $('input', $(this).parent('.form-group')).attr('type', 'password');
      $('i', this).addClass('bi-eye-slash').removeClass('bi-eye');
    }
  });


  $('.c_resolution_data').click(function () {
    if ($(this).val() == 2)
    {
      $('#' + $(this).attr('data-rel')).removeClass('hidden');
    } else {
      $('#' + $(this).attr('data-rel')).addClass('hidden');
    }
  });
});
function setOrder(order)
{
  $('#order').val(order);
  $('#dir').val(1 - $('#dir').val());
  $('#fform').submit();
}

function doAction(option, task, check, validate, $Btn) {
  var checked = false;

  if (task == 'save' || task == 'apply' || task == 'display')
  {
    if (!CheckDate())
    {
      return;
    }
  }

  if (check)
  {
    $('.checknid').each(function () {
      if ($(this).is(':checked'))
      {
        checked = true;
      }
    });
  } else
  {
    checked = true;
  }


  if (checked)
  {
    if (validate && !confirm(GlobalAlertConfirm))
    {
      $($Btn).attr('disabled', false);
      return;
    }
    $('input[name=task]').val(task);
    $('input[name=option]').val(option);
    $($Btn).attr('disabled', 'disabled');
    document.fform.submit();
    setTimeout(function () {
      $('input[name=task]').val('');
      document.fform.target = '_self';
    }, 500);
  } else
  {
    alert(GlobalAlertSelectRows);
  }


}

function SetDoAction($ID, option, task, check, validate) {
  var checked = false;
  $('.checknid').each(function () {
    if ($(this).is(':checked'))
    {
      $(this).click();
    }
  });
  $('#checknid' + $ID).click();
  if (check)
  {
    $('.checknid').each(function () {
      if ($(this).is(':checked'))
      {
        checked = true;
      }
    });
  } else
  {
    checked = true;
  }
  if (checked)
  {
    if (validate && !confirm(GlobalAlertConfirm))
    {
      return;
    }
    $('input[name=task]').val(task);
    $('input[name=option]').val(option);
    document.fform.submit();
  } else
  {
    alert(GlobalAlertSelectRows);
  }


}

function doStateAction(option, task, id)
{
  $('.checknid').each(function () {
    if ($(this).is(':checked'))
    {
      $(this).click();
    }
  });
  $('#checknid' + id).click();
  doAction(option, task);
}



var $GPSoption, $GPStask, $GPScheck, $GPSvalidate, $GPSBtn;


function doActionGPS(option, task, check, validate, $Btn) {
  $GPSoption = option;
  $GPStask = task;
  $GPScheck = check;
  $GPSvalidate = validate;
  $GPSBtn = $Btn;
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(SetLocation);
  } else {
    alert('Geolocation is not supported by Your browser.');
  }
  return false;

}

function doActionGPSDone() {
  var checked = false;
  if ($GPScheck)
  {
    $('.checknid').each(function () {
      if ($(this).is(':checked'))
      {
        checked = true;
      }
    });
  } else
  {
    checked = true;
  }
  if (checked)
  {
    if ($GPSvalidate && !confirm(GlobalAlertConfirm))
    {
      $($GPSBtn).attr('disabled', false);
      return;
    }
    $('input[name=task]').val($GPStask);
    $('input[name=option]').val($GPSoption);
    $($GPSBtn).attr('disabled', 'disabled');
    document.fform.submit();
    setTimeout(function () {
      $('input[name=task]').val('');
      document.fform.target = '_self';
    }, 500);
  } else
  {
    alert(GlobalAlertSelectRows);
  }
}


function SetLocation(position) {
  var latitude = position.coords.latitude;
  var longitude = position.coords.longitude;
  $('input[name=latitude]').val(latitude);
  $('input[name=longitude]').val(longitude);
  doActionGPSDone();
}

