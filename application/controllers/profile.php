<?php

class Profile_Controller extends Base_Controller {


    /**
     * Show the site to edit the profile
     */
    public function get_edit() {

        $view = View::make('authentification.edit_profile');        

        $view->language = $this->user['language'];      

        //Get all accepted languages
        $view->languages = Config::get('application.languages_accepted');    

        $this->layout->content = $view;
    }



    /**
     * Edit the profile
     */
    public function post_edit() {

        $redirect_success = 'profile/edit';
        $redirect_error = 'profile/edit';


        //Validate input
        $rules = array(
            'displayname'   => 'required',
            'email'         => 'required|email',
            'language'      => 'required|max:2'
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            $messages = $validation->errors->all();
            return $this->redirectWithErrors($redirect_error, $messages);
        }


        //Try to change profile settings
        try
        {
            $user = Sentry::user();
            $update = $user->update(array(
                'email' => trim(Input::get('email')),
                'metadata' => array(
                    'displayname' => trim(Input::get('displayname')),
                    'language' => Input::get('language')
                )
            ));

            if ($update) {
                Cookie::forever('language', Input::get('language'));
                return Redirect::to_route($redirect_success)->with('info', __('profile.profile_change_success'));
            } else {
                $messages = array(__('profile.profile_change_failure'));
                return $this->redirectWithErrors($redirect_error, $messages);
            }
        }
        catch (Sentry\SentryException $e)
        {
            $messages = array($e->getMessage());
            return $this->redirectWithErrors($redirect_error, $messages);
        }
    }



    /**
     * Change the password
     */
    public function post_changepassword() {

        $redirect_success = 'profile/edit';
        $redirect_error = 'profile/edit';


        //Validate input
        $rules = array(
            'password_old'  => 'required',
            'password'      => 'required|confirmed'
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            $messages = $validation->errors->all();
            return $this->redirectWithErrors($redirect_error, $messages);
        }


        //Try to change the password
        try
        {
            $user = Sentry::user();

            if ($user->change_password(Input::get('password'), Input::get('password_old'))) {
                return Redirect::to_route($redirect_success)->with('info', __('profile.password_change_success'));
            } else {
                $messages = array(__('profile.password_change_failure'));
                return $this->redirectWithErrors($redirect_error, $messages);
            }
        }
        catch (Sentry\SentryException $e)
        {
            $messages = array($e->getMessage());
            return $this->redirectWithErrors($redirect_error, $messages);
        }
    }


    /**
     * Show question if really delete the profile
     */
    public function get_delete() {
        $this->layout->content = View::make('authentification.delete_profile');
    }


    /**
     * Delete the user with all his data
     */
    public function delete_delete() {
        
        $redirect_success = 'home';
        $redirect_error = 'profile/edit';

        $userSentry = Sentry::user();
        $userKakadu = User::find($userSentry->get('id'));

        //Delete all related data
        $userKakadu->favorites()->delete();
        $userKakadu->flashcards()->delete();

        //Delete all learngroups, where user is the only admin
        $role = Role::where('name', 'LIKE', 'admin')->first();

        foreach($userKakadu->learngroups()->get() as $group) {
            $pivot = $group->users()->pivot();
            $number = $pivot->where('role_id', '=', $role->id)->count();

            if($number !== null && $number > 1) {
                //Delete learngroup with courses
                Helper_Group::deleteGroupAndCheckRelatedCourses($group);
            }
        }

        //Try to delete the user
        try
        {
            if ($userSentry->delete()) {
                Sentry::logout();
                return Redirect::to_route($redirect_success)->with('info', __('profile.profile_delete_success'));
            } else {
                $messages = array(__('profile.profile_delete_failure'));
                return $this->redirectWithErrors($redirect_error, $messages);
            }
        }
        catch (Sentry\SentryException $e)
        {
            $messages = array($e->getMessage());
            return $this->redirectWithErrors($redirect_error, $messages);
        }
    }

}
