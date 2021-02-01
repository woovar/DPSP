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
<?php //cfct_misc('tools-nav'); ?>
<?php
          setCrunchifyPostViewsPDF(get_the_ID());
?>
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
?>

<article <?php post_class('article-full'); ?>>
	<header class="header">
		<div class="entry-title">
			<h1 class="title"><a rel="bookmark" href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>
			<?php if (anno_has_subtitle()): ?>
				<p class="subtitle"><?php anno_the_subtitle(); ?></p>
			<?php endif; ?>
		</div>
			<?php if (anno_has_expected()): ?>
				<p class="sec">Full article can be expected: <?php anno_the_expected(); ?></p>
			<?php endif; ?>
		<div class="sec sec-authors">
			<span class="authors nav">
				<?php $ata = anno_the_authors();echo $ata;  ?>
			</span>
		</div>
		<div class="sec sec-citation">
			<?php //anno_the_citation(); ?>
		</div>
		<div class='dpsp' style='font-family: "Times New Roman", Times, serif;line-height: 1.5;'>
		<table>
		<tr> 
			<td><a href="https://paulscholten.eu"><img width="75" height="78" src="https://paulscholten.eu/cp/wp-content/uploads/2013/02/dpsp1.png"></a></td>
			<td style='width:300px;padding-top:20px;line-height: 1.5;'><b>DPSP Annual</b><br><br>
			        <?php
                                if(strpos(get_the_term_list($post_id, 'article_tag', '', ';', ''), 'abstract')){
                                        echo '<b>Abstract only</b>';
                                }
                                ?>
			<b><?php echo anno_the_volume(); ?></b></td>
		</tr>
		</table>
		</div>
	</header>
	<div class="main">
		<div class="content entry-content">
			<?php if (has_excerpt()): ?>
				<section class="sec abstract">
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
				<?php if (anno_has_keywords()): ?>
					<div class='keywords nobreak'>
						<h1>Keywords</h1>
						<?php anno_the_keywords(); ?>
					</div>
				<?php endif; ?>
				<?php 
				if(!strpos(get_the_term_list($post_id, 'article_tag', '', ';', ''), 'category_editor')){
				?>
				<div class='keywords nobreak'>
				<h1>Cite as</h1>
				<?php anno_the_citation(); ?>
				</div>
				<?php } ?>	
			<?php endif; ?>

                <section class="sec sec_abstract nobreak" id='article-info'>
                        <h1>Article Info</h1>
                        <?php
                                $the_category=wp_get_object_terms($post_id,'article_category');
                                if(isset($the_category[0]) && $the_category[0]->name!='not applicable'){
                        ?>
                        <div>
			<?php anno_the_terms('article_category', '<span class="article-categories">Research question <span class="sep">&middot;</span> ', ',', '</span>'); ?>
                        </div>
			<?php } ?>
			<?php if (sizeof($comments)>0) { ?>
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
			if(strpos(get_the_term_list($post_id, 'article_tag', '', ';', ''), 'abstract')){
				echo 'Abstract only';
			}
 			?>
			</div>
			<div>
			<?php if (anno_has_expected()): ?>
                                Full article can be expected: <?php anno_the_expected(); ?>
                        <?php endif; ?>
			</div>
                        <div>
                        <?php echo anno_the_volume();?>
			<br/><br/>
                        </div>
                </section>
			<hr>
			<?php if (anno_has_funding_statement()): ?>
				<section class="sec" id="funding-statement">
					<h1><span><?php _e('Funding Statement', 'anno'); ?></span></h1>
					<?php anno_the_funding_statement(); ?>
				</section>
			<?php endif; ?>
			<div class='the_content'>
			<?php
			$content=get_the_content();

                                #$content = preg_replace('/<sup>([^<]+)<\/sup>/','<a href="#$1">[$1]</a>',$content);

                                #$content = preg_replace('/(\d+)<sup><\/sup>/','<a name="$1" href="#$1">[$1]</a>',$content);
                                #$content = preg_replace('-X-','&nbsp;&nbsp;&nbsp',$content);


                                #$content = preg_replace('/([^\'"])(https?:\/\/)([\da-z\.-]+)\.([a-z\.]{2,6})([\/\d-\+=&;%@.\w_?#]*)/i','$1<a href="$2$3.$4$5">$2$3.$4$5</a>',$content); //JVDP
                                $content = preg_replace('/([^>])\[(\d+)\]/','$1 <a href="#asdf$2"><sup>$2</sup></a>',$content); //JVDP
				$content = preg_replace('/(>)\[(\d+)\]/','><a name="asdf$2">[$2]</a>',$content);
/*
                               $content = preg_replace('/\>\[(\d+)\]/','<a name="_$1">[$1]</a>',$content);

                                #$content = preg_replace('/<sup>([^<]+)<\/sup>/','<a href="#$1">[$1]</a>',$content);

                                #$content = preg_replace('/(\d+)<sup><\/sup>/','<a name="$1" href="#$1">[$1]</a>',$content);
                                $content = preg_replace('-X-','&nbsp;&nbsp;&nbsp',$content);


                                $content = preg_replace('/([^\'"])(https?:\/\/)([\da-z\.-]+)\.([a-z\.]{2,6})([\/\d-\+=&;%@.\w_?#]*)/i','$1<a href="$2$3.$4$5">$2$3.$4$5</a>',$content); //JVDP
                                $content = preg_replace('/([^\]])\[(\d+)\]/','$1<a style="vertical-align: super;font-size: smaller;" href="#_$2">$2</a>',$content); //JVDP
*/

				$content = preg_replace('/<\/em>/','</i>',preg_replace('/<em>/','<i>',$content));//JVDP
				$occ_nl = strpos($content,'</p>',100);
				echo "<span style='dipslay:inline;page-break-inside:avoid;'>".substr($content,0,$occ_nl)."</span>";
				echo "<span style='display:inline;'>".substr($content,$occ_nl,strlen($content))."</span>";
			wp_link_pages();
			?>
			</div>
			<?php if (anno_has_acknowledgements()): ?>
				<section class="sec" id="acknowledgements">
					<h1><span><?php _e('Acknowledgements', 'anno'); ?></span></h1>
					<?php anno_the_acknowledgements(); ?>
				</section>
			<?php endif; ?>
			<?php anno_the_appendices(); ?>
			<?php anno_the_references(); ?>
		</div><!--/.content-->
	</div><!--/.main-->
</article>
