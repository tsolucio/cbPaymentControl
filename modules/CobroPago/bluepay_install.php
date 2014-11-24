<?php

$moduleTitle="TSolucio::vtiger CRM CobroPago";

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
echo "<html><head><title>vtlib $moduleTitle</title>";
echo '<style type="text/css">@import url("themes/softed/style.css");br { display: block; margin: 2px; }</style>';
echo '</head><body class=small style="font-size: 12px; margin: 2px; padding: 2px; background-color:#f7fff3; ">';
echo '<table width=100% border=0><tr><td align=left>';
echo '<a href="index.php"><img src="themes/softed/images/vtiger-crm.gif" alt="vtiger CRM" title="vtiger CRM" border=0></a>';
echo '</td><td align=center style="background-image: url(\'vtlogowmg.png\'); background-repeat: no-repeat; background-position: center;">';
echo "<b><H1>$moduleTitle</H1></b>";
echo '</td><td align=right>';
echo '<a href="www.vtiger-spain.com"><img src="vtspain.gif" alt="vtiger-spain" title="vtiger-spain" border=0 height=100></a>';
echo '</td></tr></table>';
echo '<hr style="height: 1px">';

// Turn on debugging level
$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Module.php');

$modAccounts = Vtiger_Module::getInstance('Accounts');
$modCobroPago = Vtiger_Module::getInstance('CobroPago');

//********************************* Contacts

$block = VTiger_Block::getInstance('LBL_ACCOUNT_INFORMATION', $modAccounts);

$field = new Vtiger_Field();
$field->name = 'token';
$field->uitype = 1;
$field->displaytype = 2;
$block->addField($field);

$modAccounts->addLink(
'DETAILVIEWBASIC',
'Get Bluepay Token',
'index.php?module=CobroPago&action=bluepay&src_module=$MODULE$&src_record=$RECORD$'
);

?>