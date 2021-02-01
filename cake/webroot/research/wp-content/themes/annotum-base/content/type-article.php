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
<?php
          setCrunchifyPostViews(get_the_ID());
?>
<?php //cfct_misc('tools-nav'); ?>

<?php 
			$post_id = get_the_ID();
			#var_dump(get_comments($post_id));
			global $wpdb;
			$comments = $wpdb->get_results($wpdb->prepare("
      SELECT *
      FROM $wpdb->comments
      WHERE comment_post_ID = %d
        AND comment_approved = 1
        AND comment_type = 'article_review'
      ORDER BY comment_date
    ", $post_id));
			
			$the_category=wp_get_object_terms($post_id,'article_category');
			//var_dump($the_category);
			if(isset($the_category[0])){
				$parent= get_term_by( 'id', $the_category[0]->parent,'article_category');
				
				$link=$parent->slug;
				
				if($parent->parent!=0){
					$grandparent= get_term_by( 'id', $parent->parent,'article_category');
					$link=$grandparent->slug.$link;
				}
			}else{
				$link='';
			}
			
			//echo '<br/><br/>';
			//var_dump($parent);
			if(empty($link)){
				if(empty($the_category[0]) || $the_category[0]->slug=='not-applicable'){
				echo '&nbsp;&nbsp;<div class="back-bread"><a href="/">
						Back
	 		    </a></div><br/><br/><br/><br/><br/><br/>';//JVDP
				}else{
					echo '&nbsp;&nbsp;<div class="back-bread"><a href="/r/'.$the_category[0]->slug.'">
						Back
	 		    </a></div><br/><br/><br/><br/><br/><br/>';//JVDP
				}
			}else{
				echo '&nbsp;&nbsp;<div class="back-bread"><a href="/'.$link.'/r/'.$the_category[0]->slug.'">
 		       Back
 		    </a></div><br/><br/><br/><br/><br/><br/>';//JVDP
				}
			?>
<article <?php post_class('article-full'); ?>>

	<header class="header">
		<div class="entry-title">
		
			<table class='table_header'>
			<tr>
				<td class='table_header'>
				
				<?php //var_dump(get_term_by('slug','general-issues','article_category')); ?>
				<img width="172" height="184" src="https://paulscholten.eu/cp/wp-content/uploads/2013/02/dpsp1.png"></td>
			
				<td><h1 class="title"><a rel="bookmark" contentType='book' pageType='book' href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>
							<?php 
			#foreach(anno_the_comments() as $c){
			#	var_dump($c);
			#}
			if (anno_has_subtitle()){ ?>
				<p class="subtitle"><?php anno_the_subtitle(); ?></p>
			<?php } ?>
					<span style="font-size:14px;">
					DPSP Annual
		<?php
$posttags = get_the_term_list(get_the_ID(), 'article_tag', '', ';', '');//JVDP

if(strpos(get_the_term_list($post_id, 'article_tag', '', ';', ''), 'abstract')){
	echo 'Abstract only';
}elseif (strpos($posttags, 'in-progress') == TRUE){
	echo 'Article in Progress';
}elseif (anno_the_volume()!=''){
	echo anno_the_volume(); 
}
?>
<div>
Online ISSN: eISSN 2667-2790
</div>
				</span>
				</td>
			</tr>
			</table>


			
		</div>
					<?php if (anno_has_expected()): ?>
				<span class="meta">Full article can be expected: <?php anno_the_expected(); ?></span>
			<?php endif; ?>


		<?php
			if (strpos($posttags, 'in-progress') == FALSE){
				cfct_misc('tools-bar');
			}
 			if (false && function_exists('annowf_get_clone_dropdown')) {
				$markup = annowf_get_clone_dropdown(get_the_ID());
				if (!empty($markup)) {
		?>
			<div class="sec sec-revisions">
				<span class="title"><span><?php _e('Revisions', 'anno'); ?></span></span>
				<?php _e('This article is either a revised version or has previous revisions', 'anno'); ?>
				<?php echo $markup; ?>
			</div>
		<?php
				}
			}
		
		?>
		<div class="sec sec-authors">
			<span class="title"><span><?php echo _n( 'Author', 'Authors', anno_num_authors(get_the_ID()), 'anno' ) ?></span></span>
			<ul class="authors nav">
				<?php $ata = anno_the_authors(); echo $ata; ?>
			</ul>
		</div>
		


	</header>
	<div class="main">
		<div class="content entry-content">
				
			
			<?php if (anno_has_keywords()): ?>
				<section class="sec" id='keywords-html'>
					<h1><span>Keywords</span></h1>
					<?php anno_the_keywords(); ?>
				</section>
			<?php endif; ?>
			
                <section class="sec sec-authors">
                        <h1><span>Article Info</span></h1>
			<?php
				if(strpos(get_the_term_list($post_id, 'article_tag', '', ';', ''), 'category_new_translation')){
					echo '<div>Category: new translation</div>';
				}
				if(strpos(get_the_term_list($post_id, 'article_tag', '', ';', ''), 'category_edited_reissue')){
					echo '<div>Category: edited reissue</div>';
				}
				if(strpos(get_the_term_list($post_id, 'article_tag', '', ';', ''), 'category_research')){
					echo '<div>Category: research</div>';
				}
				if(strpos(get_the_term_list($post_id, 'article_tag', '', ';', ''), 'category_editor')){
					echo '<div>Category: editor</div>';
				}
				$the_category=wp_get_object_terms($post_id,'article_category');
				if(isset($the_category[0]) && $the_category[0]->name!='not applicable'){
			?>
			<!--<time class="published" pubdate datetime="<?php the_time('c'); ?>"><?php the_time('F j, Y'); ?></time>-->
				<div>Research Question:
				<?php 
					echo $the_category[0]->name;
				?>
				</div>
				<div>
                        	<?php if (anno_has_expected()): ?>
                                	Full article can be expected: <?php anno_the_expected(); ?>
                        	<?php endif; ?>
				</div>
			<?php } ?>
			<?php if (sizeof($comments)>0 && (!strpos(get_the_term_list($post_id, 'article_tag', '', ';', ''), 'review na'))){ ?>
                                <div>Reviewed by:
                                <?php
                                $authors=array();
                                foreach ($comments as $c){
                                        if($c->comment_author != get_author_name() && $c->comment_author != "author's response" &&  $c->comment_author != "reaction to revision"){
                                                array_push($authors, $c->comment_author);
                                        }
                                }
                                echo implode(', ', $authors);
                                ?>

                                </div>
                        <?php } ?>
			<div> 
			<?php 
			if(strpos(get_the_term_list($post_id, 'article_tag', '', ';', ''), 'category_editor')){
                        	echo anno_the_volume();
			}elseif(!strpos(get_the_term_list($post_id, 'article_tag', '', ';', ''), 'abstract') && !strpos(get_the_term_list($post_id, 'article_tag', '', ';', ''), 'in-progress')){
				echo 'Cite as: ';
				echo anno_the_citation();
			} 
if(strpos(get_the_term_list($post_id, 'article_tag', '', ';', ''), 'abstract')){
	echo 'Abstract only';
}elseif (strpos($posttags, 'in-progress') == TRUE){
	echo 'Article in Progress';
}
			?>
			
			</div>
		</section>
			<?php if (has_excerpt()): ?>
				<section class="sec sec_abstract">
					<?php 
					if(strpos(get_the_term_list($post_id, 'article_tag', '', ';', ''), 'category_new_translation')){
						echo "<h1><span id='abstract'>Foreword by editor</span></h1>";
					}elseif (strpos(get_the_term_list($post_id, 'article_tag', '', ';', ''), 'category_edited_reissue')){
						echo "<h1><span id='abstract'>Foreword by editor</span></h1>";	
					}else{
					?>
					<h1><span id='abstract'><?php _e('Abstract', 'anno'); ?></span></h1>
					<?php } ?>
					<?php the_excerpt(); ?>
				</section>
			<?php endif; ?>
			<?php if (anno_has_funding_statement()): ?>
				<section class="sec" id="funding-statement">
					<h1><span><?php _e('Funding Statement', 'anno'); ?></span></h1>
					<?php anno_the_funding_statement(); ?>
				</section>
                        </ul>
                </div>
			<?php endif; ?>

<div id="toc"></div>
			<?php
			$content = get_the_content();
			//$content = preg_replace('/<h2(.*)<h2/s','<p><h2$1</p><h2', $content);
			#$content = preg_replace('/<h2(.*?)<h2/s','<div style="page-break-inside:avoid;break-inside:avoid;"><h2$1</div><h2', $content);
			#$content = preg_replace('/<h2(.{500})/s','<span style="page-break-inside:avoid;break-inside:avoid;"><h2$1</span>', $content);
			#$content = apply_filters('the_content', $content);
			/*if(!empty($content)){
				echo '<section class="sec" id="preprint">
					<h1><span></span></h1>
 					</section>';
			}*/

			$posttags = get_the_term_list($post_id, 'article_tag', '', ';', '');
			if ((strpos($posttags, 'translation') !== FALSE) || (strpos($posttags, 'edited_reissue') !== FALSE)){
				$content=preg_replace('/<p>|<\/p>/','',$content);
				echo '<div class="standalone_div original_text translation">'.$content."</div>";//JVDP
			}else{
 				#$content = preg_replace('/"\s+title="">([^<]+)<\/a>/',' ($1)',$content);
 				#$content = preg_replace('/<a href="/','',$content);
				
				$content = preg_replace('/<p>\[(\d+)\]/','<p><a name="$1">[$1]</a>',$content);
				
				#$content = preg_replace('/<sup>([^<]+)<\/sup>/','<a href="#$1">[$1]</a>',$content);
				
				#$content = preg_replace('/(\d+)<sup><\/sup>/','<a name="$1" href="#$1">[$1]</a>',$content);
				$content = preg_replace('-X-','&nbsp;&nbsp;&nbsp',$content);
				
				
				$content = preg_replace('/([^\'"])(https?:\/\/)([\da-z\.-]+)\.([a-z\.]{2,6})([\/\d-\+=&;%@.\w_?#]*)/i','$1<a href="$2$3.$4$5">$2$3.$4$5</a>',$content); //JVDP
				$content = preg_replace('/([^\]])\[(\d+)\]/','$1<a style="vertical-align: super;font-size: smaller;" href="#$2">$2</a>',$content); //JVDP
				echo $content;
			}
			
			wp_link_pages();
			?>

			<?php if (anno_has_acknowledgements()): ?>
				<section class="sec" id="acknowledgements">
					<h1><span><?php _e('Acknowledgements', 'anno'); ?></span></h1>
					<?php anno_the_acknowledgements(); ?>
				</section>
			<?php endif; ?>
			<?php anno_the_appendices(); ?>
			<?php anno_the_references(); ?>
			<?php if (!empty($comments)): ?>
				<section class="sec standalone_div" id="comments">
                                <h1><span><?php 
				if (!strpos(get_the_term_list($post_id, 'article_tag', '', ';', ''), 'review na')){
					_e('Reviews', 'anno');
				}else{
					echo 'Appendix';
				}
				 ?>

				</span></h1>
				<?php foreach($comments as $comment){
					#var_dump($comment);
					echo '<h3>'.$comment->comment_author.'</h3>';
					$cont = $comment->comment_content;
					$cont = preg_replace('/-X-/','&nbsp;&nbsp;&nbsp',$cont);
					$cont = preg_replace('/-#--#-/','<span class="newline"></span>',$cont);
					$cont = preg_replace('/-#-/','<br>',$cont);
					echo($cont);
				}
				?>
			<?php endif; ?>
		</div><!--/.content-->
	</div><!--/.main-->
	<footer class="footer">
		<?php
		//anno_the_terms('article_tag', '<dl class="kv"><dt>'.__('Tags:', 'anno').'</dt> <dd class="tags">', ' <span class="sep">&middot;</span> ', '</dd></dl>'); 
		//the_tags('<dt>'.__('Tags:', 'anno').'</dt> <dd class="tags">', ' <span class="sep">&middot;</span> ', '</dd>'); ?>
	</footer><!-- .footer -->
</article>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-46034732-1', 'auto');
  ga('send', 'pageview');

</script>
