<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'Pages';
	public $profiling=false;
/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();
	public $components = array('Wordpress');
	

	public function search(){
		$this->set('title_for_layout', 'Search');
		$this->set('search_code_google', Configure::read('Site.search_code_google'));
		$this->render('search');
	}

	
	public function home(){
		$this->set('title_for_layout', Configure::read('Site.site_title'));
		
		//debug($menu);
		//debug($sidebar);
		$content = $this->Wordpress->getWordpressContent("?json=get_page&slug=title-page&custom_fields=".Configure::read('Site.custom_fields'));
		if($content->page->custom_fields->info){
			//$content->page->content=preg_replace('/<\/?span[^>]+>.*<\/span>/','',$content->page->content);
			$content->page->content=preg_replace('/<\/?span class="(para_marker|commenticonbox)">[^>]+>.*<\/span>/','',$content->page->content);
		}
		if(isset($content->page->custom_fields->description[0])){
			$this->set('description_for_layout',$content->page->custom_fields->description[0]);
		}
		$content->page->content=preg_replace('/<li class="list_commenticon">\s*<\/li>/','',$content->page->content);
		debug($content->page->custom_fields->description);
		//debug($content);
		$info=1;
		$this->set(compact('content','info'));
		$this->render('page');
	}

        public function home_english(){
                $this->set('title_for_layout', Configure::read('Site.site_title'));

                debug($menu);
                //debug($sidebar);
                $content = $this->Wordpress->getWordpressContent("?json=get_page&slug=title-page-english&custom_fields=".Configure::read('Site.custom_fields'));
                if($content->page->custom_fields->info){
                        //$content->page->content=preg_replace('/<\/?span[^>]+>.*<\/span>/','',$content->page->content);
                        $content->page->content=preg_replace('/<\/?span class="(para_marker|commenticonbox)">[^>]+>.*<\/span>/','',$content->page->content);
                }
                if(isset($content->page->custom_fields->description[0])){
                        $this->set('description_for_layout',$content->page->custom_fields->description[0]);
                }
                $content->page->content=preg_replace('/<li class="list_commenticon">\s*<\/li>/','',$content->page->content);
                debug($content->page->custom_fields->description);
                //debug($content);
                $info=1;
                $this->set(compact('content','info'));
                $this->render('page');
        }
	
	public function all_comments_sort(){
		$info=1;
		$this->set('title_for_layout','All comments - '.Configure::read('Site.site_title'));
		$all_comments=$this->Wordpress->getWordpressContent("?json=get_all_comments_json");
        $slug='all-comments';
        $tablesorter=1;
        $pagename='Comments';

		$this->set(compact('all_comments','info','pagename','slug','tablesorter'));
		$this->render('page');		
	}

	public function all_comments_old(){
		$info=1;
		$this->set('title_for_layout','All comments - '.Configure::read('Site.site_title'));
		$all_comments=$this->Wordpress->getWordpressContent("?json=get_all_comments");
		$this->set(compact('all_comments','info','pagename'));
		$this->render('page');		
	}

	public function all_commenters(){
		$info=1;
		$all_commenters=$this->Wordpress->getWordpressContent("?json=get_comments_by_commenter");
		$this->set(compact('all_commenters','info'));
		$this->render('page');
	}	
	
	public function page($category=null,$page=null){
		
		if($this->profiling==true){
			echo ("<br/>page begin<br/>");
			var_dump(microtime());
		}
		if($category!=null){
			$slug=$category.'/'.$page;
		}else{
			$slug=$page;
		}
		
		//$content_sidebar = $this->Wordpress->getWordpressContent('?json=menu.get_content_sidebar');
		//$sidebar = $this->Wordpress->getWordpressContent('?json=sidebar.get_sidebar&slug='.$page);
		if($this->profiling==true){
			echo ("<br/>wordpress begin<br/>");
			var_dump(microtime());
		}
		if(in_array($page,Configure::read('Site.pages_with_cache'))){
			if($category!=null){
				$url="?json=get_page&slug=".$category.'/'.$page.'&custom_fields='.Configure::read('Site.custom_fields');
			}else{
				$url="?json=get_page&slug=".$page.'&custom_fields='.Configure::read('Site.custom_fields');
			}
			$content = $this->Wordpress->getWordpressContent($url,true,$page);
			debug('url:');
			debug($url);
		}else{
			$content = $this->Wordpress->getWordpressContent("?json=get_page&slug=".$category.'/'.$page.'&custom_fields='.Configure::read('Site.custom_fields'));
			debug('url2:');
			debug("?json=get_page&slug=".$category.'/'.$page.'&custom_fields='.Configure::read('Site.custom_fields'));
		}
		debug($category);
		debug('Content:');
		debug($content);
		//debug($page);
		//debug($content);
		if($content->status=='error' || $content->page->content==''){
			throw new NotFoundException('Could not find the page');
		}
		
		if(isset ($content->page->title)){
			$this->set('title_for_layout',$content->page->title.' - '.Configure::read('Site.site_title'));
		}else{
			$this->set('title_for_layout',Configure::read('Site.site_title'));
		}
		$pagename=$content->page->title;
		if($this->profiling==true){
			echo ("<br/>wordpress end<br/>");
			var_dump(microtime());
		}
		//debug($content->page);
		if(isset($content->page->custom_fields->description[0])){
			$this->set('description_for_layout',$content->page->custom_fields->description[0]);
		}
		if(isset($content->page->custom_fields->info[0])){
			$info=$content->page->custom_fields->info[0];
			if($info){
				$content->page->content=preg_replace('/<\/?span class="(para_marker|commenticonbox)">[^>]+>.*<\/span>/','',$content->page->content);
			}
		}
		//debug($sidebar);
		debug($content->page->custom_fields->description);
		debug($page);
		debug($menu);

		if(isset($content->page->custom_fields->translation[0]))
		$translation=$content->page->custom_fields->translation[0];
		if(isset($content->page->custom_fields->table[0]))
		$table=$content->page->custom_fields->table[0];
		
		$this->set(compact('category','content','info','table','page','translation','pagename','slug'));	
		
		if($this->profiling==true){	
			echo ("<br/>page end<br/>");
			var_dump(microtime());
		}
		#add_action ('wp_update_nav_menu', 'emw_create_hierarchy_from_menu', 10, 2);
	}

	public function drafts(){
	
		$articles = $this->Wordpress->getResearchContent('?json=get_articles_by_tag&slug=draft')->articles;

		$this->set('title_for_layout','Drafts - '.Configure::read('Site.site_title'));
		if(empty($articles)){
			throw new NotFoundException('No drafts found');
		}
		$pagename='Drafts';
		$slug='drafts';
		$info=1;
		$this->set(compact('pagename','slug','articles','info'));
		$this->render('page');
	
	}	
	public function in_progress(){
	
		$articles = $this->Wordpress->getResearchContent('?json=get_articles_by_tag&slug=in-progress')->articles;
		debug($articles);
		$this->set('title_for_layout','Proceedings - '.Configure::read('Site.site_title'));
		if(empty($articles)){
			throw new NotFoundException('No in-progress articles found');
		}
		$pagename='In progress';
		$slug='in-progress';
		$info=1;
		$this->set(compact('pagename','slug','articles','info'));
		$this->render('page');
	}
	
	public function preprints(){
	
		$articles = $this->Wordpress->getResearchContent('?json=get_articles_by_tag&slug=preprint')->articles;
		$this->set('title_for_layout','Preprints - '.Configure::read('Site.site_title'));
		if(empty($articles)){
			throw new NotFoundException('No preprints found');
		}
		$pagename='Preprints';
		$slug='preprints';
		$info=1;
		$this->set(compact('pagename','slug','articles','info'));
		$this->render('page');
	
	}	
	public function articles(){
	
		$articles = $this->Wordpress->getResearchContent('?json=get_articles_by_tag&slug=article')->articles;
		debug($articles);
		$this->set('title_for_layout','Articles - '.Configure::read('Site.site_title'));
		if(empty($articles)){
			throw new NotFoundException('No articles found');
		}
		$pagename='Articles';
		$slug='articles';
		$info=1;
		$this->set(compact('pagename','slug','articles','info'));
		$this->render('page');
	
	}	
	
	public function reviews($page=null){
	
		$cat = $this->Wordpress->getResearchContent('?json=get_article&slug='.$page)->article;
		$reviews=$cat->reviews;
		debug($reviews);
		if(isset ($cat->post_title)){
			$this->set('title_for_layout','Reviews of "'.$cat->post_title.'" by '.$cat->author_name->name.' - '.Configure::read('Site.site_title'));
		}else{
			$this->set('title_for_layout',Configure::read('Site.site_title'));
		}
		if(!isset($cat->post_title)){
			throw new NotFoundException('Could not find the page');
		}
		$pagename=$cat->post_title;
		debug($cat);
		$slug=$cat;
		$info=1;
		$this->set(compact('pagename','slug','cat','reviews','info'));
		$this->render('page');
	
	}	
	
	public function researchquestions($category=null){
		$cat_array=explode('/',$category);
		debug($cat_array);
		$article_category=$cat_array[sizeof($cat_array)-1];
		debug($article_category);
		$content = $this->Wordpress->getWordpressContent("?json=get_page&slug=".$category.'/research-questions&custom_fields='.Configure::read('Site.custom_fields'));
		//debug($content);
		if($content->page->custom_fields->info){
			$content->page->content=preg_replace('/<\/?span class="(para_marker|commenticonbox)">[^>]+>.*<\/span>/','',$content->page->content);
		}
		if($content->status=='error'){
			throw new NotFoundException('Could not find the page');
		}
		debug($content);
		if(isset ($content->page->title)){
			$this->set('title_for_layout',ucfirst($article_category).' '.$content->page->title.' - '.Configure::read('Site.site_title'));
		}else{
			$this->set('title_for_layout', Configure::read('Site.site_title'));
		}		
		$pagename='Research questions';
		$research_questions = $this->Wordpress->getResearchContent('?json=get_article_category_index&slug='.$article_category);
		debug($research_questions);
		$info=$content->page->custom_fields->info;
		$page=$category.'/research-questions';
		$slug=$category;
		$this->set(compact('research_questions','info','page','category','content','pagename','article_category','slug'));
		$this->render('page');
		
	}
	public function researchquestion($category=null,$page=null){
		
		$research_question = $this->Wordpress->getResearchContent('?json=get_article_category&slug='.$page);
		
		if(isset ($research_question->category->name)){
			$this->set('title_for_layout',$research_question->category->name.' - '.Configure::read('Site.site_title'));
		}else{
			$this->set('title_for_layout',Configure::read('Site.site_title'));
		}
		if(!$research_question->category){
				throw new NotFoundException('Could not find the page');		
		}
		$draft_rq=$this->Wordpress->getWordpressContent("?json=get_page&slug=".$page.'&custom_fields='.Configure::read('Site.custom_fields'));
		debug($draft_rq);
		if(empty($draft_rq->page->content)){
			$info=1;
		}
		$pagename=$research_question->category->name;
 		debug($research_question);
 		$slug=$category.'/'.$page;
		$this->set(compact('research_question','category','draft_rq','page','pagename','slug','info'));
		$this->render('page');
	
	}
	public function download($ra=null){
		$this->layout='ajax';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, Configure::read('Site.wordpress_research_url')."/article/".$ra.'/pdf/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $submit_data);
		curl_setopt($session, CURLOPT_HEADER, true);
		$content = curl_exec($ch);
		//var_dump($output);
		$info = curl_getinfo($ch);
		//var_dump($info);
		curl_close($ch);
		header("Content-Type: application/pdf");
		echo $content;
		$this->set(compact('content'));
	}	
	
