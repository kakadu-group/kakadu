<?php

require_once 'testcase.course.php';

class TestImportWithPermissions extends CourseTestCase {

    protected $names_create = array('Test xy');
    protected $names_delete = array('Test xy');

    private $course = null;


    public function setUp() {
        parent::setUp();
        $this->course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();

        //Login in
        try {
            Sentry::login('alex@example.com', 'password');
        } catch (Sentry\SentryException $e) {
            printf($e->getMessage());
        }

        Session::forget('import');
    }


    /**
     * Test the view to show the import view of a course
     */
    public function testCourseImportView() {
        $response = $this->get('course/' . $this->course->id . '/import');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test the view to show the import view of a not existing course
     */
    public function testCourseImportViewNotExistingID() {
        $id = $this->getNotExistingID('Course');
        $response = $this->get('course/' . $id . '/import');
        $this->assertEquals('404', $response->foundation->getStatusCode());
    }


    /**
     * Test the read CSV import with not first line
     */
    public function testReadCSVImportWithNoFirstLine() {
        $path = dirname(__FILE__) . '/imports/course1.csv';
        $result = $this->getReadCSVImport($path);
        $this->assertFalse($result);
    }


    /**
     * Test the read CSV import with not existing parent
     */
    public function testReadCSVImportWithNotExistingParent() {
        $path = dirname(__FILE__) . '/imports/course2.csv';
        $result = $this->getReadCSVImport($path);
        $this->assertFalse($result);
    }


    /**
     * Test the read CSV import with already existing id
     */
    public function testReadCSVImportWithAlreadyExistingID() {
        $path = dirname(__FILE__) . '/imports/course3.csv';
        $result = $this->getReadCSVImport($path);
        $this->assertFalse($result);
    }


    /**
     * Test the read CSV import with not existing catalog
     */
    public function testReadCSVImportWithNotExistingCatalog() {
        $path = dirname(__FILE__) . '/imports/course4.csv';
        $result = $this->getReadCSVImport($path);
        $this->assertFalse($result);
    }


    /**
     * Test the read CSV import with not existing type
     */
    public function testReadCSVImportWithNotExistingType() {
        $path = dirname(__FILE__) . '/imports/course5.csv';
        $result = $this->getReadCSVImport($path);
        $this->assertFalse($result);
    }


    /**
     * Test the read CSV import with wrong simple question
     */
    public function testReadCSVImportWithWrongSimpleQuestion() {
        $path = dirname(__FILE__) . '/imports/course6.csv';
        $result = $this->getReadCSVImport($path);
        $this->assertFalse($result);
    }


    /**
     * Test the read CSV import with wrong multiple question - answer
     */
    public function testReadCSVImportWithWrongMultipleQuestionAnswer() {
        $path = dirname(__FILE__) . '/imports/course7.csv';
        $result = $this->getReadCSVImport($path);
        $this->assertFalse($result);
    }


    /**
     * Test the read CSV import with wrong multiple question - choices
     */
    public function testReadCSVImportWithWrongMultipleQuestionChoices() {
        $path = dirname(__FILE__) . '/imports/course8.csv';
        $result = $this->getReadCSVImport($path);
        $this->assertFalse($result);
    }


    /**
     * Test the read CSV import
     */
    public function testReadCSVImport() {
        $path = dirname(__FILE__) . '/imports/course0.csv';
        $result = $this->getReadCSVImport($path);
        $this->assertArrayHasKey('catalogs', $result);
        $this->assertArrayHasKey('questions', $result);
    }


    /**
     * Test the check import view with not set session
     */
    public function testGetCheckWithNotSetSession() {
        $response = $this->get('import/check');
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('/', $response);
        $this->checkIfErrorsExist();
    }

    /**
     * Test the check import view with set session
     */
    public function testGetCheckWithSetSession() {
        //Session
        $catalogs = array();

        $question1 = array(
            'catalogs'  => array(1),
            'type'      => 'simple',
            'question'  => 'Question Test 1',
            'answer'    => 'Answer Test 1'
        );

        $questions = array($question1);

        $import = array(
            'course'    => $this->course->id,
            'catalogs'  => $catalogs,
            'questions' => $questions
        );

        Session::put('import', $import);

        //Call
        $response = $this->get('import/check');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }

    
    /**
     * Test the save import with no set session
     */
    public function testPostSaveWithNoSetSession() {
        $post_data = array(
            'answer' => 'true'
        );
        $response = $this->post('import/save', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('/', $response);
        $this->checkIfErrorsExist();
    }

    /**
     * Test the save import with set session and no answer
     */
    public function testPostSaveWithSetSessionAndAnswerNo() {
        //Session
        $catalogs = array();

        $question1 = array(
            'catalogs'  => array(1),
            'type'      => 'simple',
            'question'  => 'Question Test 1',
            'answer'    => 'Answer Test 1'
        );

        $questions = array($question1);

        $import = array(
            'course'    => $this->course->id,
            'catalogs'  => $catalogs,
            'questions' => $questions
        );

        Session::put('import', $import);

        //Call
        $post_data = array(
            'answer' => 'false'
        );
        $response = $this->post('import/save', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('/course/' . $this->course->id, $response);
        $this->checkIfNoErrorsExist();

        //Check created questions
        $catalog = $this->course->catalog()->first();
        $question = $catalog->questions()->where('question', 'LIKE', '%Question Test 1%')->first();
        $this->assertNull($question);
    }

    /**
     * Test the save import with set session and yes answer
     */
    public function testPostSaveWithSetSessionAndAnswerYes() {
        //Session
        $catalog1 = array(
            'id'        => 2,
            'name'      => 'Test 1',
            'parent'    => 1
        );
        $catalogs = array($catalog1);

        $question1 = array(
            'catalogs'  => array(1, 2),
            'type'      => 'simple',
            'data'      => array(
                'question'  => 'Question Test 1',
                'answer'    => 'Answer Test 1'
            )
        );
        $question2 = array(
            'catalogs'  => array(2),
            'type'      => 'multiple',
            'data'      => array(
                'question'  => 'Question Test 2',
                'answer'    => array(1),
                'choices'   => array('Answer 1 Test 2', 'Answer 2 Test 2')
            )
        );
        $questions = array($question1, $question2);

        $import = array(
            'course'    => $this->course->id,
            'catalogs'  => $catalogs,
            'questions' => $questions
        );

        Session::put('import', $import);

        //Call
        $post_data = array(
            'answer' => 'true'
        );
        $response = $this->post('import/save', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('/course/' . $this->course->id, $response);
        $this->checkIfNoErrorsExist();

        //Check created questions and catalogs
        $catalog = $this->course->catalog()->first();
        $question = $catalog->questions()->where('question', 'LIKE', '%Question Test 1%')->first();
        $this->assertNotNull($question);

        $questionType = QuestionType::getQuestionFromQuestion($question);
        $this->assertNotNull($questionType);

        $catalogs = $question->catalogs()->get();
        $this->assertCount(2, $catalogs);

        foreach($catalogs as $catalog) {
            $names = array('Course 1 of group Test xy', 'Test 1');
            $this->assertContains($catalog->name, $names);
        }

        $question = Question::where('question', 'LIKE', '%Question Test 2%')->first();
        $this->assertNotNull($question);

        $questionType = QuestionType::getQuestionFromQuestion($question);
        $this->assertNotNull($questionType);
    }


    /**
     * Returns the result of the readCSVImport call
     * 
     * @param  string        $path The path of the csv file
     * @return boolean|array       False on a syntax error or an array with the parsed data on success
     */
    private function getReadCSVImport($path) {
        //Reflection
        $controller = new Import_Controller;
        $reflection = new ReflectionClass("Import_Controller");
        $method = $reflection->getMethod("readCSVImport");
        $method->setAccessible(true);

        //Call
        return $method->invoke($controller, $path);
    }

}