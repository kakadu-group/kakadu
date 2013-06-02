<?php

class Import_Controller extends Base_Kakadu_Controller {

    private $rules = array(
                            'id'        => 'required|integer|min:0',
                            'file'      => 'required'
                        );


    /**
     * Shows the view to import the data
     */
    public function get_course($id) {

        //Get course
        $this->course = Course::find($id);

        if($this->course === null) {
            return Response::error('404');
        }

        //Check permissions
        $permission = $this->checkPermissions(Const_Action::CREATE);

        if($permission === Const_Permission::DENIED) {
            return View::make('general.permission');
        }

        //Check favorite
        $favorite = false;
        $catalog = $this->course->catalog()->first();

        if($this->user !== null) {
            $userSentry = Sentry::user();
            $favorite = Helper_Favorite::isCatalogFavoriteOfUser($catalog, $userSentry);
        }

        //Create view
        $view = View::make('course.import');
        $this->layout->content = $view;
        $view->course = $this->getCourseArray($this->course, $favorite);
    }


    /**
     * Import the data
     */
    public function post_check() {

        $redirect_success = 'import/check';
        $redirect_error = 'course/import';

        //Delete old session
        Session::forget('import');

        //Validate input
        $response = $this->validateInput($this->rules);

        if ($response !== true) {
            $parameters = array(Input::get('id'));
            return $this->redirectWithErrors($redirect_error, $response, $parameters);
        }

        //Data
        $id = Input::get('id');
        $file = Input::file('file');
        $fileName = $file['name'];
        $filePath = $file['tmp_name'];

        //Check extension
        if (File::extension($fileName) !== 'csv') {
            $message = array(__('import.import_extension'));
            $parameters = array(Input::get('id'));
            return $this->redirectWithErrors($redirect_error, $message, $parameters);
        }

        //Get course
        $this->course = Course::find($id);

        if($this->course === null) {
            return Response::error('404');
        }

        //Check permissions
        $permission = $this->checkPermissions(Const_Action::CREATE);

        if($permission === Const_Permission::DENIED) {
            return View::make('general.permission');
        }

        //Read CSV file
        $import = $this->readCSVImport($filePath);

        if(!$import) {
            return $this->redirectWithSyntaxError();
        }

        //Save parsed data in session
        $import['course'] = $this->course->id;
        Session::put('import', $import);

        return Redirect::to_route($redirect_success);
    }


    /**
     * Show the confirmations site for the import
     */
    public function get_check() {

        $redirect_error = 'home';

        //Check the session variable
        if(!Session::has('import')) {
            $message = array(__('import.no_import_available'));
            return $this->redirectWithErrors($redirect_error, $message);
        }

        $import = Session::get('import');

        $course = array(
            'id'        => 1,
            'name'      => 'Course',
            'parent'    => ''
        );
        $import['catalogs'] = array($course) + $import['catalogs'];

        //Get course
        $this->course = Course::find($import['course']);

        if($this->course === null) {
            return Response::error('404');
        }

        //Check permissions
        $permission = $this->checkPermissions(Const_Action::CREATE);

        if($permission !== Const_Permission::ALLOWED) {
            return View::make('general.permission');
        }

        //Create view
        $view = View::make('import.import');
        $this->layout->content = $view;
        $view->course = $this->getCourseArray($this->course);
        $view->import = $import;
    }


