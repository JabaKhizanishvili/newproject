function getWorkers(data, $ORG)
{
  getViewWorkers(data, 0, $ORG);
}

function getGroupWorkers(data)
{
  console.log('data: ' + data);
  var Workers = $('.WorkersData').val().split(',');
  var newWorker = [data];
  var allWorkers = Workers.concat(newWorker);
  data = allWorkers.join();
  var link = '?option=s&service=getGroupWorkers&data=' + data;
  LoadGroupWorkersData(link);
}
function delGroupWorker($Worker)
{
  if (Workers_in_progress === 1)
  {
    alert('Please Wait!');
    return false;
  }
  Workers_in_progress = 1;
//  if (!confirm('Are You Sure?'))
//  {
//    return false;
//  }
  var Workers = $('.WorkersData').val().split(',');
  var data = Workers.join();
  var link = '?option=s&service=getGroupWorkers&data=' + data + '&delete=' + $Worker;
  LoadGroupWorkersData(link);
}


function getUniqWorker(data)
{
  var link = '?option=s&service=getUWorkers&data=' + data;
  LoadGroupWorkersData(link);
}
function getUniqWorkers(data, mode, name = '')
{
  var type = '';
  if (mode !== '')
  {
    type = '&type=' + mode;
  }
  var Workers = $('.WorkersData' + name).val().split(',');
  var newWorker = [data];
  var allWorkers = Workers.concat(newWorker);
  data = allWorkers.join();
  var link = '?option=s&service=getUWorkers' + type + '&data=' + data + '&name=' + name;
  LoadGroupWorkersData(link, name);
}
function getViewWorkers(data, mode, $ORG)
{
  var Workers = $('.WorkersData' + $ORG).val().split(',');
  var newWorker = [data];
  var allWorkers = Workers.concat(newWorker);
  data = allWorkers.join();
  var link = '?option=s&service=getWorkers&data=' + data + '&mode=' + mode + '&org=' + $ORG;
  LoadWorkersData(link, $ORG);
}
function getGWorkers(data, $ID, mode)
{
  var Workers = $('.WorkersData').val().split(',');
  var newWorker = [data];
  var allWorkers = Workers.concat(newWorker);
  data = allWorkers.join();
  var link = '?option=s&service=getGWorkers&data=' + data + '&mode=' + mode + '&group=' + $ID;
  $.get(link, function (response) {
    var CallResult = JSON.parse(response);
    if (CallResult.idx != '')
    {
      $('.WorkersData').val(CallResult.idx.join());
    } else {
      $('.WorkersData').val('');
    }
    $('.WorkersContainer').html(CallResult.html);
    Workers_in_progress = 0;
  });
}
function getReWorkers(data, $ID)
{
  var Workers = $('.WorkersData').val().split(',');
  var newWorker = [data];
  var allWorkers = Workers.concat(newWorker);
  data = allWorkers.join();
  var link = '?option=s&service=getReWorkers&data=' + data + '&id=' + $ID;
  LoadWorkersData(link);
}

var Workers_in_progress = 0;
/**
 * 
 * @param {type} id
 * @returns {Boolean}
 */
function delWorker(id, $ORG = 0)
{
  if (Workers_in_progress === 1)
  {
    alert('Please Wait!');
    return false;
  }
  Workers_in_progress = 1;
//  if (!confirm('Are You Sure?'))
//  {
//    return false;
//  }
  var Workers = $('.WorkersData' + $ORG).val().split(',');
  data = Workers.join();
  var link = '?option=s&service=getWorkers&data=' + data + '&delete=' + id + '&org=' + $ORG;
  LoadWorkersData(link, $ORG);
}


function delGWorker(id, $ID)
{
  if (Workers_in_progress === 1)
  {
    alert('Please Wait!');
    return false;
  }
  Workers_in_progress = 1;
  var Workers = $('.WorkersData').val().split(',');
  var newWorker = [data];
  var allWorkers = Workers.concat(newWorker);
  var data = allWorkers.join();
  var link = '?option=s&service=getGWorkers&data=' + data + '&group=' + $ID + '&delete=' + id;
  $.get(link, function (response) {
    var CallResult = JSON.parse(response);
    if (CallResult.idx != '')
    {
      $('.WorkersData').val(CallResult.idx.join());
    } else {
      $('.WorkersData').val('');
    }
    $('.WorkersContainer').html(CallResult.html);
    Workers_in_progress = 0;
  });



}

function delUWorker(id, name = '')
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

  console.log('Workers: ' + Workers);
  console.log('ID: ' + id);
  var link = '?option=s&service=getUWorkers&data=' + data + '&delete=' + id + '&name=' + name;
  LoadGroupWorkersData(link, name);
}

function delReWorker($Worker, $ID)
{
  if (Workers_in_progress === 1)
  {
    alert('Please Wait!');
    return false;
  }
  Workers_in_progress = 1;
//  if (!confirm('Are You Sure?'))
//  {
//    return false;
//  }
  var Workers = $('.WorkersData').val().split(',');
  data = Workers.join();
  var link = '?option=s&service=getWorkers&data=' + data + '&delete=' + $Worker + '&id=' + $ID;
  LoadWorkersData(link);
}

/**
 * 
 * @param {type} link
 * @returns {undefined}
 */
function LoadWorkersData(link, $ORG)
{
  $.get(link, function (response) {
    var CallResult = JSON.parse(response);
    if (CallResult.idx != '')
    {
      $('.WorkersData' + $ORG).val(CallResult.idx.join());
    } else {
      $('.WorkersData' + $ORG).val('');
    }
    $('.WorkersContainer' + $ORG).html(CallResult.html);
    Workers_in_progress = 0;
  });
}
/**
 * 
 * @param {type} link
 * @returns {undefined}
 */
function LoadGroupWorkersData(link, name = '')
{
  console.log(link);

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
function CleanWorkersData($ORG)
{
  if (confirm('Are You Shure?'))
  {
    $('.WorkersData' + $ORG).val('');
    $('.WorkersContainer' + $ORG).html('');
  }
}
