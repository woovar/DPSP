<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<title><?php echo $title_for_layout; ?></title>
	<?php if(isset($description_for_layout)){ 
		echo '<meta name="description" content="'.$description_for_layout.'"/>';
	}?>
	
	<link rel="shortcut icon" href="<?php echo Configure::read('Site.site_url'); ?>/favicon.ico" />

	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
  
  <meta name="robots" content="NOODP,NOYDIR,NOARCHIVE,INDEX,FOLLOW" />
	  
<?php 
if(!empty($table)){ ?>
<link rel='stylesheet' id='tablepress-default-css'  href='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-content/plugins/tablepress/css/default.min.css?ver=1.0' type='text/css' media='all' />
<?php } ?>

<?php
if(!empty($tablesorter)){ ?>
<script type="text/javascript" src="/js/jquery-latest.js"></script>
<script type="text/javascript" src="/js/jquery.tablesorter.min"></script>
<?php } ?>

<?php if(empty($info) || $info!=1){ ?>
<link rel='stylesheet' id='cp_layout_css-css'  href='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-content/plugins/commentpress-core/themes/commentpress-theme/assets/css/layout.css?ver=3.4.6' type='text/css' media='all' />	

<link rel='stylesheet' id='cp_jquery_ui_base-css'  href='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-content/plugins/commentpress-core/commentpress-core/assets/css/jquery.ui.css?ver=3.4.6' type='text/css' media='all' />

<link rel='stylesheet' id='cp_typography_css-css'  href='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-content/plugins/commentpress-core/themes/commentpress-theme/assets/css/typography.css?ver=3.4.6' type='text/css' media='all' />
<!-- link rel='stylesheet' id='cp_print_css-css'  href='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-content/plugins/commentpress-core/themes/commentpress-theme/assets/css/print.css?ver=3.4.6' type='text/css' media='print' /-->
<link rel='stylesheet' id='wp-jquery-ui-dialog-css'  href='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/css/jquery-ui-dialog.min.css?ver=3.5.1' type='text/css' media='all' />

<?php } ?>
<!-- IE stylesheets so we can override anything -->
<!--[if IE 6]>
<link rel="stylesheet" type="text/css" href="<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-content/plugins/commentpress-core/themes/commentpress-theme/assets/css/ie6.css" media="screen" />
<![endif]-->
<!--[if gte IE 7]>
<link rel="stylesheet" type="text/css" href="<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-content/plugins/commentpress-core/themes/commentpress-theme/assets/css/ie7.css" media="screen" />
<![endif]-->

<!--  <link rel='stylesheet' id='cp_webfont_css-css'  href='http://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic' type='text/css' media='all' /> -->
<!--  <link rel='stylesheet' id='cp_colours_css-css'  href='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-content/plugins/commentpress-core/themes/commentpress-theme/assets/css/colours-01.css?ver=3.4.6' type='text/css' media='all' />  --> 


<link rel='stylesheet' id='toc-screen-css'  href='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-content/plugins/table-of-contents-plus/screen.css?ver=1303.1' type='text/css' media='all' />
<link type="text/css" rel="stylesheet" href="https://paulscholten.eu/css/override.css" media="screen" /> 
<link rel="stylesheet" href="https://paulscholten.eu/css/print.css" type="text/css" media="print" />
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-46034732-1', 'paulscholten.eu');
  ga('send', 'pageview');

</script>

	<script type="text/javascript">
	
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-1835430-50']);
	  _gaq.push(['_trackPageview']);
	
	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	
	</script>

	
</head>

<body>

	<div id="wrapper">

<?php if(empty($info) || $info!=1){ ?>

<?php if(!empty($content->page->custom_fields->translation) && $content->page->custom_fields->translation){
	#echo $this->element('selector',array('add_to_class'=>'_sidebar'));
}
?>

<!--
<span id="sidebar_show_hide" class='sidebar_show_hide' title="Minimise all Comment Sections"></span>
<div id='sidebar_cp' class='right'>
<div id='loading_comments'>
Loading comments
</div>
<img id='waiting_img' src='/img/waiting.gif'/>
</div>			
-->
<?php } ?>
<div id="mainCntr">
			
			<div id="headerCntr">
		    
