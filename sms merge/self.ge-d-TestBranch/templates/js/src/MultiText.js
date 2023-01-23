function SetMultiText($ID)
{
  var $Data = $('#' + $ID + '_value').val().split('|');
  $.each($Data, function (i, $Value) {
    _AddMultiText($ID, $Value);
  });
}
function AddMultiText($ID)
{
  var $Number = $.trim($('#' + $ID + '_input').val());
  if ($Number != '')
  {
    _AddMultiText($ID, $Number);
  }
  var $Number = $('#' + $ID + '_input').val('');

}
function AddMultiTextKeyPress($ID, event)
{
  var keycode = (event.keyCode ? event.keyCode : event.which);
  if (keycode == '13') {
    AddMultiText($ID);
  }
}
function _AddMultiText($ID, $Value) {
  if ($Value == '')
  {
    return;
  }
  var $Data = $('#' + $ID + '_value').val().split('|');
  var $NData = [];
  if ($('#MULTI_ITEM_' + $Value).length > 0)
  {
    return;
  }

  $Data.push($Value);
  $Data = Object.values(array_flip(array_flip($Data)));
  var $H = '<div class="ContractItem" id="MULTI_ITEM_' + $Value + '"><div class="ContractItem_name">'
          + $Value
          + '</div><div class="Contracttools"><span class="Contracttool" onclick="DeleteMultiText(\''
          + $Value
          + '\', \'' + $ID + '\');"> <a class="bi bi-x-lg" data-option-array-index="0"></a></span></div><div class="cls"></div></div>';
  $('#' + $ID + '_container').append($H);
  $('#' + $ID + '_value').val($Data.join('|'));
}
function DeleteMultiText($Value, $ID) {
  $('#MULTI_ITEM_' + $Value).remove();
  var $Data = $('#' + $ID + '_value').val().split('|');
  var $NData = [];
  $.each($Data, function (i, $V) {
    if ($Value != $V)
    {
      $NData.push($V);
    }
  });
  $('#' + $ID + '_value').val($NData.join('|'));
}