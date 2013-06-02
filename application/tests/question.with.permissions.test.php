<?php

require_once 'testcase.course.php';

class TestQuestionWithPermissions extends CourseTestCase {

    protected $names_create = array('Test xy');
    protected $names_delete = array('Test xy');

    private $course = null;
    private $question = null;


    public function setUp() {
        parent::setUp();
        $this->course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();
        $this->question = Question::where('question', 'LIKE', '%This is question 1.2 of course 1 - group Test xy%')
                                    ->first();

        //Login in
        try {
            Sentry::login('alex@example.com', 'password');
        } catch (Sentry\SentryException $e) {
            printf($e->getMessage());
        }
    }


    /**
     * Test the view to show a existing question
     */
    public function testCourseViewExistingID() {
        $response = $this->get('question/' . $this->question->id);
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test the view to show a not existing question
     */
    public function testCourseViewNotExistingID() {
        $id = $this->getNotExistingID('Question');
        $response = $this->get('question/' . $id);
        $this->assertEquals('404', $response->foundation->getStatusCode());
    }


    /**
     * Test the view to create a question
     */
    public function testQuestionCreateView() {
        $response = $this->get('course/' . $this->course->id . '/question/create');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test create question with a too short data
     *
     * @depends testQuestionCreateView
     */
    public function testQuestionCreatePostWithTooShortData() {
        $post_data = array(
            'course' => '',
            'type' => 'a',
            'question' => '',
            'answer' => '',
            'catalogs' => ''
        );
        $response = $this->post('question/create', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('course//question/create', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test create question with valid data
     *
     * @depends testQuestionCreatePostWithTooShortData
     */
    public function testQuestionCreatePostWithValidData() {
        //Select catalogs
        $catalog = $this->course->catalog()->first();
        $subcatalogs = $catalog->children()->get();
        $ids = array();

        foreach ($subcatalogs as $c) {
            $ids[] = $c->id;
        }

        $post_data = array(
            'course'    => $this->course->id,
            'type'      => 'simple',
            'question'  => 'Question y',
            'answer'    => 'Question y',
            'catalogs'  => $ids
        );
        $response = $this->post('question/create', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('course/' . $this->course->id . '/question/create', $response);
        $this->checkIfNoErrorsExist();

        //Check the catalogs
        $question = Question::where('question', 'LIKE', '%Question y%')->first();
        $this->assertNotNull($question);

        $jsonQuestion = json_encode(array(
            'question' => 'Question y'
        ));

        $jsonAnswer = json_encode(array(
            'answer' => 'Question y'
        ));

        $this->assertEquals($question->question, $jsonQuestion);
        $this->assertEquals($question->answer, $jsonAnswer);

        $catalogs = $question->catalogs()->get();
        $this->assertNotNull($catalogs);
        $this->assertCount(2, $catalogs);

        foreach($catalogs as $catalog) {
            $this->assertContains($catalog->id, $ids);
        }
    }


    /**
     * Test the view to edit a existing question
     */
    public function testQuestionEditExistingID() {
        $response = $this->get('question/' . $this->question->id . '/edit');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test the view to edit a not existing question
     */
    public function testQuestionEditNotExistingID() {
        $id = $this->getNotExistingID('Question');
        $response = $this->get('question/' . $id . '/edit');
        $this->assertEquals('404', $response->foundation->getStatusCode());
    }


    /**
     * Test edit course with not existing id
     */
    public function testQuestionEditPostWithNotExistingID() {
        $id = $this->getNotExistingID('Question');

        $post_data = array(
            'course'    => $this->course->id,
            'id'        => $id,
            'type'      => 'type',
            'question'  => 'Test',
            'answer'    => 'Test',
            'catalogs'  => array(1, 2)
        );
        $response = $this->post('question/edit', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('question/' . $id . '/edit', $response);
    }


    /**
     * Test edit question with too short data
     *
     * @depends testQuestionEditPostWithNotExistingID
     */
    public function testQuestionEditPostWithTooShortData() {
        $post_data = array(
            'course' => '',
            'id' => '1',
            'type' => 'a',
            'question' => '',
            'answer' => '',
            'catalogs' => ''
        );
        $response = $this->post('question/edit', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkIfErrorsExist();
    }


    /**
     * Test edit question with valid data
     *
     * @depends testQuestionEditPostWithTooShortData
     */
    public function testQuestionEditPostWithValidData() {
        //Select catalogs
        $catalog = $this->course->catalog()->first();
        $subcatalogs = $catalog->children()->get();
        $ids = array();
        
        foreach ($subcatalogs as $c) {
            $ids[] = $c->id;
        }


        $post_data = array(
            'course'    => $this->course->id,
            'id'        => $this->question->id,
            'type'      => 'simple',
            'question'  => 'Question z',
            'answer'    => 'Question z',
            'catalogs'  => $ids
        );
        $response = $this->post('question/edit', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('question/' . $this->question->id, $response);
        $this->checkIfNoErrorsExist();

        //Check changed data
        $question = Question::find($this->question->id);

        $this->assertNotNull($question);
        $this->assertEquals($question->type, 'simple');

        $jsonQuestion = json_encode(array(
            'question' => 'Question z'
        ));

        $jsonAnswer = json_encode(array(
            'answer' => 'Question z'
        ));

        $this->assertEquals($question->question, $jsonQuestion);
        $this->assertEquals($question->answer, $jsonAnswer);

        $catalogs = $question->catalogs()->get();
        $this->assertNotNull($catalogs);
        $this->assertCount(2, $catalogs);

        foreach($catalogs as $catalog) {
            $this->assertContains($catalog->id, $ids);
        }
    }


    /**
     * Test delete question with not an existing course
     */
    public function testQuestionDeleteNotExistingID() {
        $id = $this->getNotExistingID('Question');
        $response = $this->get('question/' . $id . '/delete');
        $this->assertEquals('404', $response->foundation->getStatusCode());
    }


    /**
     * Test delete question with valid data
     *
     * @depends testQuestionDeleteNotExistingID
     */
    public function testQuestionDeleteWithValidData() {
        $catalog = $this->question->catalogs()->first();

        $response = $this->get('question/' . $this->question->id . '/delete');
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('catalog/' . $catalog->id, $response);

        $question = Question::where('question', 'LIKE', 'This is question 1.2 of Test xy')->first();

        $this->assertNull($question);
    }


    /**
     * Test the view to show a question was deleted
     */
    public function testQuestionDeletedView() {
        $response = $this->get('question/deleted');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }

}