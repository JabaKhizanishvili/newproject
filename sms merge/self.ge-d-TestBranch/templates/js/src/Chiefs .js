function getChiefs(data, $ORG = 0)
{
  getViewChiefs(data, 0, $ORG);
}
function getChief(data, $ORG = 0)
{
  getViewChief(data, 0, $ORG);
}

function getViewChiefs(data, mode, $ORG)
{
  var Chiefs = $('.ChiefsData' + $ORG).val().split(',');
  var newChief = [data];
  var allChiefs = Chiefs.concat(newChief);
  data = allChiefs.join();
  var link = '?option=s&service=getChiefs&data=' + data + '&mode=' + mode + '&org=' + $ORG;
  LoadChiefsData(link, $ORG);
}
function getViewChief(data, mode, $ORG)
{
  var link = '?option=s&service=getChiefs&data=' + data + '&mode=' + mode + '&org=' + $ORG;
  LoadChiefsData(link, $ORG);
}
var Chiefs_in_progress = 0;
/**
 * 
 * @param {type} id
 * @returns {Boolean}
 */
function delChief(id, $Org)
{
  if (Chiefs_in_progress === 1)
  {
    alert('Please Wait!');
    return false;
  }
  Chiefs_in_progress = 1;
  if (!confirm('Are You Sure?'))
  {
    return false;
  }
  var Chiefs = $('.ChiefsData' + $Org).val().split(',');
  data = Chiefs.join();
  var link = '?option=s&service=getChiefs&data=' + data + '&delete=' + id + '&org=' + $Org;
  LoadChiefsData(link, $Org);
}

/**
 * 
 * @param {type} link
 * @returns {undefined}
 */
function LoadChiefsData(link, $ORG)
{
  $.get(link, function (response) {
    var CallResult = JSON.parse(response);
    if (CallResult.idx != '')
    {
      $('.ChiefsData' + $ORG).val(CallResult.idx.join());
    } else
    {
      $('.ChiefsData' + $ORG).val('');
    }

    $('.ChiefsContainer' + $ORG).html(CallResult.html);
    Chiefs_in_progress = 0;
  });
}


function CleanChiefsData($ORG)
{
  if (confirm('Are You Shure?'))
  {
    $('.ChiefsData' + $ORG).val('');
    $('.ChiefsContainer' + $ORG).html('');
  }
}

function SetOrg($ID)
{
  var $ORG = $("#paramsORG").val();
  $('#' + $ID).attr('href', $ORGURL.replace('_ORG_', $ORG));
}
function SetWGroups()
{
  var $ORG = $("#paramsORG").val();

  $('.org_groups').prop("disabled", true);
  $('.org_groups_l').hide();

  $('input[data-rel="org-' + $ORG + '"]').prop("disabled", false);

  $('div[data-rel="orgl-' + $ORG + '"]').show();
//  $('input[value="org-' + $ORG + '"]').attr();
}
