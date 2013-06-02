<?php

class Base_Controller extends Controller {

    public $restful = true;
    public $layout = 'layouts.master';

    protected $user;
    protected $role;


    public function __construct(){
        parent::__construct();
        
        //csrf
        $this->filter('before', 'csrf')->on('post');

        //Assets
        Asset::add('jquery', 'js/jquery-1.8.2.js');
        Asset::add('underscore', 'js/underscore-min.js');
        Asset::add('backbone', 'js/backbone-min.js');
        Asset::add('bootstrap', 'js/bootstrap.js');
        Asset::add('bootbox', 'js/bootbox.js');
        Asset::add('jquery-ui', 'js/jquery-ui-1.10.0.js');
        Asset::add('sidebar', 'js/sidebar.js');
        Asset::add('cutString', 'js/cutString.js');
    }


    /**
     * Catch-all method for requests that can't be matched.
     *
     * @param  string   $method
     * @param  array    $parameters
     * @return Response
     */
    public function __call($method, $parameters)
    {
        return Response::error('404');
    }


    /**
     * It sets the user informations and the role in the system.
     * 
     * This function is called before the action is executed.
     *
     * @return void
     */
    public function before() {
        parent::before();

        //User not logged in
        if(Sentry::check() === FALSE) {
            $this->user = NULL;
            $this->role = Const_Role::GUEST;
        } else {
            //Get user informations
            $user = Sentry::user();
            $this->user = array();
            $this->user['id'] = $user->get('id');
            $this->user['email'] = $user->get('email');
            $this->user['displayname'] = $user->get('metadata.displayname');
            $this->user['language'] = $user->get('metadata.language');

            //Get the role in the system
            if($user->has_access('admin')) {
                $this->role = Const_Role::ADMIN;
            } else {
                $this->role = Const_Role::USER;
            }
        }

        $this->layout->share('user', $this->user);
        $this->layout->share('roleSystem', $this->role);
        $this->layout->share('roleLearngroup', $this->role);
    }


    /**
     * Redirect to the destination route with an error message 
     * @param  string $destinationRoute
     * @param  array  $message
     * @param  array  $parameters
     * @return Response                   
     */
    protected function redirectWithErrors($destinationRoute, $messages, $parameters = array()) {
        return Redirect::to_route($destinationRoute, $parameters)->with_errors($messages)->with_input();
    }


    /**
     * Returns a json response with the ok messages
     * @return Response      Response
     */
    protected function getJsonOkResponse() {
        $response = array(
            'status'    => 'Ok'
        );

        return Response::json($response);
    }


    /**
     * Returns a json response with the given info messages
     * @param  array $infos Info messages
     * @return Response      Response
     */
    protected function getJsonInfoResponse($infos) {
        $response = array(
            'status'    => 'Info',
            'messages'  => $infos
        );

        return Response::json($response);
    }
    

    /**
     * Returns a json response with the given error messages
     * @param  array $errors Error messages
     * @return Response      Response
     */
    protected function getJsonErrorResponse($errors) {
        $response = array(
            'status'    => 'Error',
            'errors'    => $errors
        );

        return Response::json($response);
    }

}