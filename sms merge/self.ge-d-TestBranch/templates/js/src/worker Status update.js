function  GetWorkerStatus($Target, $UserName) {
  var link = 'status/' + $UserName + '/?t=' + Date.now();
  $.get(link, function (response, statusText, xhr) {
    if (xhr.status == 203)
    {
      window.location.reload();
    }
    $($Target).html(response);
  });
}



function  GetWorkerGPSStatus($Target, $UserName) {
  var link = 'status/' + $UserName + '/?t=' + Date.now();
  $.get(link, function (response) {
    $($Target).html(response);
  });
}