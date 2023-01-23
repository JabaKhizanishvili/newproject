<?php
defined('_EXEC') or die('Restricted access');
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:tmp">
    <soapenv:Header/>
    <soapenv:Body>
        <urn:getCategories/>
    </soapenv:Body>
</soapenv:Envelope>