<?php 
global $wpdb;
$ab_plugindir = get_option('siteurl').'/'.PLUGINDIR.'/'.dirname(plugin_basename(__FILE__));
$ol_flash 	= '';
$htp 		= "http://";
$htps		= "https://";
$ab_img 	= $htp;
$ab_link	= $htp;
$ab_img_err	= '';
$ab_link_err= '';
$ab_formfunc= 'add';

$ad_button_action = $_GET['action']; 
$ad_button = $_GET['adbut'];

if($ad_button_action == 'deactivate') {
	$ol_flash = "Ad Button $ad_button has been deactivated.";
	$wpdb->query("UPDATE ".$wpdb->prefix."ad_buttons SET ad_active = 0 WHERE id = $ad_button");
} elseif($ad_button_action == 'activate') {
	$ol_flash = "Ad Button $ad_button has been activated.";
	$wpdb->query("UPDATE ".$wpdb->prefix."ad_buttons SET ad_active = 1 WHERE id = $ad_button");
} elseif($ad_button_action == 'delete') {
	$ol_flash = "Ad Button $ad_button has been deleted.";
	$wpdb->query("UPDATE ".$wpdb->prefix."ad_buttons SET ad_active = 2 WHERE id = $ad_button");	
} elseif($ad_button_action == 'edit') {
	$ab_formfunc= 'edit';
	$this_ad = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."ad_buttons WHERE id = $ad_button");
	$ab_img 	= $this_ad->ad_picture;
	$ab_link	= $this_ad->ad_link;
	$ab_txt		= $this_ad->ad_text;
	$ab_strdat = $this_ad->ad_strdat;
	if($ab_strdat == '0000-00-00'){$ab_strdat = '';}
	$ab_enddat = $this_ad->ad_enddat;
	if($ab_enddat == '0000-00-00'){$ab_enddat = '';}
	$ab_views = $this_ad->ad_views;
	$ab_clicks = $this_ad->ad_clicks;
	$ab_countries = $this_ad->adg_count;
	$ab_csh  = $this_ad->adg_show;
	$ab_pos  = $this_ad->ad_pos;
}

$widget_adbuttons_cfg = array(

	'ab_cfg1'	=> ''

);

