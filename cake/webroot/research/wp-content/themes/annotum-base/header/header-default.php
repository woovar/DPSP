<?php

/**
 * @package anno
 * This file is part of the Annotum theme for WordPress
 * Built on the Carrington theme framework <http://carringtontheme.com>
 *
 * Copyright 2008-2011 Crowd Favorite, Ltd. All rights reserved. <http://crowdfavorite.com>
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 */
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }
if (CFCT_DEBUG) { cfct_banner(__FILE__); }
?>
<!doctype html>
<?php anno_open_html(); ?>
<head>
	<meta charset="<?php bloginfo('charset') ?>" />

	<title><?php wp_title( '-', true, 'right' ); echo esc_html( get_bloginfo('name') ); ?></title>

	<?php wp_head(); ?>
	<?php cfct_misc('custom-colors'); ?>
	<link type="text/css" rel="stylesheet" href="https://paulscholten.eu/css/override.css" media="screen, print" /> 
	<link rel="stylesheet" href="https://paulscholten.eu/css/print.css" type="text/css" media="print" />
    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery("#toc").append('<h4>Table of contents</h4>')
            j = 0;
            jQuery("h1, h2").each(function(i) {
                j=j+1;
                if (j>3){
                var current = jQuery(this);
                current.attr("id", "title" + i);

                jQuery("#toc").append("<a id='link" + i + "' style='display:block;' href='#title" + i + "' title='" + current.attr("tagName") + "'>" + current.html() + "</a>");
                }
            });
        });
    </script>
</head>

<body <?php body_class(); ?>>

	<div id="wrapper">
	<div id="mainCntr">
			
			<div id="headerCntr">
<div style='font-weight:bold;color: #000;text-decoration: none;margin:0;padding-top:15px;padding-left:27px;' class='logo'><a style='color: #000;text-decoration: none;' href='/'>Paul Scholten in Open Access</a></div>
		    


				

<div class="linkBox">
</div> <!-- /link box -->

<div class="navBox2">
  <ul>
<?php
#if (preg_match('/.*en\/$/',$_SERVER['REQUEST_URI'])){
echo '<li>';
echo '<a href="/en/" target="_self">';
echo '<img src="/img/en.png" height="12px" />';
echo '</a>';
echo '</li>';
#}else{
echo '<li>';
echo '<a href="/" target="_self">';
echo '<img src="/img/nl.png" height="12px" />';
echo '</a>';
echo '</li>';
#}
?>
		<li><a href="/" target="_self">Home</a></li><li><a href="/contact-en" target="_self">Contact</a></li><li><a href="/licenses-en" target="_self">Licenses</a></li><li><a href="/hosting-en" target="_self">Hosting</a></li>
  </ul>
	
	<!--input id="printthis" type="button" value="Print" onclick="window.print()"-->
</div> <!-- /nav box -->


	  		
<div class="menuBox">




<?php 

$context = stream_context_create(array('http' => array('header'=>'Connection: close',
'http' => array(
'method' => 'GET',
'header' => 'Accept-Encoding:gzip,deflate\r\n',
))));
			$parse=parse_url(get_site_url());
			$return=json_decode(file_get_contents('http://'.$parse['host'].'/cp/?json=menu.get_menu_english',false,$context));
			$return = preg_replace('/\/cp\//','/',$return);
			echo $return;//JVDP
?>


</div> <!-- /menu box -->
</div>

<div id="main" class="act">
	<div class="in">
