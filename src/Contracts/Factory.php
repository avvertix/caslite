<?php

namespace Avvertix\Caslite\Contracts;

interface Factory
{
    /**
     * Redirect the user to the authentication page for the provider.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function authenticate();

    /**
     * Get the User instance for the authenticated user.
     *
     * @return \Avvertix\Caslite\Contracts\User
     */
    public function user();
    
    /**
     * This method is used to logout from CAS
     *
     * @param array ['url' => 'http://...'] || ['service' => ...]
     *
     * @return none
     */
    public function logout($params);
    
    /**
     * Check if already authenticated
     *
     * @return boolean
     */
    public function check();
}

