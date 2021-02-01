<?php 
debug($cat);
echo '<td>'."<a href='/research/article/".$cat->post_name."'>".$cat->post_title.'</a></td>';
echo '<td>'.$cat->author_name->last_name.'</td>';

if(preg_match('/category_(\w+)/', $cat->tags, $re)){
//	echo $re[0];
  //      echo preg_replace('/Category_/','',ucfirst($re[0]));
        echo '<td>'.preg_replace('/_/','',preg_replace('/Category_/','',ucfirst($re[0]))).'</td>';
}else{
echo '<td></td>';
}
echo "<td>";
if(!empty($cat->research_question_parent->slug)){
	echo "<a href='/".$cat->research_question_parent->slug."/r/".$cat->research_question->slug."/'>".$cat->research_question->name."</a>";
}elseif($re[0]=='category_research' && !empty($cat->research_question->name)){
	echo $cat->research_question->name;
}else{
	echo "Not applicable";
}
echo "</td>";
if($slug=='in-progress'){
echo '<td>';
	echo $cat->comments_article;
echo '</td>';
}
if($slug=='articles'){
echo '<td>';
if(!empty($cat->comments) && $type=='article' && !strpos($cat->tags, '>review na<')!== FALSE){
	echo "<a href='/reviews-en/".$cat->post_name."'>";
}
if($type=='article'){
if(strpos($cat->tags, '>awaiting review<')!== FALSE){
	echo 'awaiting review';
}elseif(strpos($cat->tags, '>published<')!== FALSE){
	echo 'published';
}elseif(strpos($cat->tags, '>reviewed<')!== FALSE){
	echo 'reviews';	
}elseif(strpos($cat->tags, '>awaiting editing<')!== FALSE){
	echo 'awaiting editing';	
}elseif(strpos($cat->tags, '>review na<')!== FALSE){
	echo 'not applicable';	
}elseif(strpos($cat->tags, '>awaiting author comment<')!== FALSE){
	echo 'reviewed, awaiting comment of author';	
}elseif(strpos($cat->tags, '>revision in progress<')!== FALSE){
	echo 'revision in progress';	
}elseif(strpos($cat->tags, '>new edition<')!== FALSE){
	echo 'new edition, awaiting comments of reviewers';	
}elseif(strpos($cat->tags, '>none<')!== FALSE){
	echo 'none';	
}else{
}
if(!empty($cat->comments)){
	echo "</a>";
}
}elseif($slug=='in-progress'){
if(strpos($cat->tags, '>abstract<')!== FALSE){
	echo 'abstract only';
}elseif(strpos($cat->tags, '>concept<')!== FALSE){
	echo 'concept';
}elseif(strpos($cat->tags, '>edited<')!== FALSE){
	echo 'edited';
}elseif(strpos($cat->tags, '>ready to publish<')!== FALSE){
	echo 'ready to publish';
}elseif(strpos($cat->tags, '>editing in progress<')!== FALSE){
	echo 'editing in progress';
}elseif(strpos($cat->tags, '>awaiting review<')!== FALSE){
	echo 'awaiting review';
}elseif(strpos($cat->tags, '>published<')!== FALSE){
	echo 'published';
}elseif(strpos($cat->tags, '>reviewed<')!== FALSE){
	echo 'reviews';	
}elseif(strpos($cat->tags, '>awaiting editing<')!== FALSE){
	echo 'awaiting editing';	
}elseif(strpos($cat->tags, '>review na<')!== FALSE){
	echo 'not applicable';	
}elseif(strpos($cat->tags, '>awaiting author comment<')!== FALSE){
	echo 'reviewed, awaiting comment of author';	
}elseif(strpos($cat->tags, '>revision in progress<')!== FALSE){
	echo 'revision in progress';	
}elseif(strpos($cat->tags, '>new edition<')!== FALSE){
	echo 'new edition, awaiting comments of reviewers';	
}elseif(strpos($cat->tags, '>none<')!== FALSE){
	echo 'none';	
}
}
if(!empty($cat->comments)){
	echo "</a>";
}
echo '</td>';
debug(preg_match('/edition (\d+)/', $cat->tags, $re));
debug($cat->tags);
if(preg_match('/edition (\d+)|concept|abstract/', $cat->tags, $re)){
	//echo '<td>'.ucfirst($re[0]).'</td>';
}else{
	//echo '<td></td>';
}

if($slug!='preprints' && $slug!='in-progress'){
    //echo '<td>';
    //echo empty($cat->expected)? 'Full article':'Abstract only';
    //echo '</td>';
}

if($slug!='in-progress'){
if(preg_match('/volume_(\d+)_\((\d+)\)/', $cat->tags, $re)){
        echo '<td>'.ucfirst($re[2]).'</td>';
}else{
	echo '<td></td>';
}
}

if($slug=='in-progress'){
echo '<td>';
if(strpos($cat->tags, '>awaiting review<')!== FALSE){
	echo 'awaiting review';
}elseif(strpos($cat->tags, '>published<')!== FALSE){
	echo 'published';
}elseif(strpos($cat->tags, '>reviewed<')!== FALSE){
	echo 'reviews';	
}elseif(strpos($cat->tags, '>awaiting editing<')!== FALSE){
	echo 'awaiting editing';	
}elseif(strpos($cat->tags, '>awaiting response author<')!== FALSE){
	echo 'awaiting response author';	
}elseif(strpos($cat->tags, '>review na<')!== FALSE){
	echo 'not applicable';	
}elseif(strpos($cat->tags, '>awaiting author comment<')!== FALSE){
	echo 'reviewed, awaiting comment of author';	
}elseif(strpos($cat->tags, '>revision in progress<')!== FALSE){
	echo 'revision in progress';	
}elseif(strpos($cat->tags, '>new edition<')!== FALSE){
	echo 'new edition, awaiting comments of reviewers';	
}elseif(strpos($cat->tags, '>none<')!== FALSE){
	echo 'none';	
}else{
}
if(!empty($cat->comments)){
	echo "</a>";
}
}
echo '</td>';
}
if($slug!='in-progress'){
if($cat->pdf_link!==''){
	echo '<td><a href="'.$cat->pdf_link.'">PDF</a></td>';
}else{
	echo '<td><a href="/research/article/'.$cat->post_name.'/pdf/">PDF</a></td>';
}
}
?>
