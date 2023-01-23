function resetFilter()
{
  $('input', '.filter-block').val('');
  $('select', '.filter-block').val('-1');
  setFilter(0);
}
function setFilter(set = 1)
{
  $('#start').val(0);

  if (set == 1)
  {
    if (!CheckDate())
    {
      return;
    }
  }

  $('#fform').submit();
}


$(document).ready(function () {
  $("input, select", '.filter-block').keypress(function (event) {
    if (event.which == 13) {
      setFilter();
    }
  });
});

