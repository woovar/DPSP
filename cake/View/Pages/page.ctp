<?php


if(isset($all_comments)){
#    var_dump($all_comments);
#	echo $all_comments;
    echo '<table class="tablesorter" id="dpspsort"><thead><tr><th class="header">Page</th><th class="header">Author</th><th class="header">Date</th><th class="header">Content</th></tr></thead><tbody>';
    foreach ($all_comments as $ac){
        #if($ac->comment_author!='Robert Knegt'){
        #    continue;
        #}
        #var_dump($ac);
        echo "<tr>";
        echo "<td>".$ac->post_title."</td>";
        echo "<td>".$ac->comment_author."</td>";
        echo "<td>".substr($ac->comment_date,0,10)."</td>";
        $string = strip_tags($ac->comment_content);

        if (strlen($string) > 100) {

            // truncate string
            $stringCut = substr($string, 0, 100);

            // make sure it ends in a word so assassinate doesn't become ass...
            $string = substr($stringCut, 0, strrpos($stringCut, ' ')).'... '; 
        }
        echo "<td><a href="."/".$ac->post_name.'#comment-'.$ac->comment_ID.">".$string."</a></td>";
        echo "</tr>";
    }
    echo "</tbody></table>";

    echo '<script type="text/javascript">$(document).ready(function() 
    { 
    $("#dpspsort").tablesorter( {sortList: [[2,1]]} ); 
    } 
    ); </script>';

}
if(isset($all_commenters)){
	echo $all_commenters;
}


if((empty($content->page->custom_fields->notitle[0]) || $content->page->custom_fields->notitle[0]!=1) && !empty($content->page->title)){
	if(isset($research_questions) && stripos($article_category,'research')===false){
		echo "<div id='page-title'>".ucfirst($article_category)." ".$content->page->title."</div>";
	}else{
		echo "<div id='page-title'>".$content->page->title."</div>";
	}
}
if(!empty($content->page->custom_fields->translation) && $content->page->custom_fields->translation){
	echo $this->element('selector');
}

echo $content->page->content;
if(isset($research_questions)){
	echo "<div id='research-questions'>Research Questions</div>";
	foreach($research_questions->categories as $cat){
		if($cat->slug !='archive' && $cat->slug !='not-applicable'){
			echo "<div class='research-question'><a href='/".$category."/r/".$cat->slug."'>".$cat->name."</a></div>";
		}
	}
}
if(isset($reviews)){
	echo "<h1>Reviews of <a href='/research/article/".$cat->post_name."'/>".$cat->post_title."</a></h1>";
	if(!empty($cat->subtitle)){
		echo "<div class='articles-subtitle'>".$cat->subtitle."</div>";
	}
	echo "by ".$cat->author_name->name;
	if(!empty($cat->expected)){
		echo "<div class='research-keywords'><strong>The full article can be expected ".$cat->expected."</strong></div>";
	}
	if(!empty($reviews)){
		if(sizeof($reviews)>1){
			$text='reviews';
		}else{
			$text='review';
		}
	}
	echo '<div class="research-author">('.count($reviews).' '.$text.')</div>';
	foreach ($reviews as $review){
		echo "<div class='review'>";
		echo "<div class='research-author'><h4>Review by ";
		echo !empty($review->url) ? "<a href='".$review->url."'>".$review->name.'</a> on '.date("d-m-Y", strtotime($review->date)).'</div>': $review->name.' on '.date("d-m-Y", strtotime($review->date)).'</div>';
		echo "</h4>";
		echo $review->content;
		echo "</div>";
		
	}
}

