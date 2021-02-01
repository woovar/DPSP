<?php
class WordpressComponent extends Component {
	

	public function getWordpressContent($query,$caching=false,$store=null,$time=null){
		if($caching && $return=Cache::read($store)){
			debug('cached');
		}else{
			$wordpress_url=Configure::read('Site.wordpress_url_backend');
			$context = stream_context_create(array('http' => array('header'=>'Connection: close',
'http' => array(
'method' => 'GET',
'header' => 'Accept-Encoding:gzip,deflate\r\n',
))));
			$return=json_decode(file_get_contents($wordpress_url.'/'.$query,false,$context));
			debug($wordpress_url.'/'.$query);
//			debug($return);
//			debug(file_get_contents($wordpress_url.$query,false,$context));
			if($store==null){
				Cache::write($query,$return);
			}else{
				if(isset($time)){
					Cache::set(array('duration' => $time));
				}
				Cache::write($store,$return);
			}
		}
		return $return;
	}

	
	public function getResearchContent($query,$caching=false){
				//var_dump($wordpress_url.'/'.$query);
		if($caching && $return=Cache::read($query) ){
				
		}else{
				$wordpress_url=Configure::read('Site.wordpress_research_url');
				$context = stream_context_create(array('http' => array('header'=>'Connection: close')));
				$return=json_decode(file_get_contents($wordpress_url.'/'.$query,false,$context));
				debug($wordpress_url.'/'.$query);
				//			debug($return);
				//			debug(file_get_contents($wordpress_url.$query,false,$context));
				Cache::write($query,$return);
		}
		return $return;		
	}
}
?>
