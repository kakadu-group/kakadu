<?php

class Search_Controller extends Base_Controller {
    

    /**
     * Check if the user has the permission to perform this action
     * 
     * @return Permission
     */
    protected function checkPermissions($action) {
        if(Sentry::check()) {
            return Const_Permission::ALLOWED;
        } else {
            return Const_Permission::DENIED;
        }
    }

    
    /**
     * Searches all users with a given text.
     * 
     * On a ajax request just the list will be returned
     */
    public function post_user() {

        //Validate input
        $response = $this->validateInput();

        if($response !== true) {
            return $response;
        }


        //Check permissions
        $permission = $this->checkPermissions(Const_Action::SEARCH);

        if($permission !== Const_Permission::ALLOWED) {
            $response = array(
                'status'    => 'Error',
                'errors'    => array(__('general.permission_denied'))
            );

            return Response::json($response);
        }


        //Get users
        $text = '%' . Input::get('search') . '%';

        $users = DB::table('users')
                    ->join('users_metadata', 'users.id', '=', 'users_metadata.user_id')
                    ->where('users.email', 'LIKE', $text)
                    ->or_where('users_metadata.displayname', 'LIKE', $text)
                    ->get(array('users.id', 'users.email', 'users_metadata.displayname'));

        $response = array(
            'status'    => 'Ok',
            'users'     => $users
        );

        return Response::json($response);
    }


    /**
     * Searches all groups with a given text.
     * 
     * On a ajax request just the list will be returned
     */
    public function post_group() {

        //Validate input
        $response = $this->validateInput();

        if($response !== true) {
            return $response;
        }

        //Check permissions
        $permission = $this->checkPermissions(Const_Action::SEARCH);

        if($permission !== Const_Permission::ALLOWED) {
            $response = array(
                'status'    => 'Error',
                'errors'    => array(__('general.permission_denied'))
            );

            return Response::json($response);
        }


        //Get groups
        $text = '%' . Input::get('search') . '%';

        $query = Learngroup::where('name', 'LIKE', $text)
                            ->or_where('description', 'LIKE', $text)
                            ->get();

        $groups = array();

        foreach ($query as $group) {
            $groups[] = array(
                'id'            => $group->id,
                'name'          => $group->name,
                'description'   => $group->description,
                'created_at'    => $group->created_at,
                'updated_at'    => $group->updated_at
            );
        }

        $response = array(
            'status'    => 'Ok',
            'groups'     => $groups
        );

        return Response::json($response);
    }


    /**
     * Validates the input
     * 
     * @return boolean|Response   True or the Json response
     */
    private function validateInput() {

        $rules = array(
            'search'    => 'required'
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            $errors = $validation->errors->get('search');

            $response = array(
                'status'    => 'Error',
                'errors'    => $errors
            );

            return Response::json($response);
        }

        return true;
    }

}