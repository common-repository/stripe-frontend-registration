<?php global $wpdb;	
if(isset($_POST['submit']))
{	 

	$charge = sanitize_text_field($_POST['charge']);
	$skey = sanitize_text_field($_POST['skey']);	
	$pkey = sanitize_text_field($_POST['pkey']);	
	
	$wpdb->update( 
	'wp_pro_registration_stripe_detail', 
			array( 
				'value' => $charge
			), 
			array( 'id' => 1 ), 
			array( 
				'%d'	// value2
			), 
			array( '%d' ) 
			);
	
	$wpdb->update( 
	'wp_pro_registration_stripe_detail', 
			array( 
				'value' =>  $skey
			), 
			array( 'id' => 3 ), 
			array( 
				'%s'	// value2
			), 
			array( '%s' ) 
			);
			
	$wpdb->update( 
	'wp_pro_registration_stripe_detail', 
			array( 
				'value' =>  $pkey
			), 
			array( 'id' => 4 ), 
			array( 
				'%s'	// value2
			), 
			array( '%s' ) 
			);
			
			
			
			echo '<div class="wrap">
      <div class="updated" style="background-color:#7AD03A;">
        <p><strong style="color:#FFF;" >Details are updated successfully.
        </strong> </p>
      </div>
    </div>';
			
}
?>

<script type="text/javascript">
function checkform()
{
	
	
	var e = 0;
	if(isEmpty("charge", "Please Enter Charge for Registration.", "err_charge"))
	{
		e++
	}
	if(isEmpty("skey", "Please Enter Secret Key.", "err_skey"))
	{
		e++
	}
	if(isEmpty("pkey", "Please Enter Publishable Key.", "err_pkey"))
	{
		e++
	}
	
	
	if(e > 0)
	{
		//alert("Please fill login details");
		return false;
	}
	else
	{
			return true;
	}
function isEmpty(e, t, n)
{
		var r = document.getElementById(e);
		var n = document.getElementById(n);
		if(r.value.replace(/\s+$/, "") == "")
		{
			n.innerHTML = t;
			r.value = "";
			r.focus();
			return true
		}
		else
		{
			n.innerHTML = "";
			return false
		}
}

	
		

}
function isNumber(evt) 
				{
					evt = (evt) ? evt : window.event;
					var charCode = (evt.which) ? evt.which : evt.keyCode;
					/*if (charCode == 32) 
					{
						return false;
					}*/
					if (charCode > 31 && (charCode < 48 || charCode > 57)) 
					{
						return false;
					}
					return true;
				}
</script>
<style>

.postbox label {  color:#F00; font-size:14px; }
</style>
<div id="wpbody">
  <div tabindex="0" aria-label="Main content"  style="overflow: hidden;">
    <div id="execphp-message"></div>
    <div class="wrap">
      <div class="updated fade">
        <p><strong>Stripe Details
        </strong> </p>
      </div>
    </div>
    <div class="clear"></div>
    <div class="wrap">
      <div class="updated" style=" background-color: #0074A2;">
        <p><strong style="color:#FFF;">Short code for Registration Form : <input type="text" value="[stripe_form]" style=" border-radius: 5px; width: 200px;" readonly="readonly" >
        </strong> </p>
      </div>
    </div>
  </div>
  
  <!-- wpbody-content -->
  <div class="clear"></div>
  <?php $myrows = $wpdb->get_col("SELECT value FROM wp_pro_registration_stripe_detail" ); 
  
  
  ?>
  <div  class="postbox">
  <form action="" method="post" >
    <table id="misc-publishing-actions " class="misc-pub-section" cellpadding="0" cellspacing="0">
      <tr>
        <td  class="misc-pub-section" >Registration Charge : </td>
      </tr>
      <tr>
        <td class="misc-pub-section"><input id="charge" type="text" name="charge" value="<?php echo $myrows[0]; ?>" onkeypress="return isNumber(event)" /><b>$</b> <label id="err_charge" ></label></td>
      </tr>
      <tr >
        <td class="misc-pub-section">( Test/Live ) Secret Key: <a target="_blank" href="https://support.stripe.com/questions/where-do-i-find-my-api-keys">Where do i find this ?</a></td>
      </tr>
      <tr>
        <td class="misc-pub-section"><input size="30" type="text" id="skey" name="skey" value="<?php echo $myrows[1]; ?>" /> <label id="err_skey" ></label></td>
      </tr>
       <tr >
        <td class="misc-pub-section">( Test/Live ) Publishable Key: <a target="_blank" href="https://support.stripe.com/questions/where-do-i-find-my-api-keys">Where do i find this ?</a></td>
      </tr>
      <tr>
        <td class="misc-pub-section"><input size="30" type="text" id="pkey" name="pkey" value="<?php echo $myrows[2]; ?>" /> <label id="err_pkey" ></label></td>
      </tr>
      
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td class="misc-pub-section"><input type="submit" class="button button-primary button-large"  name="submit" value="submit" onclick="return checkform();" /></td>
      </tr>
    </table>
  </form>
  </div>
</div>
<div class="wrap">
      <div class="updated" style=" background-color: #0074A2;">
        <p><strong style="color:#FFF;">Short code for Registration Form : <input type="text" value="[stripe_form]" style=" border-radius: 5px; width: 200px;" readonly="readonly" >
        </strong> </p>
      </div>
    </div>
