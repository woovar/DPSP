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

get_header();
//JVDP
?>
<div id='contentCntr'>
<div id="main-body" class="clearfix">
	<h1 class="section-title"><span><?php _e('Page Not Found', 'anno') ?></span></h1>
	<?php _e('Sorry, we&rsquo;re not sure what you&rsquo;re looking for here.', 'anno'); ?> 
</div><!-- #main-content -->
<div id="main-sidebar" class="clearfix">
</div><!-- #main-sidebar -->
</div>
<?php get_footer(); ?>