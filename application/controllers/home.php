<?php

class Home_Controller extends Base_Controller {


    /**
     * Display the home screen.
     * 
     * Depending if the user is logged in or not the screen shows a login field or the user informations.
     */
    public function get_index() {
        $view = View::make('home.index');

        //Get informations of logged in user
        if($this->user !== null) {
            $userSentry = Sentry::user();

            //Get the learngroups of the user
            $groups = Helper_Group::getLearngroupsOfUser($userSentry);
            $view->learngroups = $groups;

            //Get favorites of the user
            $favorites = Helper_Favorite::getFavorites($userSentry);
            $view->courses = $favorites['courses'];
            $view->catalogs = $favorites['catalogs'];
        }


        $this->layout->content = $view;
    }


    public function get_help() {
        $view = View::make('home.help');
        $this->layout->content = $view;
    }

}