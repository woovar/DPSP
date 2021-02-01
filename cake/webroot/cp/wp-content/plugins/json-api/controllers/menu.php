<?php
/*
Controller name: Menu
Controller description: Basic menu methods
*/

class JSON_API_Menu_Controller {
  
  public function get_menu_english() {
    global $json_api;
    $menu=$json_api->introspector->get_menu_english();
    $menu=preg_replace('/<a href\=\"http\:\/\/[^\/]+\/cp/','<a href="',$menu);
    return $menu;
  }

  public function get_menu() {
    global $json_api;
    //$tag = $json_api->introspector->get_current_tag();
    //if (!$tag) {
    //  $json_api->error("Not found..");
    //}
    $menu=$json_api->introspector->get_menu();
    
    $menu=preg_replace('/<a href\=\"http\:\/\/[^\/]+\/cp/','<a href="',$menu);
    //var_dump($menu);
    //$posts = $json_api->introspector->get_posts(array(
    //  'tag' => $tag->slug
    //));

    //return $this->posts_object_result($menu);
    return $menu;
  }

  public function get_content_sidebar() {
  	global $json_api;
  	//$tag = $json_api->introspector->get_current_tag();
  	//if (!$tag) {
  	//  $json_api->error("Not found..");
  		//}
  		$menu=$json_api->introspector->get_content_sidebar();
  
  		//$menu=preg_replace('/<a href\=\"http\:\/\/[^\/]+\/cp/','<a href="',$menu);
  		//var_dump($menu);
  		//$posts = $json_api->introspector->get_posts(array(
  		//  'tag' => $tag->slug
  		//));
  
  		//return $this->posts_object_result($menu);
  		return $menu;
  }  
  
}
?>