if(isset($research_question)){
	//debug($research_question);

	//echo "<div id='rq-title'>".$research_question->category->name."</div>";
	echo "<h1>Research question: ".$research_question->category->name."</h1>";
	
	if(!empty($research_question->category->description)){
		echo "<div id='rq-introduction'>".$research_question->category->description."</div>";
	}
	
	
	if(!empty($research_question->articles)){
		//echo "<div id='articles-submitted'>Articles submitted on this research question</div>";
		
		$i=0;
		$preprints_content="";
		foreach($research_question->articles as $cat){
			if ((strpos($cat->tags, '>in-progress<') !== FALSE || strpos($cat->tags, '>preprint<') !== FALSE) && strpos($cat->tags, '>archive<') == FALSE){
				$i++;
				$preprints_content .= $this->element('article-summary', array(
						"type" => "preprint",
						'cat'=>$cat
				));
			}

		}
		if($i>0){
			echo "<h2 class='no-margin pt15'>Articles in progress submitted on this research question</h2>";
			echo '<ul style="width:100%">';
			echo $preprints_content;
			echo '</ul>';
		}
		$j=0;
		$articles_content="";
		foreach($research_question->articles as $cat){
			if ((strpos($cat->tags, '>category_research<') !== FALSE || strpos($cat->tags, '>article<') !== FALSE) && strpos($cat->tags, '>archive<') == FALSE && strpos($cat->tags, '>in-progress<') == FALSE){
				$j++;
				$articles_content .= $this->element('article-summary', array(
						"type" => "article",
						'cat'=>$cat
				));
			}
		}
		if($j>0){
			echo "<h2 class='no-margin pt15'>Articles submitted on this research question</h2>";
			echo '<ul style="width:100%">';
			echo $articles_content;
			echo '</ul>';
		}

	}else{
		echo "<div id='no-articles-submitted'>No articles submitted on this research question, please submit an article. </div>";
	}
	if(!empty($draft_rq->page->content)){
		//echo "<div id='draft_intro'>Draft of an answer to this question, open for discussion, please comment.</div>";
		//echo "This is a draft of an answer to this question, open for discussion, please comment.";
		echo '<br>';
		echo $draft_rq->page->content;
	}
/*
	echo "<div id='submit-an-article'>";
	echo "<h3>Submit an article</h3>";
	echo "<div id='articles-submitted'>You can submit an abstract now and postpone the upload of the article. Please indicate when the article can be expected in that case (max 6 months)</div>";
	

	
	echo '<a href="'.Configure::read('Site.wordpress_research_url').'/wp-admin/post-new.php?post_type=article"><img class="alignleft" src="/img/article-category.png"/></a>Please make sure you choose the right research question. This can be chosen in the ‘name research question’ section. See picture.<br/> ';
	echo '<a href="'.Configure::read('Site.wordpress_research_url').'/wp-admin/post-new.php?post_type=article">Submit an article</a><br/><br/><br/>';
	if(!isset($_COOKIE['paulscholten_cookie'])){
		echo '<div id="contribute-title">To submit articles, please <a href="/research/wp-login.php">login</a>. New users can <a href="/research/wp-login.php?action=register">register</a> for a contributor account by clicking "register" on the login page and fill out the form. Your details and how to contribute will be mailed to your e-mail address.</div>';
	
	}	
	echo "</div>";
*/


}
#if($slug=='articles'){
#		echo "<h2 class='no-margin pt15'>Articles</h2>";
#		echo '<table>';
#		$i=0;
#		foreach($articles as $cat){
#			if (strpos($cat->tags, '>article<')!== FALSE && strpos($cat->tags, '>archive<') == FALSE){
#				$i++;
#				echo $this->element('article-summary_row', array(
#						"type" => "article",
#						'cat'=>$cat
#				));
#			}
#		}
#		echo '</table>';
#		if($i==0){
#			echo "<div id='no-articles-submitted'>No articles submitted on this research question, please submit an article. </div>";
#		}
#}


if($slug=='articles'){
		
		echo "<h2 class='no-margin pt15'>Articles</h2>";
		echo '<table id="dpspsort" class="tablesorter">';
		$i=0;
		echo '<thead>';
		echo '<th>Title</th>';
		echo '<th>Author</th>';
		echo '<th>Category</th>';
		echo '<th>Research question</th>';
		echo '<th>Review status</th>';
		//echo '<th>Edition</th>';
		//echo '<th>Content</th>';
		echo '<th>Date</th>';
		echo '<th>Download</th>';
		echo '</thead>';
		foreach($articles as $cat){
			echo '<tr>';
			if (strpos($cat->tags, '>article<')!== FALSE && strpos($cat->tags, '>archive<') == FALSE){
				$i++;
				echo $this->element('article-summary-row', array(
						"type" => "article",
						'cat'=>$cat
				));
			}
			echo '</tr>';
		}
		echo '</table>';
		echo '<script type="text/javascript" src="/js/jquery-latest.js"></script>';
		echo '<script type="text/javascript" src="/js/jquery.tablesorter.min"></script>';
		echo '<script type="text/javascript">jQuery(document).ready(function() 
    { 
        jQuery("#dpspsort").tablesorter( {sortList: [[2,1]]} ); 
    } 
); </script>';
}

