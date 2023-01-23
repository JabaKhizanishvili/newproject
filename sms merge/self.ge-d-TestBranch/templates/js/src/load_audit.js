$(document).ready(function ()
{
  $('.ID_detale', 'td').click(function ()
  {
   var  id = $(this).attr('id').replace('detale', '');
    if ($(this).hasClass('opened_detales'))
    {
      $('#dresrow' + id).css('display', 'none');
      $(this).removeClass('opened_detales');
      $('#row' + id).removeClass('hoverRowFix');
      $('#tab' + id).removeClass('tab_open');
    } else
    {
      if ($('img', '#dres' + id).length > 0)
      {
        var link = '?option=s&service=roleaudit&id=' + id;
        LoadData(link, '#dres' + id);
      }
      $('#dresrow' + id).css('display', 'table-row');
      $('#tab' + id).addClass('tab_open');
      $(this).addClass('opened_detales');
      $('#row' + id).addClass('hoverRowFix');
    }
  });
});