<?php

use Mockery as m;
use Ohio\Core\Base\Testing;

use Ohio\Core\Team\Team;
use Ohio\Core\TeamUser\TeamUser;
use Ohio\Core\User\User;
use Ohio\Core\Team\Http\Requests\AttachUser;
use Ohio\Core\Team\Http\Requests\PaginateUsers;
use Ohio\Core\Team\Http\Controllers\Api\UsersController;
use Ohio\Core\Base\Http\Exceptions\ApiNotFoundHttpException;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsersControllerTest extends Testing\OhioTestCase
{

    use Testing\CommonMocks;

    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Ohio\Core\Team\Http\Controllers\Api\UsersController::__construct
     * @covers \Ohio\Core\Team\Http\Controllers\Api\UsersController::team
     * @covers \Ohio\Core\Team\Http\Controllers\Api\UsersController::user
     * @covers \Ohio\Core\Team\Http\Controllers\Api\UsersController::destroy
     * @covers \Ohio\Core\Team\Http\Controllers\Api\UsersController::store
     * @covers \Ohio\Core\Team\Http\Controllers\Api\UsersController::index
     */
    public function test()
    {

        $team1 = factory(Team::class)->make();
        $user1 = factory(User::class)->make();

//        $userPaginateTeams = m::mock(PaginateUsers::class . '[modifyQuery]');
//        $userPaginateTeams->merge(['team_id' => 1]);
//        $qbMock = $this->getPaginateQBMock($userPaginateTeams, [$team1]);
//        $userPaginateTeams->shouldReceive('modifyQuery')->andReturn($qbMock);

        $teamRepository = m::mock(Team::class);
        $teamRepository->shouldReceive('find')->with(1)->andReturn($team1);
        $teamRepository->shouldReceive('find')->with(999)->andReturn(null);

        $teamUserRepository = m::mock(TeamUser::class);
        $teamUserRepository->shouldReceive('firstOrCreate');
        $teamUserRepository->shouldReceive('where')->andReturnSelf();
        $teamUserRepository->shouldReceive('delete');

        $userRepository = m::mock(User::class);
        $userRepository->shouldReceive('find')->with(1)->andReturn($user1);
        $userRepository->shouldReceive('find')->with(999)->andReturn(null);
        $userRepository->shouldReceive('create')->andReturn($user1);
        $userRepository->shouldReceive('query')->andReturn($this->getQBMock());

        # construct
        $controller = new UsersController($teamRepository, $teamUserRepository, $userRepository);
        $this->assertEquals($teamRepository, $controller->team);
        $this->assertEquals($teamUserRepository, $controller->teamUser);
        $this->assertEquals($userRepository, $controller->user);

        # get existing team
        $team = $controller->team(1);
        $this->assertEquals($team1->name, $team->name);

        # get existing user
        $user = $controller->user(1);
        $this->assertEquals($user1->email, $user->email);

        # get team that doesn't exist
        try {
            $controller->team(999);
        } catch (\Exception $e) {
            $this->assertInstanceOf(ApiNotFoundHttpException::class, $e);
        }

        # get user that doesn't exist
        try {
            $controller->user(999);
        } catch (\Exception $e) {
            $this->assertInstanceOf(ApiNotFoundHttpException::class, $e);
        }

        # destroy team
        $response = $controller->destroy(1, 1);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());

        # create team
        $response = $controller->store(new AttachUser(), 1);
        $this->assertInstanceOf(JsonResponse::class, $response);

        # index
        $paginatorMock = $this->getPaginatorMock();
        $paginatorMock->shouldReceive('toArray')->andReturn([]);

        $controller = m::mock(UsersController::class . '[paginator]', [$teamRepository, $teamUserRepository, $userRepository]);
        $controller->shouldReceive('paginator')->andReturn($paginatorMock);
        $response = $controller->index(new PaginateUsers(), 1);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

}