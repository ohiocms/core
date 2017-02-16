<?php

use Belt\Core\Http\Requests\StoreUser;

class StoreUserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers \Belt\Core\Http\Requests\StoreUser::rules
     */
    public function test()
    {

        $request = new StoreUser();

        $this->assertNotEmpty($request->rules());
    }

}