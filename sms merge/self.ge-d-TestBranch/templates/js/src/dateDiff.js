function LoadCalculateDiff($SItem, $EItem, $Target)
{
  $StartDate = $($SItem).val();
  $EndDate = $($EItem).val();
  var link = '?option=s&service=daycount&start=' + $StartDate + '&end=' + $EndDate;
  $.get(link, function (data) {
    var $D = JSON.parse(data);
    $($Target).val($D.count);
  });
  return;
}
