function InsertMultipleItems() {
  var $Items = [];
  var $ID = 0;
  $('.checknid').each(function () {
    if ($(this).is(':checked'))
    {
      $Items.push($(this).val());
    }
  });
  if ($Items.length === 0)
  {
    return false;
  }
  var $Func = $('input[name="js"]').val();
  window.parent[$Func]($Items);
  window.parent.$.prettyPhoto.close();
  return true;
}
