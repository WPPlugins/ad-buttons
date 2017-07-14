<?php  
/*
Plugin Name: Ad Buttons
Plugin URI: http://blogio.net/blog/wp-ad-plugin/
Description: Plugin to add ad buttons to your blog
Author: Nico
Version: 2.3.2
Author URI: http://www.blogio.net/blog/
Questions, sugestions, problems? Let me know at nico@blogio.net
*/


function ad_buttons_install()
{
	//set the options
	$newoptions = get_option('widget_adbuttons_cfg');
	$newoptions['ab_dspcnt'] = '1';
	$newoptions['ab_target'] = 'bnk';
	$newoptions['ab_powered'] = '1';
	$newoptions['ab_count'] = '1';
	add_option('widget_adbuttons_cfg', $newoptions);
	
	// create table
    global $wpdb;
    $table = $wpdb->prefix."ad_buttons";
    $structure = "CREATE TABLE $table (
        id INT(9) NOT NULL AUTO_INCREMENT,
        ad_picture VARCHAR(100) NOT NULL,
        ad_link VARCHAR(500) NOT NULL,
		ad_text VARCHAR(80) NOT NULL,
        ad_strdat DATE NOT NULL,
		ad_enddat DATE NOT NULL,
        ad_views INT(9) DEFAULT 0,
		ad_clicks INT(9) DEFAULT 0,
		ad_active TINYINT(1) NOT NULL DEFAULT 0,
        adg_count VARCHAR(500) NOT NULL,
        adg_show tinytext NOT NULL,
		ad_pos INT(9) DEFAULT 0,
		
	UNIQUE KEY id (id)
    );";
	
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($structure);
	
	$ab_plugindir = get_option('siteurl').'/'.PLUGINDIR.'/'.dirname(plugin_basename(__FILE__));
	
    $wpdb->query("INSERT INTO $table(id, ad_picture, ad_link, ad_text, ad_views, ad_clicks, ad_active)
        VALUES(1, '".$ab_plugindir."/ab125.jpg', 'http://wordpress.org/plugins/ad-buttons/', 'ads powered by Ad Buttons', 1, 0, 1)");

    $table = $wpdb->prefix."ad_buttons_stats";
    $structure = "CREATE TABLE $table (
  	abs_dat date NOT NULL,
  	abs_ip int(10) NOT NULL,
  	abs_view tinyint(4) NOT NULL,
  	abs_click tinyint(4) NOT NULL,
	KEY abs_dat (abs_dat)
    );";
	
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($structure);

	$table = $wpdb->prefix."ad_buttons_stats_hst";
	$structure = "CREATE TABLE $table (
  	abs_dat date NOT NULL,
    abs_view int(11) NOT NULL,
    abs_click int(11) NOT NULL
    );";
	
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($structure);
	
	$ad_buttons_version = "2.3.2";
	update_option("ad_buttons_version", $ad_buttons_version);

	$ad_buttons_db_version = "2.3.2";
	update_option("ad_buttons_db_version", $ad_buttons_db_version);
	
	$ip2nation_db_version = "2.3.1";
	update_option("ip2nation_db_available", $ip2nation_db_version);
}

register_activation_hook(__FILE__,'ad_buttons_install');

$ad_buttons_db_version = get_option("ad_buttons_db_version");

