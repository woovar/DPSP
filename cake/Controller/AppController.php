<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	public $components = array('Wordpress');
	public function beforeFilter(){
                if( preg_match('/.*[-\/]en\/.*/', Router::url( $this->here, true )) | parse_url($this->here, PHP_URL_PATH)=='/'){
			$menu = $this->Wordpress->getWordpressContent('?json=menu.get_menu_english',true,'menu-english','1 minutes');
		}else{
			$menu = $this->Wordpress->getWordpressContent('?json=menu.get_menu',true,'menu','1 minutes');
		}
		$menu = preg_replace('/\/cp\//','/',$menu);
		$this->set(compact('menu'));
	}
}

