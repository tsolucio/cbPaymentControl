<table width="100%" cellpadding="2" cellspacing="0" border="0">
<tr>
	<td>

		{include file='Buttons_List1.tpl'}
		
<!-- Contents -->
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
<tr>
	<td valign=top><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
	<td class="showPanelBg" valign=top width=100%>
		<!-- PUBLIC CONTENTS STARTS-->
		<div class="small" style="padding:10px" >
		
		<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
			<tr><td>		
		  		{* Module Record numbering, used MOD_SEQ_ID instead of ID *}
		  		{assign var="USE_ID_VALUE" value=$MOD_SEQ_ID}
		  		{if $USE_ID_VALUE eq ''} {assign var="USE_ID_VALUE" value=$ID} {/if}
		 		<span class="dvHeaderText">[ {$USE_ID_VALUE} ] {$NAME} -  {$SINGLE_MOD|@getTranslatedString:$MODULE} {$MOD.LBL_CARD_INFORMATION}</span>&nbsp;&nbsp;&nbsp;<span class="small">{$UPDATEINFO}</span>&nbsp;<span id="vtbusy_info" style="display:none;" valign="bottom"><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span><span id="vtbusy_info" style="visibility:hidden;" valign="bottom"><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
		 	</td></tr>
		 </table>			 
		<br>
		{if $MESSAGE ne ''}
		<p style="color:red">{$MESSAGE}</p>
		{/if}
		<div style="margin:0 3em;">
      <form action="index.php" method="post">
        <input type="hidden" name="module" value="CobroPago">
        <input type="hidden" name="action" value="bluepay">
        <input type="hidden" name="src_module" value="{$MODULE}">
        <input type="hidden" name="src_record" value="{$ID}">
        <p>
          <label><input type="radio" name="payment_type" value="card" checked onclick="document.getElementById('div-card').style.display='block';document.getElementById('div-ach').style.display='none';">Card</label>
          <label><input type="radio" name="payment_type" value="ach" onclick="document.getElementById('div-card').style.display='none';document.getElementById('div-ach').style.display='block';">ACH</label>
        </p>
        <div id="div-card">
          <p>
            <label style="display:block">{$MOD.CARD_ACCOUNT}</label>
            <input name="card_number" value="{$card_number}">
          </p>
          <p>
            <label style="display:block">{$MOD.CARD_CVV}</label>
            <input name="card_cvv" value="{$card_cvv}" size="4">
          </p>
          <p>
            <label style="display:block">{$MOD.CARD_EXPIRY_DATE}</label>
            <input name="card_expiry" value="{$card_expiry}" size="4">
          </p>
          <p>
            <input type="submit" value="{$MOD.CARD_SUBMIT}">
          </p>
        </div>
        <div id="div-ach" style="display:none;">
          <p>
            <label style="display:block">{$MOD.ACH_ACCOUNT_TYPE}</label>
            <select name="ach_acc_type">
              <option value="C">{$MOD.ACH_ACCOUNT_TYPE_C}</option>
              <option value="S">{$MOD.ACH_ACCOUNT_TYPE_S}</option>
            </select>
          </p>
          <p>
            <label style="display:block">{$MOD.ACH_ACCOUNT_NUMBER}</label>
            <input name="ach_acc_number" value="{$ach_acc_number}">
          </p>
          <p>
            <label style="display:block">{$MOD.ACH_ROUTING_NUMBER}</label>
            <input name="ach_routing_number" value="{$ach_routing_number}">
          </p>
          <p>
            <input type="submit" value="{$MOD.ACH_SUBMIT}">
          </p>
        </div>
      </form>
    </div>
  </td>
</tr>
</table>