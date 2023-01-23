//$(document).ready(function () {
//  $('.my_tooltip').click(function () {
//    if ($('.full-text', this).length == 0)
//    {
//      var $Data = $('.my_tip', this).html();
//      var $textArea = $('<div class="text-info full-text" />');
//      $(this).append($textArea);
//      $('.full-text', this).html($Data);
//      $('span', this).css('display', 'none');
//    }
//  });
//});

$(document).ready(function () {
  $('.my_tooltip').dblclick(function (e) {
    if ($('.full-text', this).length == 0)
    {
      var $Data = $('.my_tip', this).html();
      var $Block = $('<div class="full-text-block" />');
      var $Container = $('<div class="text-info full-text" style="position:relative;" />');
      var $ContainerData = $('<snan class="full-text-data" />');
      var $ContainerIcon = $('<i class="bi bi-x-lg full-text-remove" style="cursor:pointer;padding: 0 0 0 10px; color:#990000;position:absolute; right:0px;" />');
      $(this).append($Block);
      $($Block, this).append($Container);
      $($Container, this).append($ContainerData);
      $($Container, this).append($ContainerIcon);

      $('.full-text .full-text-data', this).html($Data);
      $('span', this).not('.full-text-data').addClass('hidden');
      $('.full-text-data span', this).not('.full-text-data').removeClass('hidden');       
    } else {
      $('.hidden', this).removeClass('hidden');
      $('.full-text-block', this).remove();
    }
  });
});