$widget_adbuttons_cfg = get_option('widget_adbuttons_cfg');

   
// check if the form has been submitted and validate input
if(isset($_POST['ab_img']) || isset($_POST['ab_link']) || isset($_POST['ab_txt'])) {
if ( 
    ! isset( $_POST['ab-create-nonce'] ) 
    || ! wp_verify_nonce( $_POST['ab-create-nonce'], 'ab-create' ) 
) {

   print 'Sorry, your nonce did not verify.';
   exit;

} else {


				if (isset($_POST['ab_img'])) { 
					$ab_img = $htp.str_replace($htp, "", esc_html($_POST['ab_img']));
				}

				if (isset($_POST['ab_link'])) {
					if(substr($_POST['ab_link'], 0, 7) == $htp){
						$ab_link = esc_html($_POST['ab_link']);
					}elseif(substr($_POST['ab_link'], 0, 8) == $htps){
						$ab_link = esc_html($_POST['ab_link']);
					}else{
						$ab_link = $htp.$_POST['ab_link'];
					}
				
				}

				if (isset($_POST['ab_txt'])) { 
					$ab_txt = esc_html($_POST['ab_txt']);
				}
				
				if (isset($_POST['ab_strdat'])) { 
					$ab_strdat = $_POST['ab_strdat'];
				}

				if (isset($_POST['ab_enddat'])) { 
					$ab_enddat = $_POST['ab_enddat'];
				}
				
				if (isset($_POST['ab_views'])) { 
					$ab_views = $_POST['ab_views'];
				}

				if (isset($_POST['ab_clicks'])) { 
					$ab_clicks = $_POST['ab_clicks'];
				}

				if (isset($_POST['ab_countries'])) { 
					$ab_countries = $_POST['ab_countries'];
				}
				
				if (isset($_POST['ab_csh'])) { 
					$ab_csh = $_POST['ab_csh'];
				}
				
				if (isset($_POST['ab_pos'])) { 
					$ab_pos = $_POST['ab_pos'];
				}

				
		if($ab_img == $htp || $ab_img == ''){
			$ab_img_err = 'Please fill in the link to your image file';
		}
		if($ab_link == $htp || $ab_link == ''){
			$ab_link_err = 'Please fill in the target link for your ad';
		}
	if($ab_img_err == '' && $ab_link_err == ''){
		if($ab_strdat == ''){$ab_strdat = '0000-00-00';}
		if($ab_enddat == ''){$ab_enddat = '0000-00-00';}

		// everything looks good, lets write to the database
		$table = $wpdb->prefix."ad_buttons";
		if($ab_formfunc=='add'){
			$wpdb->query("INSERT INTO $table(ad_picture, ad_link, ad_text, ad_active, ad_strdat, ad_enddat, ad_views, 
			ad_clicks, adg_count, adg_show, ad_pos)
			VALUES('$ab_img', '$ab_link', '$ab_txt', 0, '$ab_strdat', '$ab_enddat', '$ab_views', '$ab_clicks', '$ab_countries', 
			'$ab_csh', '$ab_pos')");
			$ol_flash = 'Your Ad Button has been created!';
			$ab_img 	= $htp;
			$ab_link	= $htp;
			$ab_txt	= '';
			$ab_strdat = '';
			$ab_enddat = '';
			$ab_img_err	= '';
			$ab_link_err= '';
		}elseif($ab_formfunc=='edit'){
			$wpdb->query("UPDATE ".$wpdb->prefix."ad_buttons SET ad_picture = '$ab_img', ad_link = '$ab_link', 
			ad_text = '$ab_txt', ad_strdat = '$ab_strdat', ad_enddat = '$ab_enddat', ad_views = '$ab_views', 
			ad_clicks = '$ab_clicks', adg_count = '$ab_countries', adg_show = '$ab_csh', ad_pos = '$ab_pos' WHERE id = $ad_button");
			$ol_flash = "Ad Button $ad_button has been updated.";
		}	
		if($ab_strdat == '0000-00-00'){$ab_strdat = '';}
		if($ab_enddat == '0000-00-00'){$ab_enddat = '';}
	}

}
}

?>
<?php if ($ol_flash != '') echo '<div id="message"class="updated fade"><p>' . $ol_flash . '</p></div>'; ?>
<div class="wrap">

<h2>Ad Buttons ad management</h2>

<?php if ($ab_formfunc=='edit'){ 
		echo "<h3>Edit Ad Button</h3>";
}else{
	echo "<h3>Create new Ad Button</h3>";}
?>

<p><form method="post" name="ab_form">
<?php wp_nonce_field('ab-create', 'ab-create-nonce'); 
$widget_adbuttons_cfg = get_option('widget_adbuttons_cfg');
echo'<script src="'.$ab_plugindir.'/color_functions.js"></script>'; ?>
<script type="text/javascript">
// Tigra Calendar v4.0.3 (01/12/2009) American (mm/dd/yyyy)
// http://www.softcomplex.com/products/tigra_calendar/
// Public Domain Software... You're welcome.

// default settins
var A_TCALDEF = {
	'months' : ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
	'weekdays' : ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
	'yearscroll': true, // show year scroller
	'weekstart': 0, // first day of week: 0-Su or 1-Mo
	'centyear'  : 70, // 2 digit years less than 'centyear' are in 20xx, othewise in 19xx.
	'imgpath' : '<?php echo"$ab_plugindir";?>/' // directory with calendar images
}
// date parsing function
function f_tcalParseDate (s_date) {

	var re_date = /^\s*(\d{2,4})\-(\d{1,2})\-(\d{1,2})\s*$/;
	if (!re_date.exec(s_date))
		return alert ("Invalid date: '" + s_date + "'.\nAccepted format is yyyy-mm-dd.")
	var n_day = Number(RegExp.$3),
		n_month = Number(RegExp.$2),
		n_year = Number(RegExp.$1);
	
	if (n_year < 100)
		n_year += (n_year < this.a_tpl.centyear ? 2000 : 1900);
	if (n_month < 1 || n_month > 12)
		return alert ("Invalid month value: '" + n_month + "'.\nAllowed range is 01-12.");
	var d_numdays = new Date(n_year, n_month, 0);
	if (n_day > d_numdays.getDate())
		return alert("Invalid day of month value: '" + n_day + "'.\nAllowed range for selected month is 01 - " + d_numdays.getDate() + ".");

	return new Date (n_year, n_month - 1, n_day);
}
// date generating function
function f_tcalGenerDate (d_date) {
	return (
		 d_date.getFullYear() + "-"
		+ (d_date.getMonth() < 9 ? '0' : '') + (d_date.getMonth() + 1) + "-"
		+ (d_date.getDate() < 10 ? '0' : '') + d_date.getDate()
	);
}

// implementation
function tcal (a_cfg, a_tpl) {

	// apply default template if not specified
	if (!a_tpl)
		a_tpl = A_TCALDEF;

	// register in global collections
	if (!window.A_TCALS)
		window.A_TCALS = [];
	if (!window.A_TCALSIDX)
		window.A_TCALSIDX = [];
	
	this.s_id = a_cfg.id ? a_cfg.id : A_TCALS.length;
	window.A_TCALS[this.s_id] = this;
	window.A_TCALSIDX[window.A_TCALSIDX.length] = this;
	
	// assign methods
	this.f_show = f_tcalShow;
	this.f_hide = f_tcalHide;
	this.f_toggle = f_tcalToggle;
	this.f_update = f_tcalUpdate;
	this.f_relDate = f_tcalRelDate;
	this.f_parseDate = f_tcalParseDate;
	this.f_generDate = f_tcalGenerDate;
	
	// create calendar icon
	this.s_iconId = 'tcalico_' + this.s_id;
	this.e_icon = f_getElement(this.s_iconId);
	if (!this.e_icon) {
		document.write('<img src="' + a_tpl.imgpath + 'cal.gif" id="' + this.s_iconId + '" onclick="A_TCALS[\'' + this.s_id + '\'].f_toggle()" class="tcalIcon" alt="Open Calendar" />');
		this.e_icon = f_getElement(this.s_iconId);
	}
	// save received parameters
	this.a_cfg = a_cfg;
	this.a_tpl = a_tpl;
}

function f_tcalShow (d_date) {

	// find input field
	if (!this.a_cfg.controlname)
		throw("TC: control name is not specified");
	if (this.a_cfg.formname) {
		var e_form = document.forms[this.a_cfg.formname];
		if (!e_form)
			throw("TC: form '" + this.a_cfg.formname + "' can not be found");
		this.e_input = e_form.elements[this.a_cfg.controlname];
	}
	else
		this.e_input = f_getElement(this.a_cfg.controlname);

	if (!this.e_input || !this.e_input.tagName || this.e_input.tagName != 'INPUT')
		throw("TC: element '" + this.a_cfg.controlname + "' does not exist in "
			+ (this.a_cfg.formname ? "form '" + this.a_cfg.controlname + "'" : 'this document'));

	// dynamically create HTML elements if needed
	this.e_div = f_getElement('tcal');
	if (!this.e_div) {
		this.e_div = document.createElement("DIV");
		this.e_div.id = 'tcal';
		document.body.appendChild(this.e_div);
	}
	this.e_shade = f_getElement('tcalShade');
	if (!this.e_shade) {
		this.e_shade = document.createElement("DIV");
		this.e_shade.id = 'tcalShade';
		document.body.appendChild(this.e_shade);
	}
	this.e_iframe =  f_getElement('tcalIF')
	if (b_ieFix && !this.e_iframe) {
		this.e_iframe = document.createElement("IFRAME");
		this.e_iframe.style.filter = 'alpha(opacity=0)';
		this.e_iframe.id = 'tcalIF';
		this.e_iframe.src = this.a_tpl.imgpath + 'pixel.gif';
		document.body.appendChild(this.e_iframe);
	}
	
	// hide all calendars
	f_tcalHideAll();

	// generate HTML and show calendar
	this.e_icon = f_getElement(this.s_iconId);
	if (!this.f_update())
		return;

	this.e_div.style.visibility = 'visible';
	this.e_shade.style.visibility = 'visible';
	if (this.e_iframe)
		this.e_iframe.style.visibility = 'visible';

	// change icon and status
	this.e_icon.src = this.a_tpl.imgpath + 'no_cal.gif';
	this.e_icon.title = 'Close Calendar';
	this.b_visible = true;
}

function f_tcalHide (n_date) {
	if (n_date)
		this.e_input.value = this.f_generDate(new Date(n_date));

	// no action if not visible
	if (!this.b_visible)
		return;

	// hide elements
	if (this.e_iframe)
		this.e_iframe.style.visibility = 'hidden';
	if (this.e_shade)
		this.e_shade.style.visibility = 'hidden';
	this.e_div.style.visibility = 'hidden';
	
	// change icon and status
	this.e_icon = f_getElement(this.s_iconId);
	this.e_icon.src = this.a_tpl.imgpath + 'cal.gif';
	this.e_icon.title = 'Open Calendar';
	this.b_visible = false;
}

function f_tcalToggle () {
	return this.b_visible ? this.f_hide() : this.f_show();
}

function f_tcalUpdate (d_date) {
	
	var d_today = this.a_cfg.today ? this.f_parseDate(this.a_cfg.today) : f_tcalResetTime(new Date());
	var d_selected = this.e_input.value == ''
		? (this.a_cfg.selected ? this.f_parseDate(this.a_cfg.selected) : d_today)
		: this.f_parseDate(this.e_input.value);

	// figure out date to display
	if (!d_date)
		// selected by default
		d_date = d_selected;
	else if (typeof(d_date) == 'number')
		// get from number
		d_date = f_tcalResetTime(new Date(d_date));
	else if (typeof(d_date) == 'string')
		// parse from string
		this.f_parseDate(d_date);
		
	if (!d_date) return false;

	// first date to display
	var d_firstday = new Date(d_date);
	d_firstday.setDate(1);
	d_firstday.setDate(1 - (7 + d_firstday.getDay() - this.a_tpl.weekstart) % 7);
	
	var a_class, s_html = '<table class="ctrl"><tbody><tr>'
		+ (this.a_tpl.yearscroll ? '<td' + this.f_relDate(d_date, -1, 'y') + ' title="Previous Year"><img src="' + this.a_tpl.imgpath + 'prev_year.gif" /></td>' : '')
		+ '<td' + this.f_relDate(d_date, -1) + ' title="Previous Month"><img src="' + this.a_tpl.imgpath + 'prev_mon.gif" /></td><th>'
		+ this.a_tpl.months[d_date.getMonth()] + ' ' + d_date.getFullYear()
			+ '</th><td' + this.f_relDate(d_date, 1) + ' title="Next Month"><img src="' + this.a_tpl.imgpath + 'next_mon.gif" /></td>'
		+ (this.a_tpl.yearscroll ? '<td' + this.f_relDate(d_date, 1, 'y') + ' title="Next Year"><img src="' + this.a_tpl.imgpath + 'next_year.gif" /></td></td>' : '')
		+ '</tr></tbody></table><table><tbody><tr class="wd">';

	// print weekdays titles
	for (var i = 0; i < 7; i++)
		s_html += '<th>' + this.a_tpl.weekdays[(this.a_tpl.weekstart + i) % 7] + '</th>';
	s_html += '</tr>' ;

	// print calendar table
	var n_date, n_month, d_current = new Date(d_firstday);
	while (d_current.getMonth() == d_date.getMonth() ||
		d_current.getMonth() == d_firstday.getMonth()) {
	
		// print row heder
		s_html +='<tr>';
		for (var n_wday = 0; n_wday < 7; n_wday++) {

			a_class = [];
			n_date  = d_current.getDate();
			n_month = d_current.getMonth();

			// other month
			if (d_current.getMonth() != d_date.getMonth())
				a_class[a_class.length] = 'othermonth';
			// weekend
			if (d_current.getDay() == 0 || d_current.getDay() == 6)
				a_class[a_class.length] = 'weekend';
			// today
			if (d_current.valueOf() == d_today.valueOf())
				a_class[a_class.length] = 'today';
			// selected
			if (d_current.valueOf() == d_selected.valueOf())
				a_class[a_class.length] = 'selected';

			s_html += '<td onclick="A_TCALS[\'' + this.s_id + '\'].f_hide(' + d_current.valueOf() + ')"' + (a_class.length ? ' class="' + a_class.join(' ') + '">' : '>') + n_date + '</td>'

			d_current.setDate(++n_date);
			while (d_current.getDate() != n_date && d_current.getMonth() == n_month) {
				d_current.setHours(d_current.getHours + 1);
				d_current = f_tcalResetTime(d_current);
			}
		}
		// print row footer
		s_html +='</tr>';
	}
	s_html +='</tbody></table>';
	
	// update HTML, positions and sizes
	this.e_div.innerHTML = s_html;

	var n_width  = this.e_div.offsetWidth;
	var n_height = this.e_div.offsetHeight;
	var n_top  = f_getPosition (this.e_icon, 'Top') + this.e_icon.offsetHeight;
	var n_left = f_getPosition (this.e_icon, 'Left') - n_width + this.e_icon.offsetWidth;
	if (n_left < 0) n_left = 0;
	
	this.e_div.style.left = n_left + 'px';
	this.e_div.style.top  = n_top + 'px';

	this.e_shade.style.width = (n_width + 8) + 'px';
	this.e_shade.style.left = (n_left - 1) + 'px';
	this.e_shade.style.top = (n_top - 1) + 'px';
	this.e_shade.innerHTML = b_ieFix
		? '<table><tbody><tr><td rowspan="2" colspan="2" width="6"><img src="' + this.a_tpl.imgpath + 'pixel.gif"></td><td width="7" height="7" style="filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'' + this.a_tpl.imgpath + 'shade_tr.png\', sizingMethod=\'scale\');"><img src="' + this.a_tpl.imgpath + 'pixel.gif"></td></tr><tr><td height="' + (n_height - 7) + '" style="filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'' + this.a_tpl.imgpath + 'shade_mr.png\', sizingMethod=\'scale\');"><img src="' + this.a_tpl.imgpath + 'pixel.gif"></td></tr><tr><td width="7" style="filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'' + this.a_tpl.imgpath + 'shade_bl.png\', sizingMethod=\'scale\');"><img src="' + this.a_tpl.imgpath + 'pixel.gif"></td><td style="filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'' + this.a_tpl.imgpath + 'shade_bm.png\', sizingMethod=\'scale\');" height="7" align="left"><img src="' + this.a_tpl.imgpath + 'pixel.gif"></td><td style="filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'' + this.a_tpl.imgpath + 'shade_br.png\', sizingMethod=\'scale\');"><img src="' + this.a_tpl.imgpath + 'pixel.gif"></td></tr><tbody></table>'
		: '<table><tbody><tr><td rowspan="2" width="6"><img src="' + this.a_tpl.imgpath + 'pixel.gif"></td><td rowspan="2"><img src="' + this.a_tpl.imgpath + 'pixel.gif"></td><td width="7" height="7"><img src="' + this.a_tpl.imgpath + 'shade_tr.png"></td></tr><tr><td background="' + this.a_tpl.imgpath + 'shade_mr.png" height="' + (n_height - 7) + '"><img src="' + this.a_tpl.imgpath + 'pixel.gif"></td></tr><tr><td><img src="' + this.a_tpl.imgpath + 'shade_bl.png"></td><td background="' + this.a_tpl.imgpath + 'shade_bm.png" height="7" align="left"><img src="' + this.a_tpl.imgpath + 'pixel.gif"></td><td><img src="' + this.a_tpl.imgpath + 'shade_br.png"></td></tr><tbody></table>';
	
	if (this.e_iframe) {
		this.e_iframe.style.left = n_left + 'px';
		this.e_iframe.style.top  = n_top + 'px';
		this.e_iframe.style.width = (n_width + 6) + 'px';
		this.e_iframe.style.height = (n_height + 6) +'px';
	}
	return true;
}

function f_getPosition (e_elemRef, s_coord) {
	var n_pos = 0, n_offset,
		e_elem = e_elemRef;

	while (e_elem) {
		n_offset = e_elem["offset" + s_coord];
		n_pos += n_offset;
		e_elem = e_elem.offsetParent;
	}
	// margin correction in some browsers
	if (b_ieMac)
		n_pos += parseInt(document.body[s_coord.toLowerCase() + 'Margin']);
	else if (b_safari)
		n_pos -= n_offset;
	
	e_elem = e_elemRef;
	while (e_elem != document.body) {
		n_offset = e_elem["scroll" + s_coord];
		if (n_offset && e_elem.style.overflow == 'scroll')
			n_pos -= n_offset;
		e_elem = e_elem.parentNode;
	}
	return n_pos;
}

function f_tcalRelDate (d_date, d_diff, s_units) {
	var s_units = (s_units == 'y' ? 'FullYear' : 'Month');
	var d_result = new Date(d_date);
	d_result['set' + s_units](d_date['get' + s_units]() + d_diff);
	if (d_result.getDate() != d_date.getDate())
		d_result.setDate(0);
	return ' onclick="A_TCALS[\'' + this.s_id + '\'].f_update(' + d_result.valueOf() + ')"';
}

function f_tcalHideAll () {
	for (var i = 0; i < window.A_TCALSIDX.length; i++)
		window.A_TCALSIDX[i].f_hide();
}

function f_tcalResetTime (d_date) {
	d_date.setHours(0);
	d_date.setMinutes(0);
	d_date.setSeconds(0);
	d_date.setMilliseconds(0);
	return d_date;
}

f_getElement = document.all ?
	function (s_id) { return document.all[s_id] } :
	function (s_id) { return document.getElementById(s_id) };

if (document.addEventListener)
	window.addEventListener('scroll', f_tcalHideAll, false);
if (window.attachEvent)
	window.attachEvent('onscroll', f_tcalHideAll);
	
// global variables
var s_userAgent = navigator.userAgent.toLowerCase(),
	re_webkit = /WebKit\/(\d+)/i;
var b_mac = s_userAgent.indexOf('mac') != -1,
	b_ie5 = s_userAgent.indexOf('msie 5') != -1,
	b_ie6 = s_userAgent.indexOf('msie 6') != -1 && s_userAgent.indexOf('opera') == -1;
var b_ieFix = b_ie5 || b_ie6,
	b_ieMac  = b_mac && b_ie5,
	b_safari = b_mac && re_webkit.exec(s_userAgent) && Number(RegExp.$1) < 500;

</script>
<?php echo"
<style type=\"text/css\">
/* calendar icon */
img.tcalIcon {
	cursor: pointer;
	margin-left: 1px;
	vertical-align: middle;
}
/* calendar container element */
div#tcal {
	position: absolute;
	visibility: hidden;
	z-index: 100;
	width: 158px;
	padding: 2px 0 0 0;
}
/* all tables in calendar */
div#tcal table {
	width: 100%;
	border: 1px solid silver;
	border-collapse: collapse;
	background-color: white;
}
/* navigation table */
div#tcal table.ctrl {
	border-bottom: 0;
}
/* navigation buttons */
div#tcal table.ctrl td {
	width: 15px;
	height: 20px;
}
/* month year header */
div#tcal table.ctrl th {
	background-color: white;
	color: black;
	border: 0;
}
/* week days header */
div#tcal th {
	border: 1px solid silver;
	border-collapse: collapse;
	text-align: center;
	padding: 3px 0;
	font-family: tahoma, verdana, arial;
	font-size: 10px;
	background-color: gray;
	color: white;
}
/* date cells */
div#tcal td {
	border: 0;
	border-collapse: collapse;
	text-align: center;
	padding: 2px 0;
	font-family: tahoma, verdana, arial;
	font-size: 11px;
	width: 22px;
	cursor: pointer;
}
/* date highlight
   in case of conflicting settings order here determines the priority from least to most important */
