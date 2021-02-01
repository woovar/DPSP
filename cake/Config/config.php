<?php	
        Configure::write('Site', array(
        		'site_title'=>'Digital Paul Scholten Project',
        		'site_url'=>'https://paulscholten.eu',
        		'wordpress_url_frontend'=>'https://paulscholten.eu/cp',
        		'wordpress_url_backend'=>'http://paulscholten.eu/cp',
        		'wordpress_research_url'=>'http://paulscholten.eu/research',
        		'custom_fields'=>'notitle,info,translation,table,description',
        		'pages_with_cache'=>array('dutch-english','dutch-french','english-french','english-dutch','french-english','french-dutch'),
        		/*'search_code_google'=>"<script>
  (function() {
    var cx = '015163029562827424287:aposgsaqnjm';
    var gcse = document.createElement('script');
    gcse.type = 'text/javascript';
    gcse.async = true;
    gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
        '//www.google.com/cse/cse.js?cx=' + cx;
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(gcse, s);
  })();
</script>
<gcse:searchresults-only></gcse:searchresults-only>"*/
                )
        );
?>
                                
