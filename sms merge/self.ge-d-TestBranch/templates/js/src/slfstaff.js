
var Workers_in_progress = 0;

function Get_SlfStaff(data, Case = '', name = '', mode = '')
{
  var Workers = $('.WorkersData' + name).val().split(',');
  var newWorker = [data];
  var allWorkers = Workers.concat(newWorker);
  if (mode !== 'single')
  {
    data = allWorkers.join();
  }
  var link = '?option=s&service=slfstaff&data=' + data + '&case=' + Case + '&name=' + name + '&tmpl=' + mode;
  Load_SlfStaff(link, name);
}

function Load_SlfStaff(link, name = '')
{
  $.get(link, function (response) {
    var CallResult = JSON.parse(response);
    if (CallResult.idx != '')
    {
      $('.WorkersData' + name).val(CallResult.idx.join());
    } else {
      $('.WorkersData' + name).val('');
    }
    $('.WorkersContainer' + name).html(CallResult.html);
    Workers_in_progress = 0;
  });
}

function Delete_SlfStaff(id, name = '', Case = '', tmpl = '')
{
  if (Workers_in_progress === 1)
  {
    alert('Please Wait!');
    return false;
  }
  Workers_in_progress = 1;
  if (!confirm('Are You Sure?'))
  {
    return false;
  }
  var Workers = $('.WorkersData' + name).val().split(',');
  var data = Workers.join();

//  console.log('Workers: ' + Workers);
//  console.log('ID: ' + id);
  var link = '?option=s&service=slfstaff&data=' + data + '&delete=' + id + '&name=' + name + '&case=' + Case + '&tmpl=' + tmpl;
  LoadGroupWorkersData(link, name);
}