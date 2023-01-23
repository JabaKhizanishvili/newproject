function getWorker(data)
{
  var link = '?option=s&service=getWorker&data=' + data;
  $.get(link, function (response) {
    var CallResult = JSON.parse(response);
    if (CallResult.idx != '')
    {
      $('.WorkerData').val(CallResult.idx);
    } else {
      $('.WorkerData').val('');
    }
    $('.WorkerContainer').html(CallResult.html);
  });
}
function getOrgUnit(data, $ID)
{
  var link = '?option=s&service=getOrgUnit&data=' + data + '&org=' + $ID;
  $.get(link, function (response) {
    var CallResult = JSON.parse(response);
    if (CallResult.idx != '')
    {
      $('.OrgUnitData' + $ID).val(CallResult.idx);
    } else {
      $('.OrgUnitData' + $ID).val('');
    }
    $('.OrgUnitContainer' + $ID).html(CallResult.html);

  });
}

function HrsMinToDecimal(t){
  t = t.split(':');
  let Min = parseFloat(parseInt(t[0], 10) + parseInt(t[1], 10)/60).toFixed(4);
  return Min;
}
function DecimalToHrsMin(minutes){
  let min = Math.floor(Math.abs(minutes));
  let sec = Math.floor((Math.abs(minutes) * 60) % 60);
  return  min + ":" + sec;
}
