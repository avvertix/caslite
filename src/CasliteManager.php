<?php

namespace Avvertix\Caslite;

use Illuminate\Support\Manager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use phpCAS;
use Config;
use Illuminate\Support\Facades\Session;

class CasliteManager extends Manager implements Contracts\Factory
{
    /**
     * Init CASAuthentication
     *
     * @param $config
     * @param AuthManager $auth
     */
    public function __construct($app)
    {
        parent::__construct($app);
        
        $this->config = $this->app['config']['services.cas'];;
        $this->initialize();
    }
    
    
    /**
     * Redirect the user to the authentication page for the provider.
     * When the authentication is performed the callback url is invoked. 
     * In that callback you can process the User and create a local entry 
     * in the database
     *
     * @param string $callback_url the url to invoke when the authentication is completed
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function authenticate($callback_url = '/auth/cas/callback'){
        
        // perform phpCas force authentication, redirect page set to /auth/cas/callback
        
        if(!phpCAS::isAuthenticated()){
            phpCAS::forceAuthentication(url('/auth/cas/callback'));
        }
        else {
           return new RedirectResponse(url('/auth/cas/callback')); 
        }
        
    }

    /**
     * Get the User instance for the authenticated user.
     *
     * @return \Avvertix\Caslite\Contracts\User
     */
    public function user(){
        
        $auth = phpCAS::checkAuthentication();
        
        $this->setUser();
        
        return (new CasUser)->setRaw($this->cas_user);
    }
    
    public function check(){
        return $this->isAuthenticated();
    }
    
    
    /**
     * This method is used to logout from CAS
     *
     * @param array ['url' => 'http://...'] || ['service' => ...]
     *
     * @return none
     */
    public function logout($params = [])
    {
        if (!phpCAS::isAuthenticated()) {
            $this->initialize();
        }

        // if ($this->auth->check()) {
        //     $this->auth->logout();
        // }

        // Session::flush();
        phpCAS::logout($params);
    }
    
    public function getDefaultDriver(){
        return $this;
    }
    
    
    // Private stuff
    
    private $config = null;
    private $cas_user = null;
    private $cas_client = null;
    
    /**
     * Initialize phpCAS
     */
    private function initialize()
    {

        session_name($this->config['cas_session_name']);
        
        // set debug
        $this->setDebug();

        // configure CAS client
        phpCAS::client(
            $this->config['cas_saml'] ? SAML_VERSION_1_1 : CAS_VERSION_2_0,
            $this->config['cas_hostname'],
            intval($this->config['cas_port']),
            $this->config['cas_uri'],
            true
        );

        // configure certificate
        $this->configureCertificate();

        // handle logout requests
        phpCAS::handleLogoutRequests(false);

        // set login and logout URL's
        phpCAS::setServerLoginURL($this->config['cas_login_uri']);
        phpCAS::setServerLogoutURL($this->config['cas_logout_uri']);

    }

    /**
     * Set phpCAS Debug
     */
    private function setDebug()
    {
        $debug = !!$this->config['cas_debug'];
        
        if ($debug) {
            phpCas::setDebug(storage_path('logs/cas.log'));
        }
    }

    /**
     * SSL Validation
     */
    private function configureCertificate()
    {
        if ($this->config['cas_validation'] == 'self') {
            phpCAS::setCasServerCert($this->config['cas_cert']);
        } else {
            if ($this->config['cas_validation'] == 'ca') {
                phpCAS::setCasServerCACert($this->config['cas_cert']);
            }
            phpCAS::setNoCasServerValidation();
        }
    }

    /**
     * Set currently logged in user and create session
     */
    private function setUser()
    {
        try{
            $this->cas_user = phpCAS::getUser();
            Session::put('cas_user', $this->cas_user);
        }catch(\CAS_OutOfSequenceBeforeAuthenticationCallException $cas_ex){
            \Log::error('CAS Get user error', ['error' => $cas_ex]);
            return null;
        }
        
    }

    /**
     * Checks to see is user is authenticated
     *
     * @return bool
     */
    protected function isAuthenticated()
    {
        if (phpCAS::isAuthenticated()) {
            $this->setUser();

            return true;
        }

        return false;
    }

    /**
     * Returns information about the currently logged in user.
     *
     * @return string|null
     */
    protected function getUser()
    {
        return $this->cas_user;
    }

    /**
     * Get currently logged in user attributes
     *
     * @return array|null
     */
    public function getAttributes()
    {
        return phpCAS::getAttributes();
    }

    /**
     * Get specific attribute
     *
     * @param $attribute_name string
     * @return string|null
     */
    public function getAttribute($attribute_name)
    {
        return isset($this->getAttributes()[$attribute_name]) ? $this->getAttributes()[$attribute_name] : null;
    }

    

    /**
     * Check if user is in specific group
     *
     * @param $group_name
     * @return bool|int (0,1)
     */
    public function isInGroup($group_name)
    {
        if ($this->config['cas_saml']) {
            $groups = $this->getAttributes()[$this->config['cas_saml_attr_groups']];

            if (empty($groups)) {
                return false;
            }

            if (!is_string($groups)) {
                $groups = implode(",", $groups);
            }

            return preg_match("/" . trim($group_name) . "/i", $groups);
        }

        return false;
    }
    
}