div#tcal td.othermonth {
	color: silver;
}
div#tcal td.weekend {
	background-color: #ACD6F5;
}
div#tcal td.today {
	border: 1px solid red;
}
div#tcal td.selected {
	background-color: #FFB3BE;
}
/* iframe element used to suppress windowed controls in IE5/6 */
iframe#tcalIF {
	position: absolute;
	visibility: hidden;
	z-index: 98;
	border: 0;
}
/* transparent shadow */
div#tcalShade {
	position: absolute;
	visibility: hidden;
	z-index: 99;
}
div#tcalShade table {
	border: 0;
	border-collapse: collapse;
	width: 100%;
}
div#tcalShade table td {
	border: 0;
	border-collapse: collapse;
	padding: 0;
}

</style>"; ?>
<table class="form-table">

<tr valign="top">
<th scope="row">Ad Button Image </th>
<td><input name="ab_img" type="text" value="<?php echo $ab_img; ?>" size="40" /> <?php if($ab_img_err)echo"$ab_img_err"; ?></td>
<td rowspan="3"><?php if ($ad_button_action == 'edit'){echo"<a href=\"$ab_link\" target=\"_blank\" title=\"$ab_txt\"><img src=\"$ab_img\" alt=\"$ab_txt\"  align=\"left\" vspace=\"10\" hspace=\"10\" border=\"0\"></a>";}?></td>
</tr>

