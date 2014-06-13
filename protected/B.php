<?php
/**
 * Gets the application start timestamp.
 */
defined('BEGIN_TIME') or define('BEGIN_TIME',microtime(true));

/**
 *
 * The web container of this application
 */
class B
{
	public $view;
    public $db;
    
    public __construct()
	{
        init();
	}
    
    private function init()
    {
        init_view();
    }
    /*
     * init smarty
     */
    private function init_view()
    {
        require '../lib/smarty/Smarty.class.php';
        $this->view = new Smarty;
        //$smarty->force_compile = true;
        $view->debugging = true;
        $view->caching = true;
        $view->cache_lifetime = 120;
    }

    /*
     *init db connection
     */
    private function init_db()
    {


    }
}