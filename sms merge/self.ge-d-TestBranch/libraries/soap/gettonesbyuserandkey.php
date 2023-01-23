<?php
defined('_EXEC') or die('Restricted access');
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:tmp">
    <soapenv:Header/>
    <soapenv:Body>
        <urn:getTonesByUserAndKey>
            <rbtUserId>{RBTUSERID}</rbtUserId>
            <keyword>{KEYWORD}</keyword>
            <price>{PRICE}</price>
            <order>{ORDER}</order>
            <dir>{DIR}</dir>
            <status>{STATUS}</status>
            <start>{START}</start>
            <limit>{LIMIT}</limit>
        </urn:getTonesByUserAndKey>
    </soapenv:Body>
</soapenv:Envelope>