<!--<a class="logo" href="/" title="Paul Scholten"><img src="/img/paddinglogo.png" alt="Paul Scholten"/></a>-->
<div style='font-weight:bold;color: #000;text-decoration: none;margin:0;padding-top:15px;padding-left:27px;' class='logo'><a style='color: #000;text-decoration: none;' href='/'>Paul Scholten in Open Access</a></div>


				



<div class="navBox2">
<ul>
<?php
#if (preg_match('/.*en\/$/',$_SERVER['REQUEST_URI'])){
echo '<li>';
echo '<a href="/project/" target="_self">';
echo '<img src="/img/nl.png" height="12px" />';
echo '</a>';
echo '</li>';
#}else{
echo '<li>';
echo '<a href="/dpsp-annual-en/" target="_self">';
echo '<img src="/img/en.png" height="12px" />';
echo '</a>';
echo '</li>';
#}
?>
    <li><a href="/" target="_self"><b>Home</b></a></li><li><a href="/contact-en" target="_self"><b>Contact</b></a></li><li><a href="/licenses-en" target="_self"><b>Licenses</b></a></li><li style='background:None'><a href="/hosting-en" target="_self"><b>Hosting</b></a></li>
</ul>
</div> <!-- /link box -->

	  		
<div class="menuBox">
<?php echo $menu; ?>
<?php echo $this->element('search');?>


</div> <!-- /menu box -->
			</div> <!-- /header container -->
	
			<div id="contentCntr">
	
				<div id="textCntr">
					
<div id="container">
<div id="main_wrapper" class="clearfix">
<div id="page_wrapper">
	<div id="content" class="clearfix">
		<div class='post'>
			<?php echo $this->element('breadcrumb'); ?>
			<?php debug($content); 
			if(!empty($content->page->custom_fields->youtube) && $content->page->custom_fields->youtube){
				echo '<div class="youtube">';
			
			}
			?>
			<?php echo $this->fetch('content'); ?>
			<?php if(!empty($content->page->custom_fields->youtube) && $content->page->custom_fields->youtube){
				echo '</div>';
				
			}
			?>
		
		</div>
	
	</div>
	<p id="back-top">
		<a href="#top">Back to top<span></span></a>
	</p>
</div>
</div>
</div>



</div> <!-- /text container -->


				</div> <!-- /content container -->

			<div id="footerCntr">
				
<ul>
	<li><a href="/" target="_self">Home</a></li><li><a href="/contact" target="_self">Contact</a></li><li><a href="/licenses" target="_self">Licenses</a></li><li><a href="/disclaimer" target="_self">Disclaimer</a></li>
</ul>
<p class="right">

Site made by:
<a target="_blank" href="http://woovar.com">Woovar</a> (Development) and
Huppes-Cluysenaer
</p>

			</div> <!-- /footer container -->
	
		</div> <!-- /main container -->

	</div> <!-- /wrapper -->


<?php 
if($slug!='articles'&&$slug!='preprints'){ ?>
	<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/jquery/jquery.js?ver=1.8.3'></script>
<?php } ?>

<script type='text/javascript' src='/readmore.js'>
						</script>