if($ad_buttons_db_version <> "2.3.2"){
	// update database
	
	//set the options
	$newoptions = get_option('widget_adbuttons_cfg');
	$newoptions['ab_dspcnt'] = '1';
	$newoptions['ab_target'] = 'bnk';
	$newoptions['ab_powered'] = '1';
	$newoptions['ab_count'] = '1';
	add_option('widget_adbuttons_cfg', $newoptions);
	
	// create table
    global $wpdb;
    $table = $wpdb->prefix."ad_buttons";
    $structure = "CREATE TABLE $table (
        id INT(9) NOT NULL AUTO_INCREMENT,
        ad_picture VARCHAR(100) NOT NULL,
        ad_link VARCHAR(500) NOT NULL,
		ad_text VARCHAR(80) NOT NULL,
        ad_strdat DATE NOT NULL,
		ad_enddat DATE NOT NULL,
        ad_views INT(9) DEFAULT 0,
		ad_clicks INT(9) DEFAULT 0,
		ad_active TINYINT(1) NOT NULL DEFAULT 0,
        adg_count VARCHAR(500) NOT NULL,
        adg_show tinytext NOT NULL,
		ad_pos INT(9) DEFAULT 0,
		
	UNIQUE KEY id (id)
    );";
	
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($structure);
	
	$ab_plugindir = get_option('siteurl').'/'.PLUGINDIR.'/'.dirname(plugin_basename(__FILE__));
	
    $wpdb->query("INSERT INTO $table(id, ad_picture, ad_link, ad_text, ad_views, ad_clicks, ad_active)
        VALUES(1, '".$ab_plugindir."/ab125.jpg', 'http://wordpress.org/plugins/ad-buttons/', 'ads powered by Ad Buttons', 1, 0, 1)");

    $table = $wpdb->prefix."ad_buttons_stats";
    $structure = "CREATE TABLE $table (
  	abs_dat date NOT NULL,
  	abs_ip int(10) NOT NULL,
  	abs_view tinyint(4) NOT NULL,
  	abs_click tinyint(4) NOT NULL,
	KEY abs_dat (abs_dat)
    );";
	
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($structure);

	$table = $wpdb->prefix."ad_buttons_stats_hst";
	$structure = "CREATE TABLE $table (
  	abs_dat date NOT NULL,
    abs_view int(11) NOT NULL,
    abs_click int(11) NOT NULL
    );";
	
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($structure);
	
	$ad_buttons_version = "2.3.2";
	update_option("ad_buttons_version", $ad_buttons_version);

	$ad_buttons_db_version = "2.3.2";
	update_option("ad_buttons_db_version", $ad_buttons_db_version);
	
	$ip2nation_db_version = "2.3.1";
	update_option("ip2nation_db_available", $ip2nation_db_version);
}

   //check if user is a bot of some sort
function is_bot()
{
    $bots = array('google','yahoo','msn','jeeves','lycos','whatuseek','BSDSeek','BullsEye','Yandex',
	'Seznam','XoviBot','NerdyBot','MJ12bot','bingbot','spider', 'crawler','eniro.com','ApptusBot','scraper','validator');
    //takes the list above and returns (google)|(yahoo)|(msn)...
    $regex = '('.implode($bots, ')|(').')';
    //uses the generated regex above to see if those keywords are contained in the user agent variable    
    return eregi($regex, $_SERVER['HTTP_USER_AGENT']);
}

function ab_show_ad($ad_id)
{
	global $wpdb;
	global $ab_geot;
	// check if geo targeting has been enabled
	if(!$ab_geot) return(1);
	// check if this button has geo targeting information stored at all
	$results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ad_buttons WHERE id = $ad_id LIMIT 1");
	foreach($results as $result){
		$adg_count 	= $result->adg_count;
		$adg_show 	= $result->adg_show;
	}
	if(!$adg_count) return(1);
	if(!$adg_show) return(1);
	
	
	$sql = 'SELECT country FROM ip2nation WHERE ip < INET_ATON("'.$_SERVER['REMOTE_ADDR'].'") 
	        ORDER BY ip DESC LIMIT 0,1';
	
	list($country) = mysql_fetch_row(mysql_query($sql));

	if(stristr($adg_count, $country) === FALSE) {
		if($adg_show == 's'){
			return(0);
		} else {
			return(1);
		}
	} else {
		if($adg_show == 's'){
			return(1);
		} else {
			return(0);
		}
	}
}

