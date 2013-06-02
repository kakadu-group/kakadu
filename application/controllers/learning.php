<?php

class Learning_Controller extends Base_Kakadu_Controller {


    /**
     * Shows the learning view for a course with the first question
     */
    public function get_course($id) {
        
        //Get course
        $this->course = Course::find($id);

        if($this->course === null) {
            return Response::error('404');
        }

        //Check permissions
        $permission = $this->checkPermissions(Const_Action::LEARN);

        if($permission !== Const_Permission::ALLOWED) {
            return View::make('general.permission');
        }

        //Check favorites
        $userSentry = Sentry::user();
        $catalog = $this->course->catalog()->first();
        if(!Helper_Favorite::isCatalogFavoriteOfUser($catalog, $userSentry)) {
            return View::make('general.permission');
        }

        //Get all catalogs
        $catalogs = Helper_Course::getSubCatalogIDsOfCatalog($catalog);

        //Create view
        $this->layout->content = $this->getLearningView('course', $catalogs);
    }


    /**
     * Shows the learning view for a catalog with the first question
     */
    public function get_catalog($id) {
        
        //Get catalog
        $catalog = Catalog::find($id);

        if($catalog === null) {
            return Response::error('404');
        }

        //Get course
        $this->course = Helper_Course::getCourseOfCatalog($catalog);

        //Check permissions
        $permission = $this->checkPermissions(Const_Action::LEARN);

        if($permission !== Const_Permission::ALLOWED) {
            return View::make('general.permission');
        }

        //Check favorites
        $userSentry = Sentry::user();
        if(!Helper_Favorite::isCatalogFavoriteOfUser($catalog, $userSentry)) {
            if(!Helper_Favorite::isParentCatalogFavoriteOfUser($catalog, $userSentry)) {
                return View::make('general.permission');
            }
        }

        //Get all catalogs
        $catalogs = Helper_Course::getSubCatalogIDsOfCatalog($catalog);

        //Create view
        $this->layout->content = $this->getLearningView('catalog', $catalogs);
    }


    /**
     * Shows the learning view for all favorites
     */
    public function get_favorites() {
        
        //Check permissions
        $permission = $this->checkPermissions(Const_Action::LEARN);

        if($permission !== Const_Permission::ALLOWED) {
            return View::make('general.permission');
        }


        //Get user
        $user = User::find($this->user['id']);

        //Get favorite catalogs
        $catalogs = array();

        foreach ($user->favorites()->get() as $favorite) {
            $fav = Helper_Course::getSubCatalogIDsOfCatalog($favorite);
            $catalogs = array_merge($catalogs, $fav);
            $catalogs = array_unique($catalogs);
        }

        //Create view
        $this->layout->content = $this->getLearningView('favorites', $catalogs);
    }


    /**
     * Saves the answer of the last question and returns the next question as a JSON response
     */
    public function post_next() {

        //Validate input
        $rules = array(
            'question'          => 'required|integer',
            'course'            => 'integer',
            'catalog'           => 'integer',
            'answer'            => 'required|in:true,false',
            'section'           => 'required|in:course,catalog,favorites',         
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            $errors = $validation->errors->all();
            return $this->getJsonErrorResponse($errors);
        }


        //Get question
        $question = Question::find(Input::get('question'));

        if($question === null) {
            return $this->getJsonErrorResponse(array(__('question.question_not_found')));
        }


        //Get catalog and course
        $section = Input::get('section');

        if($section === 'course') {
            $course = Course::find(Input::get('course'));

            if($course === null) {
                return $this->getJsonErrorResponse(array(__('question.course_not_found')));
            }

            $catalog = $course->catalog()->first();
            $check = Helper_Course::isQuestionPartOfCatalog($question, $catalog);
            
            if($check === false) {
                return $this->getJsonErrorResponse(array(__('question.catalog_not_valid')));
            }

        } else if($section === 'catalog') {
            $catalog = Catalog::find(Input::get('catalog'));

            if($catalog === null) {
                return $this->getJsonErrorResponse(array(__('question.catalog_not_found')));
            }

            $check = Helper_Course::isQuestionPartOfCatalog($question, $catalog);
            
            if($check === false) {
                return $this->getJsonErrorResponse(array(__('question.catalog_not_valid')));
            }

            $this->course = Helper_Course::getCourseOfCatalog($catalog);
        }


        //Check permissions
        $permission = $this->checkPermissions(Const_Action::LEARN);

        if($permission !== Const_Permission::ALLOWED) {
            return $this->getJsonErrorResponse(array(__('general.permission_denied')));
        }


        //Check favorites
        $userSentry = Sentry::user();

        if($section === 'course' || $section === 'catalog') {
            if(!Helper_Favorite::isCatalogFavoriteOfUser($catalog, $userSentry)) {
                if($section === 'course' || !Helper_Favorite::isParentCatalogFavoriteOfUser($catalog, $userSentry)) {
                    return $this->getJsonErrorResponse(array(__('general.permission_denied')));
                }
            }
        }


        //Get all catalogs
        if($section === 'course' || $section === 'catalog') {
            $catalogs = Helper_Course::getSubCatalogIDsOfCatalog($catalog);

        } else if($section === 'favorites') {
            $user = User::find($this->user['id']);

            $catalogs = array();
            foreach ($user->favorites()->get() as $favorite) {
                $fav = Helper_Course::getSubCatalogIDsOfCatalog($favorite);
                $catalogs = array_merge($catalogs, $fav);
                $catalogs = array_unique($catalogs);
            }

            if(count($catalogs) <= 0) {
                return $this->getJsonErrorResponse(array(__('question.no_question_found')));
            }
        }
        

        //Save the answer of the last question
        Helper_Flashcard::saveFlashcard($userSentry, $question, Input::get('answer'), $catalogs);

        //Get a new question
        $data = Helper_Flashcard::getNextQuestion($userSentry, $catalogs);

        if($data === false) {
            return $this->getJsonErrorResponse(array(__('question.no_question_found')));
        }

        $question = $data['question'];
        $questionType = QuestionType::getQuestionFromQuestion($question);

        $catalog = $data['catalog'];
        $course = Helper_Course::getCourseOfCatalog($catalog);

        $response = array(
            'status'    => 'Ok',
            'catalog'   => $catalog->id,
            'course'    => $course->id
        );

        $response = array_merge($response, $questionType->getViewElement());

        return Response::json($response);
    }


    /**
     * Gets the learning view with the first question
     * @param  array $catalogs An array with all valid catalogs
     * @return View            The learning view with the first question
     */
    private function getLearningView($section, $catalogs) {
        //Get the next question
        $data = Helper_Flashcard::getNextQuestion(Sentry::user(), $catalogs);

        //Create view
        $view = View::make('learning.question');

        //No questions found
        if($data === false) {
            $view->error = array(__('question.no_question_found'));
            return $view;
        }

        $question = $data['question'];
        $questionType = QuestionType::getQuestionFromQuestion($question);

        $catalog = $data['catalog'];
        $course = Helper_Course::getCourseOfCatalog($catalog);

        $view->section = $section;
        $view->question = $questionType->getViewElement();
        $view->catalog = $this->getCatalogArray($catalog);
        $view->course = $this->getCourseArray($course);

        return $view;
    }

}