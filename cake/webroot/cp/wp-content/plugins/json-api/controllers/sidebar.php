<?php
/*
Controller name: Sidebar
Controller description: Basic menu methods
*/

class JSON_API_Sidebar_Controller {
  

  public function get_sidebar($name=false) {
    global $json_api, $post,$id,$post_id;
    extract($json_api->query->get(array('id', 'slug')));
    
    if ($id) {
      $posts = $json_api->introspector->get_posts(array(
        'page_id' => $id
      ),true);
    } else if ($slug) {
//     	var_dump('get_sidebar in sidebar.php before get_posts');
//     	var_dump(microtime());
      $posts = $json_api->introspector->get_posts(array(
        'pagename' => $slug
      ),true);
//       var_dump($posts);
//       var_dump('get_sidebar in sidebar.php after get_posts');
//       var_dump(microtime());
    } else {
      $json_api->error("Include 'id' or 'slug' var in your request.");
    }
    if (count($posts) == 1) {
      $post = $posts[0];
      //$post->post_content='';
      //var_dump($post);
      $previous = get_adjacent_post(false, '', true);
      $next = get_adjacent_post(false, '', false);
//       var_dump('get_sidebar in sidebar.php before new JSON_API_Post($post)');
//       var_dump(microtime());      
      $post = new JSON_API_Post($post,'sidebar');
      
//       var_dump('get_sidebar in sidebar.php after new JSON_API_Post($post)');
//       var_dump(microtime());
      $post->ID=$posts[0]->ID;
//       $response = array(
//         'post' => $post
//       );
//       if ($previous) {
//         $response['previous_url'] = get_permalink($previous->ID);
//       }
//       if ($next) {
//         $response['next_url'] = get_permalink($next->ID);
//       }
//       return $response;
    } else {
      $json_api->error("Not found.");
    }
    //var_dump('sidebar.php');
    //var_dump($post);
    //$tag = $json_api->introspector->get_current_tag();
    //if (!$tag) {
    //  $json_api->error("Not found..");
    //}
    //var_dump($post);
//     var_dump('get_sidebar in sidebar.php before get_sidebar()');
//     var_dump(microtime());
    ob_start();
    $json_api->introspector->get_sidebar();
//     var_dump('get_sidebar in sidebar.php after get_sidebar()');
//     var_dump(microtime());
    //var_dump($sidebar);
    //var_dump(ob_get_clean());
    //$posts = $json_api->introspector->get_posts(array(
    //  'tag' => $tag->slug
    //));

    //return $this->posts_object_result($menu);
    return ob_get_clean();
  }
  
}

?>