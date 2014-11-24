<?php
/******************************************************************************************************
 * Copyright 2012 JPL TSolucio, S.L.  --  This file is a part of CobroPago vtiger CRM Extension.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
 ******************************************************************************************************/
require_once('modules/CobroPago/enletras.php');

$dias = array(
  'Domingo',
  'Lunes',
  'Martes',
  'Miércoles',
  'Jueves',
  'Viernes',
  'Sábado',
);

$meses = array(
  'Enero',
  'Febrero',
  'Marzo',
  'Abril',
  'Mayo',
  'Junio',
  'Julio',
  'Agosto',
  'Septiembre',
  'Octubre',
  'Noviembre',
  'Diciembre',
);

$query = "select vtiger_cobropago.*, vtiger_crmentity.* ,
coalesce(vtiger_account.accountname, vtiger_contactdetails.lastname) as payer1,
coalesce(vtiger_contactdetails.firstname, '') as payer2,
coalesce(vtiger_troubletickets.title, vtiger_purchaseorder.subject, vtiger_salesorder.subject, vtiger_invoice.subject, vtiger_quotes.subject) as document
from vtiger_cobropago left join vtiger_account on vtiger_cobropago.parent_id=vtiger_account.accountid
left join vtiger_contactdetails on vtiger_cobropago.parent_id=vtiger_contactdetails.contactid
left join vtiger_troubletickets on vtiger_cobropago.parent_id=vtiger_troubletickets.ticketid
left join vtiger_purchaseorder on vtiger_cobropago.parent_id=vtiger_purchaseorder.purchaseorderid
left join vtiger_salesorder on vtiger_cobropago.parent_id=vtiger_salesorder.salesorderid
left join vtiger_invoice on vtiger_cobropago.parent_id=vtiger_invoice.invoiceid
left join vtiger_quotes on vtiger_cobropago.parent_id=vtiger_quotes.quoteid
LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_cobropago.cobropagoid
where vtiger_cobropago.cobropagoid={$_REQUEST['record']}";
$res = $adb->query($query);

$reference = $adb->query_result($res, 0, 'reference');
$amount = $adb->query_result($res, 0, 'amount');
$date = $adb->query_result($res, 0, 'duedate');
$pmode = $adb->query_result($res, 0, 'paymentmode');
$cobro = $adb->query_result($res, 0, 'credit');
$description = $adb->query_result($res, 0, 'description');
$payerLastName = $adb->query_result($res, 0, 'payer1');
$payerFirstName = $adb->query_result($res, 0, 'payer2');
$document = $adb->query_result($res, 0, 'document');
$cant_letras = enletras($amount);

if($cobro == 1){
  $txt_entrega = 'recibe de';
  $person = $payerFirstName.' '.$payerLastName;
}else{
  $txt_entrega = 'entrega a';
  $SQL_COM = 'SELECT first_name,last_name FROM vtiger_users WHERE id=?';
  $res_com = $adb->pquery($SQL_COM,array($adb->query_result($res, 0, 'comercialid')));
  $person = $adb->query_result($res_com,0,'first_name').' '.$adb->query_result($res_com,0,'last_name');
}

$query = "select logoname, organizationname, address from vtiger_organizationdetails";
$res = $adb->query($query);
$logoname = $adb->query_result($res, 0, 'logoname');
$organizationname = $adb->query_result($res, 0, 'organizationname');
$address = $adb->query_result($res, 0, 'address');


list($year, $month, $day) = explode('-', $date);
$time = mktime(0,0,0,$month,$day,$year);
$week_day = date('w', $time);
$num_day = date('d', $time);
$num_month = date('n', $time);
$num_year = date('Y', $time);
$fulldate = $dias[$week_day].', '.$num_day.' de '.$meses[$num_month-1].' de '.$num_year;
?>
<html>
<head>
<title>Payment Ref: <?=$reference?></title>
<style type="text/css">
body {
  padding: .7em;
  border: 1px solid black;
}
.row {
  clear: both;
  margin: .5em 0;
  padding: .1em 0;
}
.underline {
  border-bottom: 1px solid lightgray;
}
</style>
</head>
<body>

<div class="row" style="float:right;margin-left:1em;padding:.2em;border:1px solid black;"><?=$mod_strings['BILL_REF']?>
  <?=$reference?>
</div>

<div style="margin-bottom:1em;font-size:.8em">
<img src="test/logo/<?=$logoname?>"/>
</div>

<div class="row underline">
  Se <?=$txt_entrega?> <?=$person?> la cantidad de: <?=$cant_letras?> € en concepto de <?=$description?>
</div>

<div class="row underline">
<?=$mod_strings['BILL_AMOUNT']?> <?=$amount?>
</div>

<div class="row" style="text-align:right;">
  <?=$mod_strings['BILL_DATE']?> <?=$fulldate?>
</div>

</body>
</html>
