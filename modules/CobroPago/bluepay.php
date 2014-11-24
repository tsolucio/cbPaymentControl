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
require_once('modules/Invoice/Invoice.php');
require_once('bluepayv2php5class.php');
global $adb;

$srcModule = $_REQUEST['src_module'];
$srcRecord = $_REQUEST['src_record'];
$paymentType = $_REQUEST['payment_type'];
$cardNumber = $_REQUEST['card_number'];
$cardCVV = $_REQUEST['card_cvv'];
$cardExpiry = $_REQUEST['card_expiry'];
$achRoutingNumber = $_REQUEST['ach_routing_number'];
$achAccNumber = $_REQUEST['ach_acc_number'];
$achAccType = $_REQUEST['ach_acc_type'];

if ($srcModule=='Accounts') {
  $account = CRMEntity::getInstance('Accounts');
  $account->retrieve_entity_info($srcRecord, 'Accounts');
  $account->id = $srcRecord;
  if (empty($_POST)) {
    $smarty = new vtigerCRM_Smarty();
    $smarty->assign('APP', $app_strings);
    $smarty->assign('MOD', $mod_strings);
    $smarty->assign('MODULE', $srcModule);
    // TODO: Update Single Module Instance name here.
    $smarty->assign('SINGLE_MOD', 'SINGLE_Accounts'); 
    $smarty->assign('CATEGORY', $category);
    $smarty->assign('IMAGE_PATH', "themes/$theme/images/");
    $smarty->assign('THEME', $theme);
    $smarty->assign('ID', $srcRecord);
    $smarty->assign('MODE', 'edit');
    $smarty->assign('NAME', $account->column_fields['accountname']);
    $smarty->assign('UPDATEINFO',updateInfo($srcRecord));
    $smarty->assign('card_number', $cardNumber);
    $smarty->assign('card_cvv', $cardCVV);
    $smarty->assign('card_expiry', $cardExpiry);
    $smarty->assign('ach_routing_number', $achRoutingNumber);
    $smarty->assign('ach_acc_number', $achAccNumber);
    $smarty->assign('ach_acc_type', $achAccType);
    $smarty->display('modules/CobroPago/bluepay_form.tpl');
    return;
  }
  switch ($paymentType) {
  case 'card':
    $bp = authPayment($account, 0, $cardNumber, $cardCVV, $cardExpiry);
    break;
  case 'ach':
    $bp = authPaymentACH($account, 0, $achRoutingNumber, $achAccNumber, $achAccType);
    break;
  }
  if ($bp->getStatus()=='E') {
    echo "ERROR: ", $bp->getMessage();
    return;
  }
  $token = $bp->getTransId();
  $query = "update vtiger_account set token='{$token}' where accountid={$account->id}";
  $adb->query($query);
  echo "<script type=\"text/javascript\">location.href = 'index.php?action=DetailView&module={$srcModule}&record={$srcRecord}';</script>";
  return;
}
elseif ($srcModule=='CobroPago') {
  $cobropago = CRMEntity::getInstance('CobroPago');
  $cobropago->retrieve_entity_info($srcRecord, 'CobroPago');
  $cobropago->id = $srcRecord;
  $amount = $cobropago->column_fields['amount'];
  $accountId = $cobropago->column_fields['parent_id'];
  $account = CRMEntity::getInstance('Accounts');
  $account->retrieve_entity_info($accountId, 'Accounts');
  $account->id = $accountId;
  $token = $account->column_fields['token'];
  $bp = sendPayment($token, $amount);
  $cobropago->mode = 'edit';
  $cobropago->column_fields['paid'] = 1;
  $cobropago->column_fields['description'] = 'Response: '. $bp->getResponse() .'<br />'. 'TransId: '. $bp->getTransId() .'<br />'. 'Status: '. $bp->getStatus() .'<br />'. 'AVS Resp: '. $bp->getAvsResp() .'<br />'. 'CVV2 Resp: '. $bp->getCvv2Resp() .'<br />'. 'Auth Code: '. $bp->getAuthCode() .'<br />'. 'Message: '. $bp->getMessage() .'<br />'. 'Rebid: '. $bp->getRebid();
  $cobropago->save('CobroPago');
  if ($cobropago->column_fields['related_id'] && method_exists('Invoice', 'updateAmountDue')) {
    Invoice::updateAmountDue($cobropago->column_fields['related_id']);
  }
  echo "<script type=\"text/javascript\">location.href = 'index.php?action=DetailView&module={$srcModule}&record={$srcRecord}';</script>";
  return;
}

function sendPayment($token, $amount) {
  require('bluepay_config.php');
  $accountName = $account->column_fields['accountname'];
  $accountStreet = $account->column_fields['bill_street'];
  $accountCity = $account->column_fields['bill_city'];
  $accountState = $account->column_fields['bill_state'];
  $accountCode = $account->column_fields['bill_code'];
  $accountCountry = $account->column_fields['bill_country'];
  $accountPOBox = $account->column_fields['bill_pobox'];
  if (empty($bankacc)) {
    $bankacc = $account->column_fields['token'];
  }
  $bp = new BluePayment($BLUEPAY_AccountId, $BLUEPAY_SecretKey, $BLUEPAY_Mode);
  $bp->rebSale($token, $amount);
  $bp->setCustInfo(
    '',
    '',
    '',
    $accountName,
    '',
    $accountStreet,
    $accountCity,
    $accountCode,
    $accountPOBox,
    $accountCountry,
    '',
    ''
    );
  $bp->process();
  return $bp;
}

function authPayment($account, $amount, $bankacc, $cvv, $expiry) {
  require('bluepay_config.php');
  $accountName = $account->column_fields['accountname'];
  $accountStreet = $account->column_fields['bill_street'];
  $accountCity = $account->column_fields['bill_city'];
  $accountState = $account->column_fields['bill_state'];
  $accountCode = $account->column_fields['bill_code'];
  $accountCountry = $account->column_fields['bill_country'];
  $accountPOBox = $account->column_fields['bill_pobox'];
  $bp = new BluePayment($BLUEPAY_AccountId, $BLUEPAY_SecretKey, $BLUEPAY_Mode);
  $bp->auth($amount);
  $bp->setCustInfo(
    $bankacc,
    $cvv,
    $expiry,
    $accountName,
    '',
    $accountStreet,
    $accountCity,
    $accountCode,
    $accountPOBox,
    $accountCountry,
    '',
    ''
    );
  $bp->process();
  return $bp;
}

function authPaymentACH($account, $amount, $routing, $accnumber, $acctype) {
  require('bluepay_config.php');
  $accountName = $account->column_fields['accountname'];
  $accountStreet = $account->column_fields['bill_street'];
  $accountCity = $account->column_fields['bill_city'];
  $accountState = $account->column_fields['bill_state'];
  $accountCode = $account->column_fields['bill_code'];
  $accountCountry = $account->column_fields['bill_country'];
  $accountPOBox = $account->column_fields['bill_pobox'];
  $bp = new BluePayment($BLUEPAY_AccountId, $BLUEPAY_SecretKey, $BLUEPAY_Mode);
  $bp->auth($amount);
  $bp->setCustACHInfo(
    $routing,
    $accnumber,
    $acctype,
    $accountName,
    '',
    $accountStreet,
    $accountCity,
    $accountCode,
    $accountPOBox,
    $accountCountry,
    '',
    ''
    );
  $bp->processACH();
  return $bp;
}
?>