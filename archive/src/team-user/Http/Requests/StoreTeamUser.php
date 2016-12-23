<?php
namespace Ohio\Core\TeamUser\Http\Requests;

use Ohio\Core\Base\Http\Requests\FormRequest;

class StoreTeamUser extends FormRequest
{
    
    public function rules()
    {
        return [
            'team_id' => 'required|exists:teams,id',
            'user_id' => 'required|exists:users,id',
        ];
    }

}