<?php if (isset($research_question)||isset($articles)){ ?>
<script type='text/javascript'>
jQuery('.research-abstract').readmore({
	embedCSS: false,
	maxHeight: 58
	});
						</script>
<?php } ?>
<?php if(empty($info) || $info!=1){ ?>

<!-- 
<script type='text/javascript'>
jQuery(document).ready(function(){
jQuery.ajax({
		type: "POST",

		url:'<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/?json=sidebar.get_sidebar&slug=<?php echo $slug; ?>',	
		async:false,
		//data:data,
		success:
	function(data) {
		//jQuery(data).appendTo('#sidebar_cp');
		jQuery('#sidebar_cp').html(data);
		jQuery('#sidebar_show_hide').show();
		jQuery('#searchbox_translation_sidebar').show();
    }});
});
</script>
-->
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/jquery/jquery.form.min.js?ver=2.73'></script>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/jquery/ui/jquery.ui.core.min.js?ver=1.9.2'></script>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/jquery/ui/jquery.ui.widget.min.js?ver=1.9.2'></script>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/jquery/ui/jquery.ui.mouse.min.js?ver=1.9.2'></script>
<!-- script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/jquery/ui/jquery.ui.resizable.min.js?ver=1.9.2'></script-->

<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-content/plugins/commentpress-core/commentpress-core/assets/js/jquery.commentpress.dev.js?ver=3.4.6'></script>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-content/plugins/commentpress-core/commentpress-core/assets/js/jquery.scrollTo.js?ver=3.4.6'></script>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-content/plugins/commentpress-core/commentpress-core/assets/js/jquery.biscuit.js?ver=3.4.6'></script>
<script type='text/javascript'>
/* <![CDATA[ */
var CommentpressSettings = {"cp_comments_open":"y","cp_wp_adminbar":"n","cp_bp_adminbar":"n","cp_tinymce":"1","cp_is_mobile":"0","cp_is_touch":"0","cp_is_tablet":"0","cp_promote_reading":"1","cp_special_page":"0","cp_cookie_path":"\/cp\/","cp_multipage_page":"0","cp_toc_chapter_is_page":"0","cp_show_subpages":"0","cp_default_sidebar":"toc","cp_js_scroll_speed":"800","cp_min_page_width":"447","cp_is_signup_page":"0"};
/* ]]> */
</script>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-content/plugins/commentpress-core/themes/commentpress-theme/assets/js/cp_js_common.dev.js?ver=3.5.1'></script>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-content/plugins/commentpress-core/themes/commentpress-theme/assets/js/cp_js_form.dev.js?ver=3.5.1'></script>


<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/jquery/ui/jquery.ui.draggable.min.js?ver=1.9.2'></script>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/jquery/ui/jquery.ui.droppable.min.js?ver=1.9.2'></script>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/jquery/ui/jquery.ui.button.min.js?ver=1.9.2'></script>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/jquery/ui/jquery.ui.position.min.js?ver=1.9.2'></script>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/jquery/ui/jquery.ui.dialog.min.js?ver=1.9.2'></script>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/tinymce/plugins/wpdialogs/js/wpdialog.min.js?ver=3.5.1'></script>
<?php } ?>

<?php 
if(!empty($table)){ 
?>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-content/plugins/tablepress/js/jquery.datatables.min.js?ver=1.0'></script>
<script type="text/javascript">
jQuery(document).ready(function($){
	$('#tablepress-<?php echo $table; ?>').dataTable({"aaSorting":[],"bSortClasses":false,"asStripeClasses":['even','odd'],"bPaginate":false,"bInfo":false});
});
</script>

<?php	
}
?>





