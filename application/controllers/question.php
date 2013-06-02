<?php

class Question_Controller extends Base_Kakadu_Controller {

    private $question;

    private $rules = array(
                            'course'        => 'required|integer|min:0',
                            'type'          => 'required|min:2|max:10',
                            'question'      => 'required',
                            'answer'        => 'required',
                            'catalogs'      => 'required'
                        );


    /**
     * Shows question
     */
    public function get_question($id) {
        
        //Get question
        $this->question = Question::find($id);

        if($this->question === null) {
            return Response::error('404');
        }

        //Get course
        $this->course = Helper_Course::getCourseOfQuestion($this->question);

        //Check permissions
        $permission = $this->checkPermissions(Const_Action::SHOW);

        if($permission !== Const_Permission::ALLOWED) {
            return View::make('general.permission');
        }

        //Catalogs
        $catalogs = array();
        $catalogIDs = array();
        
        foreach($this->question->catalogs()->get() as $catalog) {
            $catalogs[] = array(
                'id'    => $catalog->id,
                'name'  => $catalog->name
            );

            $catalogIDs[] = $catalog->id;
        }

        //QuestionType
        $questionType = QuestionType::getQuestionFromQuestion($this->question);
        $questionViewElement = $questionType->getViewElement();
        $questionViewElement['catalogs'] = $catalogIDs;

        //Get catalog where request comes from
        $navCatalog = null;

        if(Input::has('navcatalog')) {
            $catalogID = Input::get('navcatalog');
            $catalog = Catalog::find($catalogID);

            if($catalog != null) {
                $course = Helper_Course::getCourseOfCatalog($catalog);

                if($this->course->id == $course->id) {
                    $navCatalog = array(
                        'id'        => $catalog->id,
                        'name'      => $catalog->name
                    );
                }
            }
        }
        
        //Create view
        $view = View::make('question.question');
        $this->layout->content = $view;
        $view->question = $questionViewElement;
        $view->catalogs = $catalogs;
        $view->course = $this->getCourseArray($this->course);
        $view->navCatalog = $navCatalog;

        //All catalogs of course
        $catalog = $this->course->catalog()->first();
        $view->allCatalogs = Helper_Course::getSubCatalogsOfCatalogWithIndent($catalog);
    }


    /**
     * Show the view to create a question
     * @param  integer $id Course id
     */
    public function get_create($id) {
        
        //Get course
        $this->course = Course::find($id);

        if($this->course === null) {
            return Response::error('404');
        }

        //Check permissions
        $permission = $this->checkPermissions(Const_Action::CREATE);

        if($permission !== Const_Permission::ALLOWED) {
            return View::make('general.permission');
        }

        //Create view
        $view = View::make('question.create');
        $this->layout->content = $view;
        $view->course = $this->getCourseArray($this->course);

        //All catalogs of course  
        $catalog = $this->course->catalog()->first();
        $view->catalogs = Helper_Course::getSubCatalogsOfCatalogWithIndent($catalog);
    }


    /**
     * Create a question
     */
    public function post_create() {

        $redirect_success = 'catalog';
        $redirect_error = 'question/create';

        //Validate input
        $response = $this->validateInput($this->rules);

        if ($response !== true) {
            $parameters = array(Input::get('course'));
            return $this->redirectWithErrors($redirect_error, $response, $parameters);
        }

        //Get course
        $this->course = Course::find(Input::get('course'));

        if($this->course === null) {
            return Response::error('404');
        }

        //Check permissions
        $permission = $this->checkPermissions(Const_Action::CREATE);

        if($permission !== Const_Permission::ALLOWED) {
            return View::make('general.permission');
        }

        //Check if catalogs are part of the course
        $catalogs = Input::get('catalogs');
        $result = Helper_Course::areCatalogsPartOfCourse($this->course, $catalogs);

        if($result === -1) {
            $messages = array(__('question.catalogs_not_valid'));
            $parameters = array(Input::get('course'));
            return $this->redirectWithErrors($redirect_error, $messages, $parameters);
        } else if($result === 0) {
            $messages = array(__('question.catalog_not_subcatalog_of_course'));
            $parameters = array(Input::get('course'));
            return $this->redirectWithErrors($redirect_error, $messages, $parameters);
        }

        //QuestionType and save question
        $questionType = QuestionType::getQuestionType(Input::get('type'));

        if($questionType === null) {
            $messages = array(__('question.type_not_found'));
            $parameters = array(Input::get('course'));
            return $this->redirectWithErrors($redirect_error, $messages, $parameters);
        }

        $response = $questionType->getQuestionFromInput();

        if($response !== true) {
            $parameters = array(Input::get('course'));
            return $this->redirectWithErrors($redirect_error, $response, $parameters);
        }

        $question = $questionType->save();
        $question->catalogs()->sync($catalogs);

        return Redirect::to_route($redirect_success, array($catalogs[0]));
    }


