<?php 

class Favorite_Controller extends Base_Kakadu_Controller {


    private $catalog = null;

    private $rules = array(
                            'id'    => 'required|integer',
                            'type'  => 'required|in:course,catalog'
                        );



    /**
     * Shows the view with all favorites of a user
     */
    public function get_favorites() {
        //Get user data
        $userSentry = Sentry::user();
        $data = Helper_Favorite::getFavorites($userSentry);

        //Create view
        $view = View::make('favorite.favorites');
        $this->layout->content = $view;
        $view->courses = $data['courses'];
        $view->catalogs = $data['catalogs'];
    }


    /**
     * Adds a course or a catalog to the favorite list of a user
     */
    public function post_add() {
        $response = $this->checkInputAndPermissions();

        if($response !== true) {
            return $response;
        }

        //Get the user and the favorites
        $user = User::find($this->user['id']);
        $favorites = $user->favorites()->get();


        //Check if catalog is allready a favorite
        foreach($favorites as $favorite) {
            if($this->catalog->id === $favorite->id) {
                return $this->getJsonInfoResponse(array(__('profile.allready_favorite')));
            }
        }

        //Save catalog as favorite
        $user->favorites()->attach($this->catalog);

        return $this->getJsonOkResponse();
    }


    /**
     * Removes a course or a catalog to the favorite list of a user
     */
    public function post_remove() {
        $response = $this->checkInputAndPermissions();

        if($response !== true) {
            return $response;
        }

        //Get the user and the favorites
        $user = User::find($this->user['id']);

        //Remove catalog as favorite
        $user->favorites()->detach($this->catalog);

        return $this->getJsonOkResponse();
    }


    /**
     * Check the input and the permissions
     * 
     * @return Response|boolean Returns a error response with the given message or true on a valid check
     */
    private function checkInputAndPermissions() {
        //Validate input
        $validation = $this->validateInput($this->rules);

        if($validation !== true) {
            return $validation;
        }

        //Get catalog
        $id = Input::get('id');
        $type = Input::get('type');

        if($type === 'course') {
            $this->course = Course::find($id);

            if($this->course === null) {
                return $this->getJsonErrorResponse(array(__('catalog.course_not_found')));
            }

            $this->catalog = $this->course->catalog()->first();
        } else {
            $this->catalog = Catalog::find($id);

            if($this->catalog === null) {
                return $this->getJsonErrorResponse(array(__('catalog.catalog_not_found')));
            }

            $this->course = Helper_Course::getCourseOfCatalog($this->catalog);
        }


        //Check permissions
        $permission = $this->checkPermissions(Const_Action::FAVORITE);

        if($permission !== Const_Permission::ALLOWED) {
            return $this->getJsonErrorResponse(array(__('general.permission_denied')));
        }

        return true;
    }


    /**
     * Validate input with the given rules
     * 
     * @return Response|boolean Returns a error response when there is a validation error or true on a valid validation
     */
    private function validateInput($rules) {
        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            $errors = $validation->errors->all();
            return $this->getJsonErrorResponse($errors);
        } else {
            return true;
        }
    }

}
