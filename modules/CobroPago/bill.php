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
  global $mod_strings;
  $id = $_REQUEST['record'];
  
  $SQL = "SELECT update_log FROM vtiger_cobropago WHERE cobropagoid=?";
  $result = $adb->pquery($SQL,array($id));
  $update_log = $adb->query_result($result,0,'update_log');

  $update_log .= $mod_strings['RecieptOpen'].$current_user->user_name.$mod_strings['PaidOn'].date("l dS F Y h:i:s A").'--//--';
  $SQL_UPD = "UPDATE vtiger_cobropago SET update_log=? WHERE cobropagoid=?";
  $adb->pquery($SQL_UPD,array($update_log,$id));
?>
<html>
<head>
</head>
<body>
  <script>
  window.open("index.php?module=CobroPago&action=CobroPagoAjax&file=recibo&record=<?=$id?>");
  window.history.back();
  </script>
</body>
</html>