<?php if($info!=1){ ?>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-admin/js/word-count.min.js?ver=3.5.1'></script>

<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/utils.min.js?ver=3.5.1'></script>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-admin/js/editor.min.js?ver=3.5.1'></script>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/tinymce/plugins/wpdialogs/js/wpdialog.min.js?ver=3.5.1'></script>
<script type='text/javascript'>
/* <![CDATA[ */
var wpLinkL10n = {"title":"Insert\/edit link","update":"Update","save":"Add Link","noTitle":"(no title)","noMatchesFound":"No matches found."};
/* ]]> */
</script>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/wplink.min.js?ver=3.5.1'></script>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/tinymce/plugins/wpdialogs/js/popup.min.js?ver=3.5.1'></script>

	<script type="text/javascript">
		tinyMCEPreInit = {
			base : "<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/tinymce",
			suffix : "",
			query : "ver=358-23224",
			mceInit : {'comment':{mode:"exact",width:"100%",theme:"advanced",skin:"wp_theme",language:"en",spellchecker_languages:"+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv",theme_advanced_toolbar_location:"top",theme_advanced_toolbar_align:"left",theme_advanced_statusbar_location:"none",theme_advanced_resizing:true,theme_advanced_resize_horizontal:false,dialog_type:"modal",formats:{
						alignleft : [
							{selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles : {textAlign : 'left'}},
							{selector : 'img,table', classes : 'alignleft'}
						],
						aligncenter : [
							{selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles : {textAlign : 'center'}},
							{selector : 'img,table', classes : 'aligncenter'}
						],
						alignright : [
							{selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles : {textAlign : 'right'}},
							{selector : 'img,table', classes : 'alignright'}
						],
						strikethrough : {inline : 'del'}
					},relative_urls:false,remove_script_host:false,convert_urls:false,remove_linebreaks:true,gecko_spellcheck:true,fix_list_elements:true,keep_styles:false,entities:"38,amp,60,lt,62,gt",accessibility_focus:true,media_strict:false,paste_remove_styles:true,paste_remove_spans:true,paste_strip_class_attributes:"all",paste_text_use_dialog:true,webkit_fake_resize:false,spellchecker_rpc_url:"<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/tinymce/plugins/spellchecker/rpc.php",schema:"html5",wpeditimage_disable_captions:false,wp_fullscreen_content_css:"<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/tinymce/plugins/wpfullscreen/css/wp-fullscreen.css",plugins:"inlinepopups,fullscreen,wordpress,wplink,wpdialogs",content_css:"<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-content/plugins/commentpress-core/themes/commentpress-theme/assets/css/comment-form.css",elements:"comment",wpautop:true,apply_source_formatting:false,theme_advanced_buttons1:"bold,italic,underline,|,bullist,numlist,|,link,unlink,|,removeformat,fullscreen",theme_advanced_buttons2:"",theme_advanced_buttons3:"",theme_advanced_buttons4:"",tabfocus_elements:":prev,:next",body_class:"comment post-type-page",theme_advanced_resizing_use_cookie:true}},
			qtInit : {},
			ref : {plugins:"inlinepopups,fullscreen,wordpress,wplink,wpdialogs",theme:"advanced",language:"en"},
			load_ext : function(url,lang){var sl=tinymce.ScriptLoader;sl.markDone(url+'/langs/'+lang+'.js');sl.markDone(url+'/langs/'+lang+'_dlg.js');}
		};
	</script>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/tinymce/tiny_mce.js?ver=358-23224'></script>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/tinymce/wp-tinymce-schema.js?ver=358-23224'></script>
<script type='text/javascript' src='<?php echo Configure::read('Site.wordpress_url_frontend'); ?>/wp-includes/js/tinymce/langs/wp-langs-en.js?ver=358-23224'></script>

	<script type="text/javascript">
		var wpActiveEditor;

		(function(){
			var init, ed, qt, first_init, DOM, el, i, mce = 1;

			if ( typeof(tinymce) == 'object' ) {
				DOM = tinymce.DOM;
				// mark wp_theme/ui.css as loaded
				DOM.files[tinymce.baseURI.getURI() + '/themes/advanced/skins/wp_theme/ui.css'] = true;

				DOM.events.add( DOM.select('.wp-editor-wrap'), 'mousedown', function(e){
					if ( this.id )
						wpActiveEditor = this.id.slice(3, -5);
				});

				for ( ed in tinyMCEPreInit.mceInit ) {
					if ( first_init ) {
						init = tinyMCEPreInit.mceInit[ed] = tinymce.extend( {}, first_init, tinyMCEPreInit.mceInit[ed] );
					} else {
						init = first_init = tinyMCEPreInit.mceInit[ed];
					}

					if ( mce )
						try { tinymce.init(init); } catch(e){}
				}
			} else {
				if ( tinyMCEPreInit.qtInit ) {
					for ( i in tinyMCEPreInit.qtInit ) {
						el = tinyMCEPreInit.qtInit[i].id;
						if ( el )
							document.getElementById('wp-'+el+'-wrap').onmousedown = function(){ wpActiveEditor = this.id.slice(3, -5); }
					}
				}
			}

			if ( typeof(QTags) == 'function' ) {
				for ( qt in tinyMCEPreInit.qtInit ) {
					try { quicktags( tinyMCEPreInit.qtInit[qt] ); } catch(e){}
				}
			}
		})();
					(function(){var t=tinyMCEPreInit,sl=tinymce.ScriptLoader,ln=t.ref.language,th=t.ref.theme,pl=t.ref.plugins;sl.markDone(t.base+'/langs/'+ln+'.js');sl.markDone(t.base+'/themes/'+th+'/langs/'+ln+'.js');sl.markDone(t.base+'/themes/'+th+'/langs/'+ln+'_dlg.js');sl.markDone(t.base+'/themes/advanced/skins/wp_theme/ui.css');tinymce.each(pl.split(','),function(n){if(n&&n.charAt(0)!='-'){sl.markDone(t.base+'/plugins/'+n+'/langs/'+ln+'.js');sl.markDone(t.base+'/plugins/'+n+'/langs/'+ln+'_dlg.js');}});})();
			var ajaxurl = "/wp-admin/admin-ajax.php";		</script>
<?php } ?>
		<div style="display:none;">
	<form id="wp-link" tabindex="-1">
	<input type="hidden" id="_ajax_linking_nonce" name="_ajax_linking_nonce" value="d6baad1918" />	<div id="link-selector">
		<div id="link-options">
			<p class="howto">Enter the destination URL</p>
			<div>
				<label><span>URL</span><input id="url-field" type="text" name="href" /></label>
			</div>
			<div>
				<label><span>Title</span><input id="link-title-field" type="text" name="linktitle" /></label>
			</div>
			<div class="link-target">
				<label><input type="checkbox" id="link-target-checkbox" /> Open link in a new window/tab</label>
			</div>
		</div>
				<p class="howto toggle-arrow " id="internal-toggle">Or link to existing content</p>
		<div id="search-panel" style="display:none">
			<div class="link-search-wrapper">
				<label>
					<span class="search-label">Search</span>
					<input type="search" id="search-field" class="link-search-field" autocomplete="off" />
					<span class="spinner"></span>
				</label>
			</div>
			<div id="search-results" class="query-results">
				<ul></ul>
				<div class="river-waiting">
					<span class="spinner"></span>
				</div>
			</div>
			<div id="most-recent-results" class="query-results">
				<div class="query-notice"><em>No search term specified. Showing recent items.</em></div>
				<ul></ul>
				<div class="river-waiting">
					<span class="spinner"></span>
				</div>
			</div>
		</div>
	</div>
	<div class="submitbox">
		<div id="wp-link-update">
			<input type="submit" value="Add Link" class="button-primary" id="wp-link-submit" name="wp-link-submit">
		</div>
		<div id="wp-link-cancel">
			<a class="submitdelete deletion" href="#">Cancel</a>
		</div>
	</div>
	</form>
	</div>
	