<tr valign="top">
<th scope="row">Ad Button Link </th>
<td><input name="ab_link" type="text" value="<?php echo $ab_link; ?>" size="40" /> <?php if($ab_link_err)echo"$ab_link_err"; ?></td>
</tr>
<tr valign="top">
<th scope="row">Ad Button Text </th>
<td><input name="ab_txt" type="text" value="<?php echo $ab_txt; ?>" size="40" /></td>
</tr>
<tr valign="top">
<th scope="row">Scheduling</th>
<td><table border="0">
  <tr>
    <td>Start date:</td>
    <td><input name="ab_strdat" type="text" value="<?php echo $ab_strdat; ?>" size="10" /><script language="JavaScript">
	var o_cal = new tcal ({
		// form name
		'formname': 'ab_form',
		// input name
		'controlname': 'ab_strdat'
	});
	
	</script>
</td>
  </tr>
  <tr>
    <td>End date: </td>
    <td><input name="ab_enddat" type="text" value="<?php echo $ab_enddat; ?>" size="10" /><script language="JavaScript">
	var o_cal = new tcal ({
		// form name
		'formname': 'ab_form',
		// input name
		'controlname': 'ab_enddat'
	});
	
	</script></td>
  </tr>
</table>
  <br/></td>
<td>yyyy-mm-dd format or leave empty for unlimited runtime</td>
</tr>
<tr valign="top">
<th scope="row">Ad position</th>
<td><input name="ab_pos" type="text" value="<?php echo $ab_pos; ?>" size="40" /></td>
<td>change the order of the ads, a higher number means the ad will move down in the list </td>
</tr>
<tr valign="top">
<th scope="row">Counters</th>
<td>views <input name="ab_views" type="text" value="<?php echo $ab_views; ?>" size="9" /><br>
clicks <input name="ab_clicks" type="text" value="<?php echo $ab_clicks; ?>" size="9" /></td>
<td>This only resets the views and clicks seen on this screen. Detailed view and click information is stored elsewhere. 
Viewing detailed statistics is being worked on and will be incorporated into a future release.</td>
</tr>
<tr valign="top">
<th scope="row">Geo targeting</th>
<td>countries <input name="ab_countries" type="text" value="<?php echo $ab_countries; ?>" size="20" /><br>
show <input name="ab_csh" type="radio" value="s" <?php if($ab_csh=="s")echo"checked"; ?>>
hide <input name="ab_csh" type="radio" value="h" <?php if($ab_csh=="h")echo"checked"; ?>></td>
<td>Separate values with a comma<br/>
select 'show' to only show this ad to visitors from the listed countries 
or 'hide' to show the ad only to visitors from countries not listed.
</td>
</tr>
</table>
<p class="submit">
<input type="submit" name="Submit" value="<?php if ($ab_formfunc=='edit'){ 
		echo "Update Ad Button";
}else{
	echo "Create Ad Button";}
