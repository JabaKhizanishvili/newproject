function resetPhoto($ID)
{
  $('#' + $ID + 'Preview').html(' ');
  $('#' + $ID + 'Source').val('');
  $('#' + $ID + '-data').val('');
}

function resetCropPhoto(id) {
  var $previewWrapper = $('#' + id + 'Preview')
  $previewWrapper.find('#profile-pic').attr('src', '').css({'display': 'none'});

  var rightDiagLine = document.createElement('div');
  rightDiagLine.classList.add('rightDiagLine');
  var leftDiagLine = document.createElement('div');
  leftDiagLine.classList.add('leftDiagLine');

  $previewWrapper.append(rightDiagLine);
  $previewWrapper.append(leftDiagLine);

  $('#' + id + 'Source').val('');
  $('#' + id + '-data').val('');
}
