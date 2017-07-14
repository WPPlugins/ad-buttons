<?php 
global $wpdb;
$widget_adbuttons_cfg = array(

	'ab_cfg1'	=> ''

);

$widget_adbuttons_cfg = get_option('widget_adbuttons_cfg');

$ol_flash 	= '';
$htp 		= "http://";
$ab_img 	= $htp;
$ab_link	= $htp;
$ab_img_err	= '';
$ab_link_err= '';

if ( 
    ! isset( $_POST['ab-create-nonce'] ) 
    || ! wp_verify_nonce( $_POST['ab-create-nonce'], 'ab-create' ) 
) {

   print 'Sorry, your nonce did not verify.';
   exit;

} else {
   
// check if the form has been submitted and validate input
if(isset($_POST['ab_img']) || isset($_POST['ab_link']) || isset($_POST['ab_txt'])) {
				if (isset($_POST['ab_img'])) { 
					$ab_img = $htp.str_replace($htp, "", wp_specialchars_decode($_POST['ab_img']));
				}

				if (isset($_POST['ab_link'])) { 
					$ab_link = $htp.str_replace($htp, "", wp_specialchars_decode($_POST['ab_link']));
				}

				if (isset($_POST['ab_txt'])) { 
					$ab_txt = wp_specialchars_decode($_POST['ab_txt']);

				}
		if($ab_img == $htp || $ab_img == ''){
			$ab_img_err = 'Please fill in the link to your image filex';
		}
		if($ab_link == $htp || $ab_link == ''){
			$ab_link_err = 'Please fill in the target link for your ad';
		}
	if($ab_img_err == '' && $ab_link_err == ''){
		// everything looks good, lets write to the database
		
		$table = $wpdb->prefix."ad_buttons";
		$wpdb->query("INSERT INTO $table(ad_picture, ad_link, ad_text, ad_active)
        VALUES('$ab_img', '$ab_link', '$ab_txt', 0)");
		$ol_flash = 'Your Ad Button has been created!';
		$ab_img 	= $htp;
		$ab_link	= $htp;
		$ab_txt	= '';
		$ab_img_err	= '';
		$ab_link_err= '';
	}

}
}

if ($ol_flash != '') echo '<div id="message"class="updated fade"><p>' . $ol_flash . '</p></div>'; ?>
<div class="wrap">

<h2>Create new Ad Button</h2>
<p><form method="post">
<?php wp_nonce_field('ab-create', 'ab-create-nonce');
$widget_adbuttons_cfg = get_option('widget_adbuttons_cfg')
?>

<table class="form-table">

<tr valign="top">
<th scope="row">Ad Button Image </th>
<td><input type="text" name="ab_img" value="<?php echo $ab_img; ?>" /> <?php if($ab_img_err)echo"$ab_img_err"; ?></td>
</tr>

<tr valign="top">
<th scope="row">Ad Button Link </th>
<td><input type="text" name="ab_link" value="<?php echo $ab_link; ?>" /> <?php if($ab_link_err)echo"$ab_link_err"; ?></td>
</tr>
<tr valign="top">
<th scope="row">Ad Button Text </th>
<td><input type="text" name="ab_txt" value="<?php echo $ab_txt; ?>" /></td>
</tr>
</table>
<p class="submit">
<input type="submit" name="Submit" value="Create Ad Button" />
</p>

</form></p>

</div>