?>" />
</p>

</form></p>
<h3 id="currently-active">Scheduled Ad Buttons</h3>
<table class="widefat" id="active-plugins-table">
	<thead>
	<tr>
		<th scope="col" class="num">Ad ID</th>
		<th scope="col">Ad Button</th>
		<th scope="col" class="num">Ad Text</th>
		<th scope="col" class="num">Start Date</th>
		<th scope="col" class="num">End Date</th>
		<th scope="col" class="action-links">Action</th>
	</tr>
	</thead>
	<tbody class="plugins">
<?php
$results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ad_buttons WHERE ad_active = 1 AND ad_strdat > CURDATE() ");
foreach($results as $result)
{
if($result->ad_views){
	$ad_ctr = round((($result->ad_clicks / $result->ad_views) * 100 ), 2);
	}
else {
	$ad_ctr = 0;
	}

echo  "
	<tr class='active'>
		<td class='vers'>$result->id</td>
		<td class='name'><a href=\"$result->ad_link\" target=\"_blank\" title=\"$result->ad_text\"><img src=\"$result->ad_picture\" alt=\"$result->ad_text\"  align=\"left\" vspace=\"10\" hspace=\"10\" border=\"0\"></a></td>
		<td class='vers'>$result->ad_text</td>
		<td class='vers'>$result->ad_strdat</td>
		<td class='vers'>$result->ad_enddat</td>
		<td class='togl action-links'><a href=\"?page=ad-buttons/adbuttons.php&#038;action=deactivate&#038;adbut=$result->id\" title=\"Deactivate this Ad Button\" class=\"delete\">Deactivate</a><br/>
		<a href=\"?page=ad-buttons/adbuttons.php&#038;action=edit&#038;adbut=$result->id\" title=\"Edit this Ad Button\" class=\"delete\">Edit</a></td> 
	</tr>

";
}
?>
</tbody>
</table>