    /**
     * Save the import
     */
    public function post_save() {

        $redirect_success = 'course';
        $redirect_error = 'home';

        //Validate input
        $rules = array(
            'answer'    => 'required|in:true,false'
        );

        $response = $this->validateInput($rules);

        if ($response !== true) {
            return $this->redirectWithErrors($redirect_error, $response);
        }

        //Check the session variable
        if(!Session::has('import')) {
            $message = array(__('import.no_import_available'));
            return $this->redirectWithErrors($redirect_error, $message);
        }

        $import = Session::get('import');

        //Get course
        $this->course = Course::find($import['course']);

        if($this->course === null) {
            return Response::error('404');
        }

        //Check permissions
        $permission = $this->checkPermissions(Const_Action::CREATE);

        if($permission !== Const_Permission::ALLOWED) {
            return View::make('general.permission');
        }

        //Check answer
        $answer = Input::get('answer');

        if($answer === 'true') {
            //Save import
            $catalogs = $import['catalogs'];
            $questions = $import['questions'];
            $this->saveCSVImport($catalogs, $questions);
        }

        //Save import
        Session::forget('import');
        $parameters = array($import['course']);
        return Redirect::to_route($redirect_success, $parameters);
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

        return true;
    }


    /**
     * Reads a CSV file and returns all catalogs and questions
     * 
     * @param  String $filePath
     * @return array  An array with all catalogs and questions or false on a syntax error
     */
    private function readCSVImport($filePath) {
        //Open file
        $objReader = PHPExcel_IOFactory::createReader('CSV')->setDelimiter(',')
                                                    ->setEnclosure('"')
                                                    ->setLineEnding("\n")
                                                    ->setSheetIndex(0);
        
        $objPHPExcel = $objReader->load($filePath);


        //Iterate through the file and check if the file is in the right format
        $worksheet = $objPHPExcel->getActiveSheet();
        $rowIterator = $worksheet->getRowIterator();

        $part = 1;
        $allowedCatalogIDs = array(1);
        $catalogs = array();
        $questions = array();

        foreach ($rowIterator as $row) {
            $cellIterator = $row->getCellIterator();

            switch($part) {
                case 1:
                    if($this->checkFirstRow($cellIterator)) {
                        $part = 2;
                    } else {
                        return false;
                    }
                    break;

                case 2:
                    //New line, end of catalogs
                    if(!$cellIterator->valid()) {
                        $part = 3;
                    } else {
                        $catalog = $this->readCatalogInformations($cellIterator, $allowedCatalogIDs);

                        if(!$catalog) {
                            return false;
                        } else {
                            $catalogs[] = $catalog;
                            $allowedCatalogIDs[] = $catalog['id'];
                        }
                    }
                    break;

                case 3:
                    //New line, end of questions
                    if(!$cellIterator->valid()) {
                        $part = 3;
                    } else {
                        $question = $this->readQuestionInformations($cellIterator, $allowedCatalogIDs);

                        if(!$question) {
                            return false;
                        } else {
                            $questions[] = $question;
                        }
                    }
                    break;

                case 4:
                    break;

                default:
                    return false;
            }
        }

        return array(
            'catalogs'  => $catalogs,
            'questions' => $questions
        );
    }

    private function saveCSVImport($catalogs, $questions) {

        //Get catalog of the course
        $courseCatalog = $this->course->catalog()->first();

        //Catalog map
        $catalogMap = array();
        $catalogMap[1] = $courseCatalog;

        //Get or create catalogs
        foreach($catalogs as $catalogInfos) {
            $parentID = $catalogInfos['parent'];
            $parent = $catalogMap[$parentID];

            $catalog = Catalog::where('parent', '=', $parent->id)->where('name', 'LIKE', $catalogInfos['name'])->first();

            if($catalog == null) {
                //Create new catalog
                $maxNumber = Catalog::where('parent', '=', $parent->id)->max('number');

                if($maxNumber == null) {
                    $maxNumber = 0;
                }

                $catalog = new Catalog;
                $catalog->name = $catalogInfos['name'];
                $catalog->parent = $parent->id;
                $catalog->number = ++$maxNumber;
                $catalog->save();
            }

            $id = $catalogInfos['id'];
            $catalogMap[$id] = $catalog;
        }


        //Create questions
        foreach($questions as $questionInfos) {
            $catalogIDs = $questionInfos['catalogs'];
            $type = $questionInfos['type'];
            $data = $questionInfos['data'];

            $questionType = QuestionType::getQuestionType($type);
            $questionType->getQuestionFromImportData($data);
            $question = $questionType->save();

            foreach ($catalogIDs as $catalogID) {
                $catalog = $catalogMap[$catalogID];
                $catalog->questions()->attach($question);
            }
        }
    }


