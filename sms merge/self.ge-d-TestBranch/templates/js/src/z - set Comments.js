$(document).ready(function () {
  $('.commentTip').each(function () {
    var $ID = $(this).attr('id').replace('flow_', '');
    $(this).tooltip({
      tooltipSourceURL: '?option=s&service=comments&data=' + $ID
              + '&format=html' +
              '&start_date=' + $('input[name*="start_date"]').val()
              + '&end_date=' + $('input[name*="end_date"]').val(),
      loader: 1,
      loaderImagePath: 'templates/images/ajax.gif',
      loaderHeight: 16,
      loaderWidth: 17,
      width: '650px',
      height: 'auto',
      tooltipSource: 'ajax',
      borderSize: '2'
    });

  });
});