<?php 
debug($cat);
echo "<li class='article-summary'>";

echo "<div class='research-answer'><a href='/research/article/".$cat->post_name."'/>".$cat->post_title."</a></div>";

if(!empty($cat->subtitle)){
	echo "<div class='articles-subtitle'>".$cat->subtitle."</div>";
}

echo "<div class='research-keywords'><strong>Author</strong>:".$cat->author_name->name."</div>";

if(!empty($cat->expected)){
	echo "<div class='research-keywords'><strong>The full article can be expected ".$cat->expected."</strong></div>";
}

//if(!empty($cat->keywords)){
	//echo "<div class='article_info'>Abstract</div>";
//	echo "<div class='research-keywords'><strong>Keywords</strong>: ".$cat->keywords."</div>";
//}
if(!empty($cat->research_question)){
	//echo "<div class='article_info'>Abstract</div>";
	
	echo "<div class='research-keywords'><strong>Research question</strong>: <a href='/".$cat->research_question_parent->slug."/r/".$cat->research_question->slug."/'>".$cat->research_question->name."</a></div>";
}
//if(!empty($cat->post_excerpt)){
	//echo "<div class='article_info'>Abstract</div>";
//	echo "<div class='research-abstract'>".$cat->post_excerpt."</div>";
//}

echo "<div class=''>";
echo "Views: ".$cat->views;
echo ", Downloads: ".$cat->downloads;
echo "</div>";
//echo "<div class='download-buttons'>";
//echo "<a href='/research/article/".$cat->post_name."'><div class='button w200'>View this ".$type."</div></a>";
//echo "<a href='/research/article/".$cat->post_name."/pdf/'><div class='button w200'>Download this ".$type."</div></a>";
/*
if(!empty($cat->comments)){
	if(sizeof($cat->comments)>1){
		$text='reviews';
	}else{
		$text='review';
	}
	echo "<a href='/reviews/".$cat->post_name."'><div class='button w200'>".count($cat->comments).' '.$text."</div></a>";
}
echo "</div></li>";
*/
?>