<h3 id="currently-active">Active Ad Buttons</h3>
<table class="widefat" id="active-plugins-table">
	<thead>
	<tr>
		<th scope="col" class="num">Ad ID</th>
		<th scope="col">Ad Button</th>
		<th scope="col" class="num">Ad Text</th>
		<th scope="col" class="num">Ad Views</th>
		<th scope="col" class="num">Ad Clicks</th>
		<th scope="col" class="num">CTR</th>
		<th scope="col" class="action-links">Action</th>
	</tr>
	</thead>
	<tbody class="plugins">
<?php
$results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ad_buttons WHERE ad_active = 1 AND ad_strdat <= CURDATE() AND ad_enddat >= CURDATE() OR ad_active = 1 AND ad_strdat <= CURDATE() AND ad_enddat = '0000-00-00'");
foreach($results as $result)
{
if($result->ad_views){
	$ad_ctr = round((($result->ad_clicks / $result->ad_views) * 100 ), 2);
	}
else {
	$ad_ctr = 0;
	}

echo  "
	<tr class='active'>
		<td class='vers'>$result->id</td>
		<td class='name'><a href=\"$result->ad_link\" target=\"_blank\" title=\"$result->ad_text\"><img src=\"$result->ad_picture\" alt=\"$result->ad_text\"  align=\"left\" vspace=\"10\" hspace=\"10\" border=\"0\"></a></td>
		<td class='vers'>$result->ad_text</td>
		<td class='vers'>$result->ad_views</td>
		<td class='vers'>$result->ad_clicks</td>
		<td class='vers'>$ad_ctr%</td>
		<td class='togl action-links'><a href=\"?page=ad-buttons/adbuttons.php&#038;action=deactivate&#038;adbut=$result->id\" title=\"Deactivate this Ad Button\" class=\"delete\">Deactivate</a><br/>
		<a href=\"?page=ad-buttons/adbuttons.php&#038;action=edit&#038;adbut=$result->id\" title=\"Edit this Ad Button\" class=\"delete\">Edit</a></td> 
	</tr>

";
}
?>
</tbody>
</table>
<h3 id="inactive-plugins">Expired Ad Buttons</h3>
<table class="widefat" id="inactive-plugins-table">
	<thead>
	<tr>
		<th scope="col" class="num">Ad ID</th>
		<th scope="col">Ad Button</th>
		<th scope="col" class="num">Ad Text</th>
		<th scope="col" class="num">Start Date</th>
		<th scope="col" class="num">End Date</th>
		<th scope="col" class="action-links">Action</th>
	</tr>
	</thead>
	<tbody class="plugins">