    /**
     * Returns a redirection with a syntax message
     * 
     * @return Redirect
     */
    private function redirectWithSyntaxError() {
        $message = array(__('import.import_syntax'));
        $parameters = array(Input::get('id'));
        return $this->redirectWithErrors('course/import', $message, $parameters);
    }


    /**
     * Checks the syntax of the first row
     * 
     * @param  PHPExcel_Worksheet_CellIterator $cellIterator
     * @return boolean                         Return true if the first row has the right syntax
     */
    private function checkFirstRow($cellIterator) {
        //First row - ID
        if(!$cellIterator->valid()) {
            return false;
        }

        $cell = $cellIterator->current();
        $courseID = $cell->getValue();

        //First row - Name
        $cellIterator->next();

        if(!$cellIterator->valid()) {
            return false;
        }

        $cell = $cellIterator->current();
        $courseName = $cell->getValue();

        //First row - Checks
        if($courseID != '1' || $courseName != 'Course') {
            return false;
        }

        return true;
    }


    /**
     * Reads the catalog informations
     * 
     * @param  PHPExcel_Worksheet_CellIterator $cellIterator
     * @param  array                           $allowedCatalogIDs
     * @return boolean                         Return true if the catalog row has the right syntax
     */
    private function readCatalogInformations($cellIterator, $allowedCatalogIDs) {
        //Catalog - ID
        $cell = $cellIterator->current();
        $catalogID = (int)$cell->getValue();

        if(in_array($catalogID, $allowedCatalogIDs)) {
            return false;
        }

        //Catalog - Name
        $cellIterator->next();

        if(!$cellIterator->valid()) {
            return false;
        }

        $cell = $cellIterator->current();
        $catalogName = $cell->getValue();

        //Catalog - Parent
        $cellIterator->next();

        if(!$cellIterator->valid()) {
            return false;
        }

        $cell = $cellIterator->current();
        $catalogParent = (int)$cell->getValue();

        //Check if parent id exists
        if(!in_array($catalogParent, $allowedCatalogIDs)) {
            return false;
        }

        return array(
            'id'        => $catalogID,
            'name'      => $catalogName,
            'parent'    => $catalogParent
        );
    }


    /**
     * Reads the question informations
     * 
     * @param  PHPExcel_Worksheet_CellIterator $cellIterator
     * @param  array                           $allowedCatalogIDs
     * @return boolean                         Return true if the question row has the right syntax
     */
    private function readQuestionInformations($cellIterator, $allowedCatalogIDs) {
        //Question - Catalogs
        $cell = $cellIterator->current();
        $questionCatalogs = $cell->getValue();

        //Check if the ids are allowed ids
        $ids = preg_split('/[ ]*,[ ]*/', $questionCatalogs);

        if(count($ids) <= 0) {
            return false;
        }

        foreach ($ids as $id) {
            if(!in_array($id, $allowedCatalogIDs)) {
                return false;
            }
        }
        
        //Question - Type
        $cellIterator->next();

        if(!$cellIterator->valid()) {
            return false;
        }

        $cell = $cellIterator->current();
        $questionType = $cell->getValue();

        //Question - Data
        switch($questionType) {
            case 'simple':
                $questionData = Simple::readCSVData($cellIterator);
                break;

            case 'multiple':
                $questionData = Multiple::readCSVData($cellIterator);
                break;

            default:
                return false;
        }

        if(!$questionData) {
            return false;
        }

        return array(
            'catalogs'  => $ids,
            'type'      => $questionType,
            'data'      => $questionData
        );
    }

}