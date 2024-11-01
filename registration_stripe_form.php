<div class="wrap">
<?php  
global $wpdb;	
$paypalquery = "SELECT * FROM wp_pro_registration_stripe_detail";
$paypalvalues = $wpdb->get_results($paypalquery); 	
$stripecharge = $paypalvalues[0]->value;
$s_key = $paypalvalues[1]->value;	
$p_key = $paypalvalues[2]->value;
	
?>
  
  <!-- The required Stripe lib --> 


  <script type="text/javascript">
    // This identifies your website in the createToken call below
	Stripe.setPublishableKey('<?php echo $p_key; ?>');

    var stripeResponseHandler = function(status, response) {
      var $form = jQuery('#payment-form');

      if(response.error) {
        // Show the errors on the form
		//alert(response.error.message);
		$form.find('.payment-errors').text(response.error.message);
        $form.find('button').prop('disabled', false);
      } else {
		  
        // token contains id, last4, and card type
        var token = response.id;
		
		//alert(token);
        // Insert the token into the form so it gets submitted to the server
        $form.append(jQuery('<input type="hidden" name="stripeToken"  value="' + token + '"  />'));
        // and re-submit
        $form.get(0).submit();
      }
    };

    jQuery(function($) {
      jQuery('#payment-form').submit(function(e) {
        var $form = jQuery(this);
		
        // Disable the submit button to prevent repeated clicks
        $form.find('button').prop('disabled', true);
				document.getElementById('pra_loader').style.display='block';

       Stripe.createToken($form, stripeResponseHandler);
        // Prevent the form from submitting with the default action
        return false;
      });
    });
  </script>
  <?php 