if($slug=='in-progress'){
		
		echo "<h2 class='no-margin pt15'>In progress</h2>";
		echo '<table id="dpspsort" class="tablesorter">';
		$i=0;
		echo '<thead>';
		echo '<th>Title</th>';
		echo '<th>Author</th>';
		echo '<th>Category</th>';
		echo '<th>Research Question</th>';
		echo '<th>Comments</th>';
		echo '</thead>';
		foreach($articles as $cat){
			echo '<tr>';
			if (strpos($cat->tags, '>in-progress<')!== FALSE && strpos($cat->tags, '>archive<') == FALSE){
				$i++;
				echo $this->element('article-summary-row', array(
						"type" => "in-progress",
						'cat'=>$cat
				));
			}
			echo '</tr>';
		}
		echo '</table>';
		echo '<script type="text/javascript" src="/js/jquery-latest.js"></script>';
		echo '<script type="text/javascript" src="/js/jquery.tablesorter.min"></script>';
		echo '<script type="text/javascript">jQuery(document).ready(function() 
    { 
        jQuery("#dpspsort").tablesorter( {sortList: [[2,1]]} ); 
    } 
); </script>';
}

if($slug=='preprints'){
		
		echo "<h2 class='no-margin pt15'>Preprints</h2>";
		echo '<table id="dpspsort" class="tablesorter">';
		$i=0;
		echo '<thead>';
		echo '<th>Title</th>';
		echo '<th>Author</th>';
		echo '<th>Book content</th>';
		echo '<th>Status</th>';
		echo '<th>Edition</th>';
		echo '<th>Date of last submittance</th>';
		echo '</thead>';
		foreach($articles as $cat){
			echo '<tr>';
			if (strpos($cat->tags, '>preprint<')!== FALSE && strpos($cat->tags, '>archive<') == FALSE){
				$i++;
				echo $this->element('article-summary-row', array(
						"type" => "preprint",
						'cat'=>$cat
				));
			}
			echo '</tr>';
		}
		echo '</table>';
		echo '<script type="text/javascript" src="/js/jquery-latest.js"></script>';
		echo '<script type="text/javascript" src="/js/jquery.tablesorter.min"></script>';
		echo '<script type="text/javascript">jQuery(document).ready(function() 
    { 
        jQuery("#dpspsort").tablesorter( {sortList: [[2,1]]} ); 
    } 
); </script>';
}

if($slug=='drafts'){
		
		echo "<h2 class='no-margin pt15'>Drafts</h2>";
		echo '<table id="dpspsort" class="tablesorter">';
		$i=0;
		echo '<thead>';
		echo '<th>Title</th>';
		echo '<th>Author</th>';
		echo '<th>Research question</th>';
		echo '<th>Review status</th>';
		echo '<th>Edition</th>';
		echo '<th>Content</th>';
		echo '<th>Date</th>';
		echo '</thead>';
		foreach($articles as $cat){
			echo '<tr>';
			if (strpos($cat->tags, '>draft<')!== FALSE && strpos($cat->tags, '>archive<') == FALSE){
				$i++;
				echo $this->element('article-summary-row', array(
						"type" => "draft",
						'cat'=>$cat
				));
			}
			echo '</tr>';
		}
		echo '</table>';
		echo '<script type="text/javascript" src="/js/jquery-latest.js"></script>';
		echo '<script type="text/javascript" src="/js/jquery.tablesorter.min"></script>';
		echo '<script type="text/javascript">jQuery(document).ready(function() 
    { 
        jQuery("#dpspsort").tablesorter( {sortList: [[2,1]]} ); 
    } 
); </script>';
}

                                    
