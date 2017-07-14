<?php 
    global $wpdb;

	if(isset($_POST['ab_blog_topic'])) {
		if( $_POST['ab_blog_topic'] <> ''){
			$user_agent = 'WP_AB_Plugin_'.get_option('ad_buttons_version').' OnSite: '.site_url();
			ini_set('user_agent', $user_agent);
			// see if allow_url_fopen is enabled
					if (ini_get('allow_url_fopen')) {
						$handle = fopen("http://www.blogio.net/subm.php?".$_POST['ab_blog_topic'], 'r'); 
						$result = fread($handle,8192);
					} else {
					   // allow_url_fopen is disabled see if CURL is available
						if (function_exists('curl_init')) {
							// initialize a new curl resource
							$ch = curl_init();

							// set the url to fetch
							//curl_setopt($ch, CURLOPT_URL, 'http://1.wp-adbuttons.appspot.com/hello.php');
							curl_setopt($ch, CURLOPT_URL, 'http://www.blogio.net/subm.php?'.$_POST['ab_blog_topic']);

							// don't give me the headers just the content
							curl_setopt($ch, CURLOPT_HEADER, 0);

							// return the value instead of printing the response to browser
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

							// use a user agent to mimic a browser
							curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);

							$content = curl_exec($ch);

							// remember to always close the session and free all resources
							curl_close($ch);
							} 
					} 
			update_option("ad_buttons_network_requested", "yes");
			$ol_flash = "your request has been submitted...";
		}
	}
?>
<div class="wrap">
<h2>Ad Buttons Network </h2>
<br/>
<?php 
if ($ol_flash != '') echo '<div id="message"class="updated fade"><p>' . $ol_flash . '</p></div>';
if(get_option('ad_buttons_network_requested') == 'yes'){

 
?>
<p>
Thank you for your interrest in the Ad Buttons Ad Network! You will be contacted with details on how to activate the Ad Buttons Ad Network ads when we have added your site to our advertising pool 
</p>
<?php
}else{
?>
<p>
Sign up now for the Ad Buttons Ad Network and get your blog advertised for free!
</p>
<p>
If you participate in the Ad Buttons Ad Network, ads from other blogs with the same topic as yours will be shown on your blog. 
For each ad you show on your site, your ad will be shown on someone else's site. The Ad Buttons Ad Network ads will be shown 
alongside of your own ads shown with the Ad Buttons plugin. We don't want to fill your site with ads, so only a single Ad Buttons Ad Network 
125 x 125 pixel ad will be shown on each page. You decide how many of your own ads you want to show per page with the "Number of Ad Buttons 
to display in the sidebar widget" setting on the Ad Buttons Settings page. 
</p>
<table class="form-table">
<form method="post">
<tr valign="top">
<th scope="row">Blog topic </th>
<td><input name="ab_blog_topic" type="text" size="25" maxlength="25"></td>
<td>in a single word tell us what the main topic of your blog is</td>
</tr>
<tr>
<td colspan="3">
<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Submit') ?>" />
</p>
</td></tr>
</form>
</table>
<?php
}
?>
</div>