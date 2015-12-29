<?php

namespace Avvertix\Caslite;

use ArrayAccess;

class CasUser implements Contracts\User
{

    /**
     * The user's e-mail address.
     *
     * @var string
     */
    public $email;


    /**
     * Get the e-mail address of the user.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

 
    /**
     * Set the raw user array from the provider.
     *
     * @param  string  $user
     * @return $this
     */
    public function setRaw($user)
    {
        $this->email = $user;

        return $this;
    }

}

