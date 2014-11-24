/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

function receivePayment(id) {
  var status = checkPayment(id);
  if (status=='') {
    location.href = 'index.php?module=CobroPago&action=bluepay&src_module=CobroPago&src_record='+id;
  }
  else {
    alert(status);
  }
}

function checkPayment(id) {
  var xhr = new Ajax.Request('index.php',
    {
      method: 'post',
      asynchronous: false,
      parameters: 'module=CobroPago&action=CobroPagoAjax&file=checkPayment&record='+id
    });
  return xhr.transport.responseText;
}