    /**
     * Show the view to edit a question
     * @param  integer $id Question id
     */
    public function get_edit($id) {

        //Get question
        $this->question = Question::with('catalogs')->find($id);

        if($this->question === null) {
            return Response::error('404');
        }

        //Get course
        $this->course = Helper_Course::getCourseOfQuestion($this->question);


        //Check permissions
        $permission = $this->checkPermissions(Const_Action::EDIT);

        if($permission !== Const_Permission::ALLOWED) {
            return View::make('general.permission');
        }

        //Get catalogs
        $catalogIDs = array();

        foreach($this->question->catalogs as $catalog) {
            $catalogIDs[] = $catalog->id;
        }

        //QuestionType
        $questionType = QuestionType::getQuestionFromQuestion($this->question);
        $questionViewElement = $questionType->getViewElement();
        $questionViewElement['catalogs'] = $catalogIDs;

        //Create view
        $view = View::make('question.edit');
        $this->layout->content = $view;
        $view->question = $questionViewElement;
        $view->course = $this->getCourseArray($this->course);

        //All catalogs of course
        $catalog = $this->course->catalog()->first();
        $view->catalogs = Helper_Course::getSubCatalogsOfCatalogWithIndent($catalog);
    }


    /**
     * Edit a question
     */
    public function post_edit() {

        $redirect_success = 'question';
        $redirect_error = 'question/edit';


        //Validate input
        $this->rules['id'] = 'required|integer|min:0';

        $response = $this->validateInput($this->rules);

        if ($response !== true) {
            $parameters = array(Input::get('id'));
            return $this->redirectWithErrors($redirect_error, $response, $parameters);
        }

        //Check if question is part of the course
        $id = Input::get('id');
        $this->question = Question::find($id);

        if($this->question === null) {
            $messages = array(__('question.question_not_found'));
            $parameters = array($id);
            return $this->redirectWithErrors($redirect_error, $messages, $parameters);
        }

        $this->course = Helper_Course::getCourseOfQuestion($this->question);

        if($this->course === null) {
            $messages = array(__('question.course_not_found'));
            $parameters = array($id);
            return $this->redirectWithErrors($redirect_error, $messages, $parameters);
        }

        if($this->course->id !== Input::get('course')) {
            $messages = array(__('question.question_not_part_of_course'));
            $parameters = array($id);
            return $this->redirectWithErrors($redirect_error, $messages, $parameters);
        }

        //Check permissions
        $permission = $this->checkPermissions(Const_Action::EDIT);

        if($permission !== Const_Permission::ALLOWED) {
            return View::make('general.permission');
        }

        //Check if catalogs are part of the course
        $catalogs = Input::get('catalogs');
        $result = Helper_Course::areCatalogsPartOfCourse($this->course, $catalogs);
        
        if($result === -1) {
            $messages = array(__('question.catalogs_not_valid'));
            $parameters = array($id);
            return $this->redirectWithErrors($redirect_error, $messages, $parameters);
        } else if($result === 0) {
            $messages = array(__('question.catalog_not_subcatalog_of_course'));
            $parameters = array($id);
            return $this->redirectWithErrors($redirect_error, $messages, $parameters);
        }

        //QuestionType and save question
        $questionType = QuestionType::getQuestionType(Input::get('type'));

        if($questionType === null) {
            $messages = array(__('question.type_not_found'));
            $parameters = array($id);
            return $this->redirectWithErrors($redirect_error, $messages, $parameters);
        }

        $response = $questionType->getQuestionFromInput();

        if($response !== true) {
            $parameters = array($id);
            return $this->redirectWithErrors($redirect_error, $response, $parameters);
        }

        $question = $questionType->save();
        $question->catalogs()->sync($catalogs);

        if(Request::ajax()) {
            return $this->getJsonOkResponse();
        } else {
            return Redirect::to_route($redirect_success, array($id));
        }
    }


    /**
     * Delete a question
     */
    public function get_delete($id) {
        $redirect_success = 'catalog';
        $redirect_error = 'course';

        //Get question
        $this->question = Question::find($id);

        if($this->question === null) {
            return Response::error('404');
        }

        //Get course
        $this->course = Helper_Course::getCourseOfQuestion($this->question);


        //Check permissions
        $permission = $this->checkPermissions(Const_Action::DELETE);

        if($permission !== Const_Permission::ALLOWED) {
            return View::make('general.permission');
        }

        //Get catalog
        $catalog = $this->question->catalogs()->first();

        //Delete question
        $this->question->delete();

        return Redirect::to_route($redirect_success, array($catalog->id));
    }


    /**
     * Show the view that the question was deleted
     */
    public function get_deleted() {
        $this->layout->content = View::make('question.deleted');
    }


    /**
     * Validate input with the given rules
     * 
     * @return array|boolean Returns a error array when there is validation error or true on a valid validation
     */
    private function validateInput($rules) {
        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            return $validation->errors->all();
        }

        //Check if catalogs are valid
        $catalogs = Input::get('catalogs');

        if(count($catalogs) < 1) {
            $message = __('question.no_catalog_selected') . $catalogs;
            return array($message);
        }

        foreach($catalogs as $catalog) {
            if(is_numeric($catalog) === false || ((int)$catalog) < 1) {
                $message = __('question.catalogs_not_valid');
                return array($message);
            }
        }

        return true;
    }

}