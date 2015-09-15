<?php

/**
 * Class Url
 * Create and Manage beautiful clean URL with Apache and PHP
 */
class Url
{

    /**
     * @var string the request url by the user
     */
    private $request;
    /**
     * @var array of slugs and pages
     * array('slug' => 'page.php')
     */
    private $pages;
    /**
     * @var string the 404 page template that should replace the default 404 Error page
     */
    private $page404;
    /**
     * @var array containing the requested url parsed
     */
    private $path;
    /**
     * @var array containing the GET query variables
     */
    private $query;
    /**
     * @var string the base directory for the template pages
     */
    private $base;


    /**
     * @param string $base is the folder containing your pages
     * @param array $pages this is an array of the URL and the page
     *              $pages = array('slug-1' => 'page1.php','slug-2' => 'page2.php')
     *
     * @param null $page404 if left empty it will show a simple 404 error or you can link to your custom script
     */
    public function __construct($base = '', $pages = [], $page404 = null)
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $this->request = $_SERVER['REQUEST_URI'];
        }
        $this->base = $base;
        $this->pages = $pages;
        $this->page404 = $page404;
        $this->redirect();
    }

    /**
     * @return array of the requested path
     */
    public function getPath()
    {
        $path = array();
        if (isset($_SERVER['REQUEST_URI'])) {
            $requested_url = explode('?', $this->request);
            $path['base'] = dirname($_SERVER['SCRIPT_NAME']);
            $path['call'] = substr(urldecode($requested_url[0]), strlen($path['base']) + 1);
            $path['call_parts'] = explode('/', $path['call']);
        }
        $this->path = $path;
        return $this->path;
    }


    /**
     * @return array of the GET query and the variables
     */
    public function getQuery()
    {
        $requested_url = explode('?', $this->request);
        $query['query'] = urldecode($requested_url[1]);
        $vars = explode('&', $query['query']);
        foreach ($vars as $var) {
            $t = explode('=', $var);
            $query['query_vars'][$t[0]] = $t[1];
        }
        $this->query = $query;
        return $this->query;
    }


    /**
     * Redirects to a 404 page
     */
    public function redirect404()
    {
        header('HTTP/1.1 404 Not Found');
        if ($this->page404 == null) {
            die ('
    		<h1>404 Error</h1>
    		<p>
    			Sorry! File not found on server ' . htmlspecialchars(urldecode($_SERVER['REQUEST_URI'])) . '
    		</p>
    		<p>
    			Please access the <a href="/">main page of this site</a> instead.
    		</p>
    	    ');
        } else {
            include $this->base . $this->page404;
            die();
        }
    }

    /**
     * Redirects to the requested path and loads the script assigned to that page
     */
    public function redirect()
    {
        $path_call = $this->getPath();
        $file = $this->base;
        if ($path_call['call'] == null) {
            $file .= 'home.php';
        } else {
            if (array_key_exists($path_call['call'], $this->pages)) {
                $file .= $this->pages[$path_call['call']];
            } else {
                $this->redirect404();

            }
        }
        if (is_file($file)) {
            include $file;
        } else {
            $this->redirect404();
        }
    }

}
