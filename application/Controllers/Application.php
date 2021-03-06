<?php
namespace App\Controllers;
use CodeIgniter\View\Parser;
use PHPAuth\Config as PHPAuthConfig;
use PHPAuth\Auth as PHPAuth;

/**
 * Our base controller.
 */
class Application extends \CodeIgniter\Controller
{
	protected $session; // session
	protected $data = array(); // parameters for view components
	protected $id;   // identifier for our content
	protected $isSecure = false; // set to true if login needed
	protected $auth, $authconfig;

    /**
	 * Constructor
	 */
	function __construct()
	{
		$this::init();
	}
	
	/**
	 * init
	 *
	 * @return void
	 */
	function init() {
		$this->session = \Config\Services::Session();

		$this->loader = new \CodeIgniter\Autoloader\FileLocator(new \Config\Autoload());
		$this->viewsDir = __DIR__.'/Views';
		$this->config = new \Config\App();
		$this->data	= array ();
		$this->data['title'] = 'CodeIgniter 4 Demo';
		$this->errors = array ();

		if ($this->isSecure)
		{
			// Use the new Services feature (see Services.php)
			// (Uses PDO and default db)
			$this->auth = \Config\Services::authenticator();
								
		}
	}
	/**
 	*  Check if logged in and return false or current user
	*/
	protected function getCurrentUser()
	{
    if (!$this->auth->isLogged()) {
        return false;
    }
    $userHash = $this->auth->getSessionHash();
    $userId = $this->auth->getSessionUID($userHash);
    return $user = $this->auth->getUser($userId);
}

	/**
	 * Render this page
	 */
	function render()
	{
        // header
        $d = array( 
            'pagetitle'=>isset($this->data['pagetitle'])?$this->data['pagetitle']:$this->data['title'],
            'endofheader'=>isset($this->data['endofheader'])?$this->data['endofheader']:''
        );
		echo View('theme/header', $d);

        // nav
		$choices = $this->config->menuChoices;
		foreach ($choices['menudata'] as &$menuitem)
		{
			$menuitem['active'] = (ltrim($menuitem['link'], '/ ') == uri_string()) ? 'active' : '';
		}
		echo View('theme/nav', $choices);


        // main body
        // heading optional
        $viewdata = isset($this->data['viewdata'])?$this->data['viewdata']:array();
        $d = array(
            'titleblock'=>isset($this->data['titleblock'])?$this->data['titleblock']:'', 
            'content'=>view($this->data['pagebody'], $viewdata)
            );
		echo View('theme/body', $d);
        // footer
        $d = array(
            'footerbar'=>'',
            'endofbody'=>isset($this->data['endofbody'])?$this->data['endofbody']:''
        );
		echo View('theme/footer', $d);
	}
}
/* end Application.php */