// Set the Stripe key:
// Uses STRIPE_PUBLIC_KEY from the config file.
// Check for a form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	

	// Stores errors:
	$errors = array();
	// Need a payment token:
	//echo "Token".$_POST['stripeToken'];
	
	if (isset($_POST['stripeToken'])) {
		
		$token = $_POST['stripeToken'];
		
		// Check for a duplicate submission, just in case:
		// Uses sessions, you could use a cookie instead.
		if (isset($_SESSION['token']) && ($_SESSION['token'] == $token)) {
			$errors['token'] = 'You have apparently resubmitted the form. Please do not do that.';
		} else { // New submission.
			$_SESSION['token'] = $token;
		}		
		
	} 
	
	else {
		$errors['token'] = 'The order cannot be processed. Please make sure you have JavaScript enabled and try again.';
	}
	
	// Set the order amount somehow:
	$amount = 10; // $20, in cents

	// Validate other form data!

	// If no errors, process the order:
	if (empty($errors)) {
		
		// create the charge on Stripe's servers - this will charge the user's card
		try {
			
			// Include the Stripe library:
			require_once('includes/stripe/lib/Stripe.php');

			// set your secret key: remember to change this to your live secret key in production
			// see your keys here https://manage.stripe.com/account

			//Stripe::setApiKey('sk_test_ac2wubywd8NIb5vEAbUXPA5t');
			Stripe::setApiKey($s_key);	  /*secret key*/
			//Stripe::setApiKey('sk_test_7L87zYz1wP03u3etKjJ4gx7e');	  /*secret key*/

			//////////////////////////////////////////////////
							
				$rcharge = $stripecharge*100;
				$charge = Stripe_Charge::create(array(
						  "amount" => $rcharge, // amount in cents, again (1000=$10)
						  "currency" => "usd",
						  "card" => $token,
						  "description" => $_POST['payer_email']
						  )
						);
		    //////////////////////////////////////////////////
				
							
			// Check that it was paid:
			if ($charge->paid == true) {
				// Store the order in the database.
				// Send the email.
		//echo "Celebrate!";
		
		$user_login=sanitize_text_field($_POST['user_login']);
		$first_name=sanitize_text_field($_POST["first_name"]);
	 	$last_name=sanitize_text_field($_POST["last_name"]);
	  	$user_email=sanitize_text_field($_POST['payer_email']);
	 	$user_pass=md5($_POST["user_pass"]);

						
		// insert value in wp_users table
		$insertquery='insert into wp_users (user_login,user_pass,user_nicename,user_email,user_url,user_registered,user_activation_key,user_status,display_name) 
					values ("'.$user_login.'","'.$user_pass.'","'.$first_name.'","'.$user_email.'","",NOW(),"","0","'.$first_name.'")';
		$result=mysql_query($insertquery)or die(mysql_error());
		
		// select userid from wp_users table
		$selectuid='select *  from wp_users where user_login = "'.$user_login.'"';
		$selectuidresult=mysql_query($selectuid)or die(mysql_error());
		$rowuid = mysql_fetch_array($selectuidresult);
		$uid = $rowuid["ID"];
				
				// insert value in wp_usermeta table
				$insertmeta = "insert into wp_usermeta (user_id,meta_key,meta_value)values
				('".$uid."','first_name','".$first_name."'),
				('".$uid."','last_name','".$last_name."'),
				('".$uid."','nickname','".$user_login."'),
				('".$uid."','description',''),
				('".$uid."','rich_editing','true'),
				('".$uid."','comment_shortcuts','false'),
				('".$uid."','admin_color','fresh'),
				('".$uid."','use_ssl',0),
				('".$uid."','show_admin_bar_front','true'),
				('".$uid."','wp_capabilities','a:1:{s:10:\"subscriber\";b:1;}'),
				('".$uid."','wp_user_level',0)";
				$resultmeta = mysql_query($insertmeta)or die(mysql_error());
		
		echo '<h1>Your Registartion is Completed Successfully</h1>'; 
				
			} 
			else 
			{ 
				//Charge was not paid!	
				echo '<div class="alert alert-error"><h4>Payment System Error!</h4>
				Your payment could NOT be processed (i.e., you have not been charged) because the payment system rejected the transaction. You can try again or use another card.
				</div>';
			}
			
		} catch (Stripe_CardError $e) {
		    // Card was declined.
			echo $e_json = $e->getJsonBody();
			echo $err = $e_json['error'];
			echo $errors['stripe'] = $err['message'];
		} catch (Stripe_ApiConnectionError $e) {
		    echo "Network problem, perhaps try again.";
		} catch (Stripe_InvalidRequestError $e) {
		    echo "You screwed up in your programming. Shouldn't happen!";
		} catch (Stripe_ApiError $e) {
		    echo "Stripe's servers are down!";
		} catch (Stripe_CardError $e) {
		    echo "Something else that's not the customer's fault.";
		}

	} // A user form submission error occurred, handled below.
	
} // Form submission.
?>
<script type="text/javascript">
function form_validate()
{
	var e = 0;
	if(isEmpty("user_login", "Please Enter User Name", "status"))
	{
		e++
	}
	if(isEmpty("first_name", "Please Enter Last Name", "err_first_name"))
	{
		e++
	}
	if(isEmpty("last_name", "Please Enter Last Name", "err_last_name"))
	{
		e++
	}
	if(emailcheck("payer_email", "Please Enter Correct Email Id", "err_payer_email"))
	{
		e++
	}
	if(isEmpty("card_no", "Please Enter Your Card Number", "err_user_card"))
	{
		e++
	}
	if(isEmpty("cvc_no", "Please Enter CVC Number", "err_user_cvc"))
	{
		e++
	}
	if(isEmpty("card_month", "Please Enter Expiration Month/Year", "err_user_detail"))
	{
		e++
	}
	if(isEmpty("card_year", "Please Enter Expiration Month/Year", "err_user_detail"))
	{
		e++
	}
	if(isEmpty("user_pass", "Please Enter Your Password", "err_user_pass"))
	{
		e++
	}
	if(isEmpty("con_pass", "Please Enter Your Confirm Password", "err_con_pass"))
	{
		e++
	}
	if(passcheck("user_pass", "Your Password Not Match", "err_pass_match"))
	{
		e++
	}
	
	
	
	if(e > 0)
	{
		//alert("Please fill login details");
		return false
	}
	else
	{
		return true

	}

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
function emailcheck(e, t, n)
{
	var reg=/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
	var r = document.getElementById(e);
	var n = document.getElementById(n);
	
	if(reg.test(r.value) == false)
	{
		n.innerHTML = t;
		return true
	
	}
	else
	{	
		n.innerHTML = "";
		r.focus();
		return false
		
	}
	
}
function passcheck(e, t, n)
{
	var pass = document.getElementById(e).value;
	var co_pass = document.getElementById('con_pass').value;
	var n = document.getElementById(n);
	
	
	if(pass!='' && co_pass!='')
	{
		if(pass==co_pass)
		{
			n.innerHTML = "";
			r.focus();
			return false;	
		}
		else
		{	
			n.innerHTML = t;		
			return true;		
		}
	}
	
	
}

    
</script> 




  <form class="regi_form"  action="" method="POST" id="payment-form">
    <span class="payment-errors"></span>
    <table width="100%" cellspacing="0" cellpadding="0" class="regtable">
      <tbody>
        <tr>
          <td>Username<em>&nbsp;*</em>:</td>
          <td><input type="text" value="" onkeyup="checkname(this.value)"  id="user_login" name="user_login" />
            <label id="status" ></label></td>
        </tr>
        <tr>
          <td>First Name<em>&nbsp;*</em>:</td>
          <td><input type="text"  value="" onkeyup="nospaces(this)" id="first_name" name="first_name" />
            <label id="err_first_name" ></label></td>
        </tr>
        <tr>
          <td>Last Name<em>&nbsp;*</em>:</td>
          <td><input type="text"  value="" id="last_name" name="last_name">
            <label id="err_last_name" ></label></td>
        </tr>
        <tr>
          <td>Email<em>&nbsp;*</em>:</td>
          <td><input type="text" value="" id="payer_email" name="payer_email">
            <label id="err_payer_email" ></label></td>
        </tr>
        <tr>
          <td>Card Number<em>&nbsp;*</em>:</td>
          <td><input id="card_no" type="text" value=""  size="20" data-stripe="number" onkeypress="return isNumber(event)"/>
            <label id="err_user_card" ></label></td>
        </tr>
        <tr>
          <td>CVC<em>&nbsp;*</em>:</td>
          <td><input id="cvc_no" type="text" maxlength="4" value="" size="4" data-stripe="cvc" onkeypress="return isNumber(event)" />
            <label id="err_user_cvc" ></label></td>
        </tr>
        <tr>
          <td>Expiration (MM/YYYY)<em>&nbsp;*</em>:</td>
          <td><input type="text" id="card_month" maxlength="2" value="" size="2" data-stripe="exp-month" onkeypress="return isNumber(event)" />
            <input type="text" id="card_year" maxlength="4" value="" size="4" data-stripe="exp-year" onkeypress="return isNumber(event)" />
            <label id="err_user_detail" ></label></td>
        </tr>
        <tr>
          <td>Password<em>&nbsp;*</em>:</td>
          <td><input type="password" value="" name="user_pass" id="user_pass">
            <label id="err_user_pass" ></label></td>
        </tr>
        <tr>
          <td>Confirm Password<em>&nbsp;*</em>:</td>
          <td><input type="password" value="" name="con_pass" id="con_pass">
            <label id="err_con_pass" ></label>
            <label id="err_pass_match" ></label></td>
        </tr>
        <tr>
          <td>&nbsp; 
            
            <!-- <td><input type="submit" value="Submit" name="submit"  onclick="return form_validate();" /></td>-->
          <td><input  onclick="return form_validate();" value="Submit"  type="submit" /><div id="pra_loader" class="pra_loader"></div></td>
        </tr>
      </tbody>
    </table>
  </form>
</div>
