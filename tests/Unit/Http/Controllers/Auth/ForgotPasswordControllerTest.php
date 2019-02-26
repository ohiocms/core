<?php namespace Tests\Belt\Core\Unit\Http;

use Belt\Core\Http\Controllers\Auth\ForgotPasswordController;
use Tests\Belt\Core;
use Illuminate\View\View;

class ForgotPasswordControllerTest extends \Tests\Belt\Core\BeltTestCase
{

    /**
     * @cover \Belt\Core\Http\Controllers\Auth\ForgotPasswordController::__construct
     * @cover \Belt\Core\Http\Controllers\Auth\ForgotPasswordController::showLinkRequestForm
     */
    public function test()
    {
        # __construct
        $controller = new ForgotPasswordController();

        # showLinkRequestForm
        $view = $controller->showLinkRequestForm();
        $this->assertInstanceOf(View::class, $view);
    }

}