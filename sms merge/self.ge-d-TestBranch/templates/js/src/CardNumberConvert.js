function dec2bin(dec) {
  dec = ltrim(dec, '0');
  return (dec >>> 0).toString(2);
}

function ltrim(str, charlist) {
  charlist = !charlist ? ' \\s\u00A0' : (charlist + '').replace(/([[\]().?/*{}+$^:])/g, '$1');
  var re = new RegExp('^[' + charlist + ']+', 'g');
  return (str + '').replace(re, '');
}

function ConvertToWiegand($Number) {
  var $Binary = dec2bin($Number);
  var $CardCode = $Binary.slice(-16);
  var $FacilityCode = $Binary.slice(0, ($Binary.length - $CardCode.length));
//  return lpad(bin2dec($FacilityCode), '0', 3) + ':' + lpad(bin2dec($CardCode), '0', 5);
  return bin2dec($FacilityCode) + lpad(bin2dec($CardCode), '0', 5);
}

function bin2dec(bstr) {
  return parseInt((bstr + '')
          .replace(/[^01]/gi, ''), 2);
}
function lpad(strIn, padString, length) {
  var str = strIn.toString();
  while (str.length < length)
  {
    str = padString + str;
  }
  return str;
}