function ad_buttons()
{
    global $wpdb;
	global $ab_geot;
$widget_adbuttons_cfg = array(

	'ab_title'				=> '',
	'ab_dspcnt'				=> '',
	'ab_target' 			=> '',
	'ab_adsense' 			=> '',
	'ab_adsense_fixed'		=> '',
	'ab_adsense_pos'		=> '',
	'ab_adsense_pubid'		=> '',
	'ab_adsense_channel'	=> '',
	'ab_adsense_corners'	=> '',
	'ab_adsense_col_border'	=> '',
	'ab_adsense_col_title'	=> '',
	'ab_adsense_col_bg'		=> '',
	'ab_adsense_col_txt'	=> '',
	'ab_adsense_col_url'	=> '',
	'ab_nocss'				=> '',
	'ab_width'				=> '',
	'ab_padding'			=> '',
	'ab_nofollow'			=> '',
	'ab_powered'			=> '',
	'ab_yah'				=> '',	
	'ab_yourad'				=> '',
	'ab_geot'				=> '',
	'ab_yaht'				=> '',
	'ab_yahurl'				=> '',
	'ab_anet'				=> '',
	'ab_anetu'				=> '',
	'ab_anett'				=> '',
	'ab_fix'				=> '',
	'ab_count'				=> ''
	

);

$widget_adbuttons_cfg = get_option('widget_adbuttons_cfg');
$wp_root = get_option('home');

$ab_geot = $widget_adbuttons_cfg['ab_geot'];

if($widget_adbuttons_cfg['ab_nofollow']){
	$ab_nofollow = ' rel="nofollow" ';
}

if($widget_adbuttons_cfg['ab_powered']){
	if($widget_adbuttons_cfg['ab_nocss']){
		$ab_powered = '<a class="ab_power" href="http://wordpress.org/plugins/ad-buttons/">powered by Ad Buttons</a>';
	} else {
		$ab_powered = '<div id="ab_power"><a class="ab_power" href="http://wordpress.org/plugins/ad-buttons/">powered by Ad Buttons</a></div>';	
	}
}

if($widget_adbuttons_cfg['ab_adsense']){
	if($widget_adbuttons_cfg['ab_nocss']){
		$ab_adsensecss = '';
		$ab_adsenseenddiv = '';
	}else{
		$ab_adsensecss = '<div id="ab_adsense">';
		$ab_adsenseenddiv = '</div>';
	}	
	$ab_adsense_ad = $ab_adsensecss.'
	<script type="text/javascript"><!--
google_ad_client = "'.$widget_adbuttons_cfg['ab_adsense_pubid'].'";
google_ad_width = 125;
google_ad_height = 125;
google_ad_format = "125x125_as";
google_ad_type = "text_image";
google_ad_channel = "'.$widget_adbuttons_cfg['ab_adsense_channel'].'";
google_color_border = "'.$widget_adbuttons_cfg['ab_adsense_col_border'].'";
google_color_bg = "'.$widget_adbuttons_cfg['ab_adsense_col_bg'].'";
google_color_link = "'.$widget_adbuttons_cfg['ab_adsense_col_title'].'";
google_color_text = "'.$widget_adbuttons_cfg['ab_adsense_col_txt'].'";
google_color_url = "'.$widget_adbuttons_cfg['ab_adsense_col_url'].'";
google_ui_features = "'.$widget_adbuttons_cfg['ab_adsense_corners'].'";
//-->
</script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>'.$ab_adsenseenddiv;
	}
	
if($widget_adbuttons_cfg['ab_target'] == 'bnk'){
	$target = " target=\"_blank\" ";
	}
elseif($widget_adbuttons_cfg['ab_target'] == 'top'){
	$target = " target=\"_top\" ";
	}
elseif($widget_adbuttons_cfg['ab_target'] == 'non'){
	$target = " ";
	}


if($widget_adbuttons_cfg['ab_adsense']){
	$ab_count = 1;
	}
else {
	$ab_count = 0;
	}
	
echo'
<style type="text/css">
#ab_adblock
{
width: '.$widget_adbuttons_cfg['ab_width'].'px;
padding:'.$widget_adbuttons_cfg['ab_padding'].'px;
overflow:hidden;
}
#ab_adblock a
{
float: left;
padding:'.$widget_adbuttons_cfg['ab_padding'].'px;
}
#ab_adsense
{
float: left;
padding:'.$widget_adbuttons_cfg['ab_padding'].'px;
}
#ab_clear
{
clear: both;
}
#ab_power, a.ab_power:link, a.ab_power:visited, a.ab_power:hover 
{
width: 150px;
color: #333;
text-decoration:none;
font-size: 10px;
}

</style>'; 
if(!$widget_adbuttons_cfg['ab_nocss']){
	echo '<div id="ab_adblock">';
}

if($widget_adbuttons_cfg['ab_fix']){
	$results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ad_buttons WHERE 
	ad_active = 1 AND ad_strdat <= CURDATE() AND ad_enddat > CURDATE() OR 
	ad_active = 1 AND ad_strdat <= CURDATE() AND ad_enddat = '0000-00-00' ORDER BY ad_pos");
}else{
	$results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ad_buttons WHERE 
	ad_active = 1 AND ad_strdat <= CURDATE() AND ad_enddat > CURDATE() OR 
	ad_active = 1 AND ad_strdat <= CURDATE() AND ad_enddat = '0000-00-00' ORDER BY RAND()");
}
foreach($results as $result){
	if ($ab_count < $widget_adbuttons_cfg['ab_dspcnt']) {
		if($widget_adbuttons_cfg['ab_adsense']){
			if($widget_adbuttons_cfg['ab_adsense_pos']==$ab_count){
				echo $ab_adsense_ad;
			}
		}
		if(ab_show_ad($result->id)) {
			echo"<a href=\"$wp_root/index.php?recommends=$result->id\" $target title=\"$result->ad_text\" $ab_nofollow><img src=\"$result->ad_picture\" alt=\"$result->ad_text\"  vspace=\"1\" hspace=\"1\" border=\"0\"></a>";
			$ab_count = $ab_count + 1;
			// update view counter on the ad button
			
			
			if(!is_bot()) {
				if($widget_adbuttons_cfg['ab_count'] OR !is_user_logged_in()){
					$wpdb->query("UPDATE ".$wpdb->prefix."ad_buttons 
						SET ad_views = ad_views + 1 WHERE id = ".$result->id);
					$ab_ip = ip2long($_SERVER['REMOTE_ADDR']);
					$wpdb->query("INSERT INTO ".$wpdb->prefix."ad_buttons_stats(abs_dat, abs_ip, abs_view)
					VALUES(CURDATE(), '$ab_ip', ".$result->id.")");
				}
			}
		}
	}
}
if($widget_adbuttons_cfg['ab_adsense']){
	if($widget_adbuttons_cfg['ab_adsense_pos']==$ab_count){
		echo $ab_adsense_ad;
		}
	}

if($widget_adbuttons_cfg['ab_anet']){
	$length = 10; 
	$chars = 'abcdefghijklmnoqrstuvwxyz1234567890';
    // Length of character list
    $chars_length = (strlen($chars) - 1);
    // Start our string
    $string = $chars{rand(0, $chars_length)};
    // Generate random string
    for ($i = 1; $i < $length; $i = strlen($string))
    {
        // Grab a random character from our list
        $r = $chars{rand(0, $chars_length)};
        // Make sure the same two characters don't appear next to each other
        if ($r != $string{$i - 1}) $string .=  $r;
   }
   
$string = $string.$widget_adbuttons_cfg['ab_anetu'];
if ($widget_adbuttons_cfg['ab_anett']){
	$string = $string.'t'.$widget_adbuttons_cfg['ab_anett'];
}

	echo'<a href="http://www.adbuttons.net/click/'.$string.'/" ><img src="http://www.adbuttons.net/ad/'.$string.'/" alt=""></a>';
}


if($widget_adbuttons_cfg['ab_yah']){
	$ab_plugindir = get_option('siteurl').'/'.PLUGINDIR.'/'.dirname(plugin_basename(__FILE__));
	if($widget_adbuttons_cfg['ab_yaht'] == 'url'){
		echo'<a href="'.$widget_adbuttons_cfg['ab_yahurl'].'" title="Advertise here"><img src="'.$ab_plugindir.'/your_ad_here.jpg" alt="Advertise here"></a>';
	} else {
		echo'<a href="'.$wp_root.'/?page_id='.$widget_adbuttons_cfg['ab_yourad'].'" title="Advertise here"><img src="'.$ab_plugindir.'/your_ad_here.jpg" alt="Advertise here"></a>';
	}
}

if($widget_adbuttons_cfg['ab_nocss']){
	echo $ab_powered;
}else{
	echo ''.$ab_powered.'</div>';
	}

}

function ad_buttons_settings()
{
    global $wpdb;
    include 'adbuttonsadmin.php';
}
 
function ad_buttons_stats()
{
    global $wpdb;
    include 'adbuttonsstats.php';
}

function ad_buttons_test_gae()
{
    global $wpdb;
    include 'adbuttonstestgae.php';
}

function ad_buttons_top()
{
    global $wpdb;
    include 'adbuttonstop.php';
}
 
function ad_buttons_act()
{
    global $wpdb;
    include 'adbuttonsact.php';
}

function ad_buttons_stats_actions()
{
	
	add_menu_page('Ad Buttons', 'Ad Buttons', 9, __FILE__, 'ad_buttons_act', get_option('siteurl').'/'.PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)).'/ad_buttons_icon.png');
	// Add a submenu to the custom top-level menu:
	add_submenu_page(__FILE__, 'Ad Buttons Settings', 'Settings', 9, 'ad-buttons-settings', 'ad_buttons_settings');
	add_submenu_page(__FILE__, 'Ad Buttons Stats', 'Stats', 9, 'ad-buttons-stats', 'ad_buttons_stats');
	//add_submenu_page(__FILE__, 'Ad Buttons Test', 'Ad Network', 9, 'ad-buttons-test', 'ad_buttons_test_gae');
	// see if allow_url_fopen is enabled
//	if (ini_get('allow_url_fopen')) {
//		add_submenu_page(__FILE__, 'Ad Buttons Network', 'Ad Network', 9, 'ad-buttons-network', 'ad_buttons_test_gae');
//	} else {
//		// allow_url_fopen is disabled see if CURL can be used...
//		if (function_exists('curl_init')) {
//			add_submenu_page(__FILE__, 'Ad Buttons Network', 'Ad Network', 9, 'ad-buttons-network', 'ad_buttons_test_gae');
//		}
//	}
}