<?php if(!empty($content->page->custom_fields->translation)){?>
<script type='text/javascript'>

jQuery('#searchbox_translation').submit(function() {
	var anchor = jQuery("#gotoanchorinput").val();
	var anchortype = jQuery("#anchortype").val();
	scrollToAnchor(anchortype+'_' + anchor);
	return false;
});

function scrollToAnchor(aid){
    var aTag = jQuery("[name='"+ aid +"']");
    if(typeof aTag.offset() != 'undefined'){
    	jQuery('html,body').animate({scrollTop: aTag.offset().top},700);
    	jQuery('#additional_info_search').html("");
    }else{
		jQuery('#additional_info_search').html("Not Found");
    }
}
jQuery('#searchbox_translation_sidebar').submit(function() {
	var anchor = jQuery("#gotoanchorinput_sidebar").val();
	var anchortype = jQuery("#anchortype_sidebar").val();
	scrollToAnchor(anchortype+'_' + anchor);
	return false;
});

function scrollToAnchor(aid){
    var aTag = jQuery("[name='"+ aid +"']");
    if(typeof aTag.offset() != 'undefined'){
    	jQuery('html,body').animate({scrollTop: aTag.offset().top},700);
    }else{
		jQuery('#gotoanchorinput_sidebar').val("Not Found");
    }
}



</script>
<?php } ?>
<?php if(empty($info) || $info!=1){ ?>
<script type='text/javascript'>
	jQuery('#sidebar_show_hide').click( function( event ) {
		jQuery('#sidebar_show_hide').toggleClass('sidebar_show_hide_plus');
	    // override event
	    event.preventDefault();

	    // slide all paragraph comment wrappers up
	    jQuery('#sidebar_cp').toggle();

	    // unhighlight paragraphs
	    jQuery.unhighlight_para();

	});
</script>
<?php } ?>
<script>
jQuery(document).ready(function(){

	// fade in #back-top
	jQuery(function () {
		jQuery(window).scroll(function () {
			if (jQuery(this).scrollTop() > 100) {
				
				jQuery('#back-top').fadeIn();
			} else {
				jQuery('#back-top').fadeOut();
			}
		});

		// scroll body to 0px on click
		jQuery('#back-top a').click(function () {
			jQuery('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	});

});



</script>	


</body>
</html>
