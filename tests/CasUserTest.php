<?php

//use Illuminate\Http\Request;
use Avvertix\Caslite\CasUser;

class CasUserTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        
    }

    public function testCasUserInstanceConstruction()
    {
        $user = (new CasUser)->setRaw('fake@email.com');

        $this->assertInstanceOf('Avvertix\Caslite\CasUser', $user);
        $this->assertInstanceOf('Avvertix\Caslite\Contracts\User', $user);
        $this->assertEquals('fake@email.com', $user->getEmail());
    }
}

