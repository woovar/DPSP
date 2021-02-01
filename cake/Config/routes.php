<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
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
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	Router::connect('/', array('controller' => 'pages', 'action' => 'home'));
	Router::connect('/en', array('controller' => 'pages', 'action' => 'home_english'));
	Router::connect('/all-comments-old', array('controller' => 'pages', 'action' => 'all_comments'));
	Router::connect('/all-comments', array('controller' => 'pages', 'action' => 'all_comments_sort'));
	Router::connect('/commenters', array('controller' => 'pages', 'action' => 'all_commenters'));
	//Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
	Router::connect('/register', array('controller' => 'pages', 'action' => 'register'));
	Router::connect('/search', array('controller' => 'pages', 'action' => 'search'));
	Router::connect('/preprints', array('controller' => 'pages', 'action' => 'preprints'));
	Router::connect('/preprints-en', array('controller' => 'pages', 'action' => 'preprints'));
	Router::connect('/in-progress', array('controller' => 'pages', 'action' => 'in_progress'));
	Router::connect('/in-progress-en', array('controller' => 'pages', 'action' => 'in_progress'));
	Router::connect('/drafts', array('controller' => 'pages', 'action' => 'drafts'));
	Router::connect('/drafts-en', array('controller' => 'pages', 'action' => 'drafts'));
	Router::connect('/articles', array('controller' => 'pages', 'action' => 'articles'));
	Router::connect('/articles-en', array('controller' => 'pages', 'action' => 'articles'));
	Router::connect('/reviews-en/:cat', array('controller' => 'pages', 'action' => 'reviews'),
	array(
	'pass' => array('cat'),
	'cat' => '[\w\d\-\/]+'
			)
	);	
	Router::connect('/reviews/:cat', array('controller' => 'pages', 'action' => 'reviews'),
	array(
	'pass' => array('cat'),
	'cat' => '[\w\d\-\/]+'
			)
	);	
	Router::connect('/d/:ra', array('controller' => 'pages', 'action' => 'download'),
	array(
	'pass' => array('ra'),
	'ra' => '[\w\d\-]+',
	)
	);	
	
	Router::connect('/:category/r/:page', array('controller' => 'pages', 'action' => 'researchquestion'),
	array(
	'pass' => array('category','page'),
	'category' => '[\w\d\-\/]+',
	'page'=>'[\w\d\-§]+',
	)
	);	
	
	Router::connect('/:category/research-questions', array('controller' => 'pages', 'action' => 'researchquestions'),
	array(
	'pass' => array('category'),
	'category' => '[\w\d\-\/]+'
			)
	);	

        Router::connect('/:category/research-questions-en', array('controller' => 'pages', 'action' => 'researchquestions'),
        array(
        'pass' => array('category'),
        'category' => '[\w\d\-\/]+'
                        )
        );

	Router::connect('/:category/:page/', array('controller' => 'pages', 'action' => 'page'),
	array(
	'pass' => array('category','page'),
	'category' => '[\w\d\-\/]+',
	'page' => '[\w\d\-]+'
			)
	);

	
	Router::connect('/:page', array('controller' => 'pages', 'action' => 'page'),
	array(
	'pass' => array('page'),
	'page' => '[\w\d\-]+'
			)
	);
	

	Router::connect('/*', array('controller' => 'pages', 'action' => 'page'));
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'),
		array(
			'pass' => array('page'),
			'page' => '[\w\d\-]+'
		)
	);
	

	
/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
