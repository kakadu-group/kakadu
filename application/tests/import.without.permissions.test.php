<?php

require_once 'testcase.course.php';

class TestImportWithoutPermissions extends CourseTestCase {

    protected $names_create = array('Test xy');
    protected $names_delete = array('Test xy');

    private $course = null;


    public function setUp() {
        parent::setUp();
        $this->course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();
        Session::forget('import');
    }


    /**
     * Test the view to show the import view of a course
     */
    public function testCourseImportView() {
        $response = $this->get('course/' . $this->course->id . '/import');
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $this->assertEquals('general.permission', $response->content->view);
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
        $this->assertEquals('general.permission', $response->content->view);
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
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $this->assertEquals('general.permission', $response->content->view);
    }

}