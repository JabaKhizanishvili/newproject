function setGraphActior($Org, $ID)
{
  if ($('#' + $Org + $ID).val() === '0')
  {
    $('#' + $Org + 'GRAPHGROUP').prop('disabled', false).trigger("chosen:updated");
  } else
  {
    $('#' + $Org + 'GRAPHGROUP').prop('disabled', true).trigger("chosen:updated");
  }
}

