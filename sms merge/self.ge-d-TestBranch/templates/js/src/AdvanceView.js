function AdvanceView()
{
  var datastring = $("#fform").serialize();
  var link = '?' + datastring + '&option=advance_view&tmpl=modal&iframe=true&height=97%&width=97%';
  $.fn.prettyPhoto();
  $.prettyPhoto.open(link, '', '');
}
$(document).ready(function () {
  $('a.modal-frame').prettyPhoto();
});
