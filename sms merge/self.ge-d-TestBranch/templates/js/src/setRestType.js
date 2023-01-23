function setRestType()
{
  var $Value = $('input[id^=paramsREST_TYPE]:checked').val();
  switch ($Value) {
    case '1':
      $('#form-item-START_BREAK').css('display', 'block');
      $('#form-item-END_BREAK').css('display', 'block');
      $('#form-item-REST_MINUTES').css('display', 'none');
      break;
    case '2':
      $('#form-item-START_BREAK').css('display', 'none');
      $('#form-item-END_BREAK').css('display', 'none');
      $('#form-item-REST_MINUTES').css('display', 'block');
      break;
    case '3':
      $('#form-item-START_BREAK').css('display', 'none');
      $('#form-item-END_BREAK').css('display', 'none');
      $('#form-item-REST_MINUTES').css('display', 'block');
      break;
    case '4':
      $('#form-item-START_BREAK').css('display', 'block');
      $('#form-item-END_BREAK').css('display', 'block');
      $('#form-item-REST_MINUTES').css('display', 'block');
      break;
    default :
    case '0':
      $('#form-item-START_BREAK').css('display', 'none');
      $('#form-item-END_BREAK').css('display', 'none');
      $('#form-item-REST_MINUTES').css('display', 'none');
      break;
  }
}