add_action('admin_menu', 'ad_buttons_stats_actions');


// process ad clicks
function adbuttons_getclick()
{
global $wpdb;

$widget_adbuttons_cfg = array(

	'ab_title'				=> '',
	'ab_dspcnt'				=> '',
	'ab_target' 			=> '',
	'ab_adsense' 			=> '',
	'ab_adsense_fixed'		=> '',
	'ab_adsense_pos'		=> '',
	'ab_adsense_pubid'		=> '',
	'ab_adsense_channel'	=> '',
	'ab_adsense_corners'	=> '',
	'ab_adsense_col_border'	=> '',
	'ab_adsense_col_title'	=> '',
	'ab_adsense_col_bg'		=> '',
	'ab_adsense_col_txt'	=> '',
	'ab_adsense_col_url'	=> '',
	'ab_nocss'				=> '',
	'ab_width'				=> '',
	'ab_padding'			=> '',
	'ab_nofollow'			=> '',
	'ab_powered'			=> '',
	'ab_yah'				=> '',	
	'ab_yourad'				=> '',
	'ab_geot'				=> '',
	'ab_yaht'				=> '',
	'ab_yahurl'				=> '',
	'ab_anet'				=> '',
	'ab_anetu'				=> '',
	'ab_anett'				=> '',
	'ab_fix'				=> '',
	'ab_count'				=> ''
);

$widget_adbuttons_cfg = get_option('widget_adbuttons_cfg');





	if(isset($_GET['recommends'])) {
		$ad_id = $_GET['recommends'];
		if(is_numeric($ad_id)){
			$results = $wpdb->get_results("SELECT ad_link FROM ".$wpdb->prefix."ad_buttons WHERE id = $ad_id LIMIT 1");
			foreach($results as $result){
				$send_to_url = $result->ad_link;
				
				if(!is_bot()) {
					if($widget_adbuttons_cfg['ab_count'] OR !is_user_logged_in()){
						$wpdb->query("UPDATE ".$wpdb->prefix."ad_buttons 
							SET ad_clicks = ad_clicks + 1 WHERE id = ".$ad_id);
						$ab_ip = ip2long($_SERVER['REMOTE_ADDR']);
						$wpdb->query("INSERT INTO ".$wpdb->prefix."ad_buttons_stats(abs_dat, abs_ip, abs_click)
						VALUES(CURDATE(), '$ab_ip', ".$ad_id.")");	
					}
				}
				//redirect
				header("Location: ".$send_to_url);
				exit(0);
			}
		}

	}
}
	
// widget
function widget_init_adbuttons_widget() {
	// Check for required functions
	if (!function_exists('register_sidebar_widget'))
		return;

	function adbuttons_widget($args){
	    extract($args);
		$options = get_option('widget_adbuttons_cfg');
		$title = empty($options['ab_title']) ? __('Sponsored Links') : $options['ab_title'];
		?>
	        <?php echo $before_widget; ?>
				<?php echo $before_title . $title . $after_title; ?>
				<?php 
					if( !stristr( $_SERVER['PHP_SELF'], 'widgets.php' ) ){
						ad_buttons();
					}
				?>
	        <?php echo $after_widget; ?>
		<?php
	}
	
	function adbuttons_widget_control() {
		$options = $newoptions = get_option('widget_adbuttons_cfg');
		if ( $_POST["adbuttons_widget_submit"] ) {
			$newoptions['ab_title'] = strip_tags(stripslashes($_POST["adbuttons_widget_title"]));
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_adbuttons_cfg', $options);
		}
		$title = esc_attr($options['ab_title']);
		?>
			<p><label for="adbuttons_widget_title"><?php _e('Title:'); ?> <input class="widefat" id="adbuttons_widget_title" name="adbuttons_widget_title" type="text" value="<?php echo $title; ?>" /></label></p>
			<input type="hidden" id="adbuttons_widget_submit" name="adbuttons_widget_submit" value="1" /><br/>
			That's all you can set here. All other options and ad controls can be found in the <strong>Ad Buttons</strong> 
			menu located on the far left side of this page.
		<?php
	}

	register_sidebar_widget( "Ad Buttons", "adbuttons_widget" );
	register_widget_control( "Ad Buttons", "adbuttons_widget_control" );
}

// Delay plugin execution until sidebar is loaded
add_action('widgets_init', 'widget_init_adbuttons_widget');

add_action("init", "adbuttons_getclick"); 

?>