<?php
$results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ad_buttons WHERE ad_active = 1 AND ad_enddat < CURDATE() AND ad_enddat <> '0000-00-00' ");
foreach($results as $result)
{
if($result->ad_views){
	$ad_ctr = round((($result->ad_clicks / $result->ad_views) * 100 ), 2);
	}
else {
	$ad_ctr = 0;
	}

echo  "
	<tr class='inactive'>
		<td class='vers'>$result->id</td>
		<td class='name'><a href=\"$result->ad_link\" target=\"_blank\" title=\"$result->ad_text\"><img src=\"$result->ad_picture\" alt=\"$result->ad_text\"  align=\"left\" vspace=\"10\" hspace=\"10\" border=\"0\"></a></td>
		<td class='vers'>$result->ad_text</td>
		<td class='vers'>$result->ad_strdat</td>
		<td class='vers'>$result->ad_enddat</td>
		<td class='togl action-links'><a href=\"?page=ad-buttons/adbuttons.php&#038;action=deactivate&#038;adbut=$result->id\" title=\"Deactivate this Ad Button\" class=\"delete\">Deactivate</a><br/>
		<a href=\"?page=ad-buttons/adbuttons.php&#038;action=edit&#038;adbut=$result->id\" title=\"Edit this Ad Button\" class=\"delete\">Edit</a></td> 
	</tr>

";
}
?>
</tbody>
</table>