// 	public function register(){
// 		//$this->layout='ajax';
		
// 		$submit_data['first_name']=$this->request->data('first_name');
// 		$submit_data['last_name']=$this->request->data('last_name');
// 		$submit_data['redirect_to']='';
// 		$submit_data['user_email']=$this->request->data('user_email');
// 		$submit_data['user_login']=$this->request->data('user_login');
// 		$submit_data['wp-submit']='Register';
// 		$submit_data['action']='register';
// 		$this->layout='ajax';
// 		$ch = curl_init();
// 		curl_setopt($ch, CURLOPT_URL, Configure::read('Site.wordpress_research_url')."/wp-login.php");
// 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 		curl_setopt($ch, CURLOPT_POST, true);
// 		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// 		curl_setopt($ch, CURLOPT_POSTFIELDS, $submit_data);
// 		//curl_setopt($session, CURLOPT_HEADER, true);
// 		$content = curl_exec($ch);
// 		if(empty($content)){
// 			echo 'Account registered, you will receive an e-mail shortly';
// 		}else{
// 			echo 'Not succeeded';
// 		}
// 		$info = curl_getinfo($ch);
// 		//var_dump($info);
// 		curl_close($ch);
// 	}
	
// /**
//  * Displays a view
//  *
//  * @param mixed What page to display
//  * @return void
//  */
// 	public function display() {
// 		$path = func_get_args();

// 		$count = count($path);
// 		if (!$count) {
// 			$this->redirect('/');
// 		}
// 		$page = $subpage = $title_for_layout = null;

// 		if (!empty($path[0])) {
// 			$page = $path[0];
// 		}
// 		if (!empty($path[1])) {
// 			$subpage = $path[1];
// 		}
// 		if (!empty($path[$count - 1])) {
// 			$title_for_layout = Inflector::humanize($path[$count - 1]);
// 		}
// 		$this->set(compact('page', 'subpage', 'title_for_layout'));
// 		$this->render(implode('/', $path));
// 	}
}
?>
