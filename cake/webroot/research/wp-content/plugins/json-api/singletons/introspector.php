<?php

class JSON_API_Introspector {

	public function get_article_categories($slug) {
		$the_category=get_term_by('slug',$slug,'article_category');
		$args = array(
				'type'                     => 'page',
				'child_of'                 => $the_category->term_id,
				'parent'                   => '',
				'orderby'                  => 'name',
				'order'                    => 'ASC',
				'hide_empty'               => 0,
				'hierarchical'             => 1,
				'exclude'                  => '',
				'include'                  => '',
				'number'                   => '',
				'taxonomy'                 => 'article_category',
				'pad_counts'               => false );
		return get_categories($args);
	}

	public function get_articles_by_tag($tag) {
		$newargs = array(
 				'post_type'=>'article',
                                'posts_per_page' => 50,
				'tax_query' => array(
						array(
								'taxonomy'=>'article_tag',
								'field' => 'slug',
								'terms' => $tag
						)
				),
				
		);
		$return=query_posts( $newargs );

		//$article=query_posts( $newargs );
		foreach($return as $article){
			$article->author_name=$this->get_author_by_id($article->post_author);
			//var_dump(get_post_custom());
			$article->subtitle=get_post_meta($article->ID, '_anno_subtitle', true);
			$article->keywords=get_post_meta($article->ID, '_anno_keywords', true);
			$article->expected=get_post_meta($article->ID, '_anno_expected', true);
			$article->pdf_link=get_post_meta($article->ID, '_anno_pdf_link', true);
			$article->comments_article=get_post_meta($article->ID, '_anno_comments_article', true);
			$article->page_number=get_post_meta($article->ID, '_anno_page_number', true);
			$article->views=get_post_meta($article->ID, 'post_views_count', true);
			$article->downloads=get_post_meta($article->ID, 'post_downloads_count', true);
			if(empty($article->downloads)){
				$article->downloads=0;
			}
			$article->tags=get_the_term_list($article->ID, 'article_tag', '', ';', '');
			$article->research_question=wp_get_object_terms($article->ID, 'article_category');
			$article->research_question=$article->research_question[0];
			$article->research_question_parent=get_term_by('id', $article->research_question->parent, 'article_category');
		
			global $wpdb;
			$wp_comments = $wpdb->get_results($wpdb->prepare("
					SELECT *
					FROM $wpdb->comments
					WHERE comment_post_ID = %d
					AND comment_approved = 1
					AND comment_type = 'article_review'
					ORDER BY comment_date
					", $article->ID));
					$comments = array();
					foreach ($wp_comments as $wp_comment) {
					$comments[] = new JSON_API_Comment($wp_comment);
					}
					$article->comments=$comments;
					
		}
		return $return;
	}	
	
	public function get_article($slug) {
		$newargs = array(
				'post_type' => 'article',
				'name'=>$slug
	
		);
		$query1 = new WP_Query( $newargs );
		$article=$query1->get_posts();
		//$article=query_posts( $newargs );
		$article=$article[0];
		$article->author_name=$this->get_author_by_id($article->post_author);
		//var_dump(get_post_custom());
		$article->subtitle=get_post_meta($article->ID, '_anno_subtitle', true);
		$article->keywords=get_post_meta($article->ID, '_anno_keywords', true);
		$article->expected=get_post_meta($article->ID, '_anno_expected', true);
		$article->views=get_post_meta($article->ID, 'post_views_count', true);
		$article->tags=get_the_term_list($article->ID, 'article_tag', '', ';', '');
		
		global $wpdb;
		$wp_comments = $wpdb->get_results($wpdb->prepare("
				SELECT *
				FROM $wpdb->comments
				WHERE comment_post_ID = %d
				AND comment_approved = 1
				AND comment_type = 'article_review'
				ORDER BY comment_date
				", $article->ID));
				$comments = array();
		foreach ($wp_comments as $wp_comment) {
			$comments[] = new JSON_API_Comment($wp_comment);
		}
		$article->reviews=$comments;
		return $article;
	}
	
	public function get_article_category($slug) {
		
		$the_category=get_term_by('slug',$slug,'article_category');
// 		$loop = new WP_Query( array( 'post_type' => 'article', 'cat' => $the_category->term_id ) );
// 		$loop = new WP_Query( array( 'post_type' => 'article' ) );
// 		return $get_terms('articles');
		$newargs = array(
				'post_type' => 'article',
				'tax_query' => array(
						array(
								'taxonomy' => 'article_category',
								'field' => 'slug',
								'terms' => $the_category->slug
						)
				),
				
		);
		$return=array('articles'=>query_posts( $newargs ),'category'=>$the_category);
		foreach($return['articles'] as $article){
			$article->author_name=$this->get_author_by_id($article->post_author);
			//var_dump(get_post_custom());
			$article->subtitle=get_post_meta($article->ID, '_anno_subtitle', true);
			$article->keywords=get_post_meta($article->ID, '_anno_keywords', true);
			$article->expected=get_post_meta($article->ID, '_anno_expected', true);
			$article->views=get_post_meta($article->ID, 'post_views_count', true);
			$article->tags=get_the_term_list($article->ID, 'article_tag', '', ';', '');
			if(empty($article->views)){
				$article->views=0;
			}
			$article->downloads=get_post_meta($article->ID, 'post_downloads_count', true);
			if(empty($article->downloads)){
				$article->downloads=0;
			}
			global $wpdb;
			$wp_comments = $wpdb->get_results($wpdb->prepare("
					SELECT *
					FROM $wpdb->comments
					WHERE comment_post_ID = %d
					AND comment_approved = 1
					AND comment_type = 'article_review'
					ORDER BY comment_date
					", $article->ID));
					$comments = array();
			foreach ($wp_comments as $wp_comment) {	
				$comments[] = new JSON_API_Comment($wp_comment);
			}
					
			$article->comments=$comments;	
			
			//print_r($article);
		}
		//print_r($return);
		return $return;
// 		return $loop;
	}	
	
  public function get_posts($query = false, $wp_posts = false) {
    global $post, $wp_query;

    $this->set_posts_query($query);
    $output = array();

    while (have_posts()) {
      the_post();
      if ($wp_posts) {
        $new_post = $post;
      } else {
        $new_post = new JSON_API_Post($post);
      }
      $output[] = $new_post;
    }
    return $output;
  }
  
  public function get_date_archive_permalinks() {
    $archives = wp_get_archives('echo=0');
    preg_match_all("/href='([^']+)'/", $archives, $matches);
    return $matches[1];
  }
  
  public function get_date_archive_tree($permalinks) {
    $tree = array();
    foreach ($permalinks as $url) {
      if (preg_match('#(\d{4})/(\d{2})#', $url, $date)) {
        $year = $date[1];
        $month = $date[2];
      } else if (preg_match('/(\d{4})(\d{2})/', $url, $date)) {
        $year = $date[1];
        $month = $date[2];
      } else {
        continue;
      }
      $count = $this->get_date_archive_count($year, $month);
      if (empty($tree[$year])) {
        $tree[$year] = array(
          $month => $count
        );
      } else {
        $tree[$year][$month] = $count;
      }
    }
    return $tree;
  }
  
  public function get_date_archive_count($year, $month) {
    if (!isset($this->month_archives)) {
      global $wpdb;
      $post_counts = $wpdb->get_results("
        SELECT DATE_FORMAT(post_date, '%Y%m') AS month,
               COUNT(ID) AS post_count
        FROM $wpdb->posts
        WHERE post_status = 'publish'
          AND post_type = 'post'
        GROUP BY month
      ");
      $this->month_archives = array();
      foreach ($post_counts as $post_count) {
        $this->month_archives[$post_count->month] = $post_count->post_count;
      }
    }
    return $this->month_archives["$year$month"];
  }
  
  public function get_categories($args = null) {
    $wp_categories = get_categories($args);
    $categories = array();
    foreach ($wp_categories as $wp_category) {
      if ($wp_category->term_id == 1 && $wp_category->slug == 'uncategorized') {
        continue;
      }
      $categories[] = $this->get_category_object($wp_category);
    }
    return $categories;
  }
  
  public function get_current_post() {
    global $json_api;
    extract($json_api->query->get(array('id', 'slug', 'post_id', 'post_slug')));
    if ($id || $post_id) {
      if (!$id) {
        $id = $post_id;
      }
      $posts = $this->get_posts(array(
        'p' => $id
      ), true);
    } else if ($slug || $post_slug) {
      if (!$slug) {
        $slug = $post_slug;
      }
      $posts = $this->get_posts(array(
        'name' => $slug
      ), true);
    } else {
      $json_api->error("Include 'id' or 'slug' var in your request.");
    }
    if (!empty($posts)) {
      return $posts[0];
    } else {
      return null;
    }
  }
  
  public function get_current_category() {
    global $json_api;
    extract($json_api->query->get(array('id', 'slug', 'category_id', 'category_slug')));
    if ($id || $category_id) {
      if (!$id) {
        $id = $category_id;
      }
      return $this->get_category_by_id($id);
    } else if ($slug || $category_slug) {
      if (!$slug) {
        $slug = $category_slug;
      }
      return $this->get_category_by_slug($slug);
    } else {
      $json_api->error("Include 'id' or 'slug' var in your request.");
    }
    return null;
  }
  
  public function get_category_by_id($category_id) {
    $wp_category = get_term_by('id', $category_id, 'category');
    return $this->get_category_object($wp_category);
  }
  
  public function get_category_by_slug($category_slug) {
    $wp_category = get_term_by('slug', $category_slug, 'category');
    return $this->get_category_object($wp_category);
  }
  
  public function get_tags() {
    $wp_tags = get_tags();
    return array_map(array(&$this, 'get_tag_object'), $wp_tags);
  }
  
  public function get_current_tag() {
    global $json_api;
    extract($json_api->query->get(array('id', 'slug', 'tag_id', 'tag_slug')));
    if ($id || $tag_id) {
      if (!$id) {
        $id = $tag_id;
      }
      return $this->get_tag_by_id($id);
    } else if ($slug || $tag_slug) {
      if (!$slug) {
        $slug = $tag_slug;
      }
      return $this->get_tag_by_slug($slug);
    } else {
      $json_api->error("Include 'id' or 'slug' var in your request.");
    }
    return null;
  }
  
  public function get_tag_by_id($tag_id) {
    $wp_tag = get_term_by('id', $tag_id, 'post_tag');
    return $this->get_tag_object($wp_tag);
  }
  
  public function get_tag_by_slug($tag_slug) {
    $wp_tag = get_term_by('slug', $tag_slug, 'post_tag');
    return $this->get_tag_object($wp_tag);
  }
  
  public function get_authors() {
    global $wpdb;
    $author_ids = $wpdb->get_col("
      SELECT u.ID, m.meta_value AS last_name
      FROM $wpdb->users AS u,
           $wpdb->usermeta AS m
      WHERE m.user_id = u.ID
        AND m.meta_key = 'last_name'
      ORDER BY last_name
    ");
    $all_authors = array_map(array(&$this, 'get_author_by_id'), $author_ids);
    $active_authors = array_filter($all_authors, array(&$this, 'is_active_author'));
    return $active_authors;
  }
  
  public function get_current_author() {
    global $json_api;
    extract($json_api->query->get(array('id', 'slug', 'author_id', 'author_slug')));
    if ($id || $author_id) {
      if (!$id) {
        $id = $author_id;
      }
      return $this->get_author_by_id($id);
    } else if ($slug || $author_slug) {
      if (!$slug) {
        $slug = $author_slug;
      }
      return $this->get_author_by_login($slug);
    } else {
      $json_api->error("Include 'id' or 'slug' var in your request.");
    }
    return null;
  }
  
  public function get_author_by_id($id) {
    $id = get_the_author_meta('ID', $id);
    if (!$id) {
      return null;
    }
    return new JSON_API_Author($id);
  }
  
  public function get_author_by_login($login) {
    global $wpdb;
    $id = $wpdb->get_var($wpdb->prepare("
      SELECT ID
      FROM $wpdb->users
      WHERE user_nicename = %s
    ", $login));
    return $this->get_author_by_id($id);
  }
  
  public function get_comments($post_id) {
    global $wpdb;
    $wp_comments = $wpdb->get_results($wpdb->prepare("
      SELECT *
      FROM $wpdb->comments
      WHERE comment_post_ID = %d
        AND comment_approved = 1
        AND comment_type = ''
      ORDER BY comment_date
    ", $post_id));
    $comments = array();
    foreach ($wp_comments as $wp_comment) {
      $comments[] = new JSON_API_Comment($wp_comment);
    }
    return $comments;
  }
  
  public function get_attachments($post_id) {
    $wp_attachments = get_children(array(
      'post_type' => 'attachment',
      'post_parent' => $post_id,
      'orderby' => 'menu_order',
      'order' => 'ASC',
      'suppress_filters' => false
    ));
    $attachments = array();
    if (!empty($wp_attachments)) {
      foreach ($wp_attachments as $wp_attachment) {
        $attachments[] = new JSON_API_Attachment($wp_attachment);
      }
    }
    return $attachments;
  }
  
  public function get_attachment($attachment_id) {
    global $wpdb;
    $wp_attachment = $wpdb->get_row(
      $wpdb->prepare("
        SELECT *
        FROM $wpdb->posts
        WHERE ID = %d
      ", $attachment_id)
    );
    return new JSON_API_Attachment($wp_attachment);
  }
  
  public function attach_child_posts(&$post) {
    $post->children = array();
    $wp_children = get_posts(array(
      'post_type' => $post->type,
      'post_parent' => $post->id,
      'order' => 'ASC',
      'orderby' => 'menu_order',
      'numberposts' => -1,
      'suppress_filters' => false
    ));
    foreach ($wp_children as $wp_post) {
      $new_post = new JSON_API_Post($wp_post);
      $new_post->parent = $post->id;
      $post->children[] = $new_post;
    }
    foreach ($post->children as $child) {
      $this->attach_child_posts($child);
    }
  }
  
  protected function get_category_object($wp_category) {
    if (!$wp_category) {
      return null;
    }
    return new JSON_API_Category($wp_category);
  }
  
  protected function get_tag_object($wp_tag) {
    if (!$wp_tag) {
      return null;
    }
    return new JSON_API_Tag($wp_tag);
  }
  
  protected function is_active_author($author) {
    if (!isset($this->active_authors)) {
      $this->active_authors = explode(',', wp_list_authors(array(
        'html' => false,
        'echo' => false,
        'exclude_admin' => false
      )));
      $this->active_authors = array_map('trim', $this->active_authors);
    }
    return in_array($author->name, $this->active_authors);
  }
  
  protected function set_posts_query($query = false) {
    global $json_api, $wp_query;
    
    if (!$query) {
      $query = array();
    }
    
    $query = array_merge($query, $wp_query->query);
    
    if ($json_api->query->page) {
      $query['paged'] = $json_api->query->page;
    }
    
    if ($json_api->query->count) {
      $query['posts_per_page'] = $json_api->query->count;
    }
    
    if ($json_api->query->post_type) {
      $query['post_type'] = $json_api->query->post_type;
    }
    
    if (!empty($query)) {
      query_posts($query);
      do_action('json_api_query', $wp_query);
    }
  }
  
}

?>