<h3 id="inactive-plugins">Inactive Ad Buttons</h3>
<table class="widefat" id="inactive-plugins-table">
	<thead>
	<tr>

		<th scope="col" class="num">Ad ID</th>
		<th scope="col">Ad Button</th>
		<th scope="col" class="num">Ad Text</th>
		<th scope="col" class="num">Ad Views</th>
		<th scope="col" class="num">Ad Clicks</th>
		<th scope="col" class="num">CTR</th>
		<th scope="col" class="action-links">Action</th>
	</tr>
	</thead>
	<tbody class="plugins">
<?php
$results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ad_buttons WHERE ad_active = 0");
foreach($results as $result)
{
if($result->ad_views){
	$ad_ctr = round((($result->ad_clicks / $result->ad_views) * 100 ), 2);
	}
else {
	$ad_ctr = 0;
	}

echo  "
	<tr class='inactive'>
		<td class='vers'>$result->id</td>
		<td class='name'><a href=\"$result->ad_link\" target=\"_blank\" title=\"$result->ad_text\"><img src=\"$result->ad_picture\" alt=\"$result->ad_text\"  align=\"left\" vspace=\"10\" hspace=\"10\" border=\"0\"></a></td>
		<td class='vers'>$result->ad_text</td>
		<td class='vers'>$result->ad_views</td>
		<td class='vers'>$result->ad_clicks</td>
		<td class='vers'>$ad_ctr%</td>
		<td class='togl action-links'><a href=\"?page=ad-buttons/adbuttons.php&#038;action=activate&#038;adbut=$result->id\" title=\"Activate this Ad Button\" class=\"delete\">Activate</a><br/>
		<a href=\"?page=ad-buttons/adbuttons.php&#038;action=edit&#038;adbut=$result->id\" title=\"Edit this Ad Button\" class=\"delete\">Edit</a><br/><br/>
		<a href=\"?page=ad-buttons/adbuttons.php&#038;action=delete&#038;adbut=$result->id\" title=\"Delete this Ad Button\" class=\"delete\">Delete</a></td> 
	</tr>

";
}
?>
</tbody>
</table>

</div>