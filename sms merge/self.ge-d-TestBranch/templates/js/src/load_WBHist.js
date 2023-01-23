$(document).ready(function ()
{
  $('.ID_wbhist', 'td').click(function ()
  {
    var id = $(this).attr('id').replace('wbhist', '');
    if ($(this).hasClass('opened_whist'))
    {
      $('#dresrow' + id).css('display', 'none');
      $(this).removeClass('opened_whist');
      $('#row' + id).removeClass('hoverRowFix');
      $('#tab' + id).removeClass('tab_open');
    } else
    {
      if ($('img', '#dresrow' + id).length > 0)
      {
        var link = '?option=s&service=wbhist&id=' + id;
        LoadData(link, '#dres' + id);
      }
      $('#dresrow' + id).css('display', 'table-row');
      $('#tab' + id).addClass('tab_open');
      $(this).addClass('opened_whist');
      $('#row' + id).addClass('hoverRowFix');
    }
  });
});