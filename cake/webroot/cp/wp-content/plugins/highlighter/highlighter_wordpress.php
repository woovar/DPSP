<?php
/*
Plugin Name: Highlighter
Plugin URI: http://highlighter.com
Description: Highlighter allows you and your readers to highlight, comment, share, and save text or images that they find interesting!
Version: 2.0
Author: Highlighter
Author URI: http://highlighter.com
License: GPL2
*/

/*  Copyright 2011  Highlighter.com  (email : info@highlighter.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// When activating the plugin, add a menu item to the WP Dashboard.

add_action('admin_menu', 'highlighter_menu');

function highlighter_menu() {
	add_menu_page( 'Highlighter Options', 'Highlighter', 'manage_options', 'highlighter_admin_page', 'highlighter_options', WP_CONTENT_URL.'/plugins/highlighter/wp.png' ); 
} //Specifies parameters for the new dashboard menu item and page. The page function is called highlighter_options. Arguments found in WP Codex.

// This is the Highlighter menu in the WP Dashboard
function highlighter_options() {
	if (!current_user_can('manage_options'))  { //Make sure that only the admins can access the Highlighter menu
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
		
	//This is the iFrame that will have Highlighter.com in the admin menu.
	echo '<div style="width: auto; height: auto; margin: 15px 0 0 10px; padding: 0;">';
		echo '<iframe id="ifm" src="http://highlighter.com" width="100%" height="auto" style="position: relative; float: left;">';
		echo '</iframe>';
	echo '</div> '; //close main container div
	echo "<script type=text/javascript>
		function pageY(elem) {
    		return elem.offsetParent ? (elem.offsetTop + pageY(elem.offsetParent)) : elem.offsetTop;
		}
		var buffer = 0; //scroll bar buffer
		function resizeIframe() {
    		var height = document.documentElement.clientHeight;
    		height -= pageY(document.getElementById('ifm'))+ buffer ;
    		height = (height < 0) ? 0 : height;
    		document.getElementById('ifm').style.height = height + 'px';
		}
		document.getElementById('ifm').onload=resizeIframe;
		window.onresize = resizeIframe;
		</script>";
}

		add_action('admin_notices', 'no_site_alert');
		
		function no_site_alert() {
			$hl_site_check_url = home_url(); //Store the URL to use in the remote_get URL.
			$hl_site_check = wp_remote_get( "http://highlighter.com/wordpress_plugin_remote.php?site_url={$hl_site_check_url}/" ); //Give the API the plugin's URL to ensure that a site exists on Highlighter.com
			if (!($hl_site_check['body'])){
				echo '<div style="float: left; height: auto; margin: 15px 0 0 10px; padding: 10px  0 10px 10px; height: auto; width: 725px; background: #fffbd7; border: 1px solid #dbd69e;">';
				echo 'This site has not been added to your Highlighter.com account! <a href="' . $hl_site_check_url .'/wp-admin/admin.php?page=highlighter_admin_page">Click here to manage your account.</a>';
				echo '</div> '; //close warning container div
				echo '<div style="clear:both;"></div> '; //clear warning message
				}
		}

//Place the script within the theme footer file, with the site_id included.

add_action('wp_footer', 'highlighter_script');

function highlighter_script() {

$hl_site_url = home_url(); //Store the URL to use in the remote_get URL.
$hl_site_id = wp_remote_get( "http://highlighter.com/wordpress_plugin_remote.php?site_url={$hl_site_url}/" ); //Give the API the plugin's URL.

    echo "<script type='text/javascript'>var _hl=_hl||{};_hl.site='" . $hl_site_id['body'] . "';(function(){var hl=document.createElement('script');hl.type='text/javascript';hl.async=true;hl.src=document.location.protocol+'//highlighter.com/webscript/v1/js/highlighter.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(hl,s);})();</script>"; //Echo the script in the blog footer.
}
?>