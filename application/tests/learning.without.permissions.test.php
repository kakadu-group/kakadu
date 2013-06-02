<?php

require_once 'testcase.course.php';

class TestLearningWithoutPermissions extends CourseTestCase {

    protected $names_create = array('Test xy');
    protected $names_delete = array('Test xy');

    private $course = NULL;


    public function setUp() {
        parent::setUp();
        $this->course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();

        //Create user
        try {
            $user = array(
                'email'     => 'georg@example.com',
                'password'  => 'password',
                'metadata'  => array(
                    'displayname'   => 'Georg',
                    'language'      => 'en'
                )
            );
            
            Sentry::user()->create($user);
        } catch (Sentry\SentryException $e) {
            printf($e->getMessage());
        }

        $this->resetSentry();

        //Login in
        try {
            Sentry::login('georg@example.com', 'password');
        } catch (Sentry\SentryException $e) {
            printf($e->getMessage());
        }
    }

    public function tearDown() {
        Sentry::logout();

        try {
            if(Sentry::user_exists('georg@example.com')) {
                Sentry::user('georg@example.com', TRUE)->delete();
            }
        } catch(Sentry\SentryException $e) {
            printf($e->getMessage());
        }

        parent::tearDown();
    }



    /**
     * Test the view to learn a course
     */
    public function testCourseView() {
        $response = $this->get('course/' . $this->course->id . '/learning');
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $this->assertEquals('general.permission', $response->content->view);
    }

    /**
     * Test the view to learn a catalog
     */
    public function testCatalogView() {
        $response = $this->get('catalog/' . $this->course->catalog()->first()->id . '/learning');
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $this->assertEquals('general.permission', $response->content->view);
    }

    /**
     * Test the view to learn a favorite
     */
    public function testFavoriteView() {
        Sentry::logout();
        $response = $this->get('favorites/learning');
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $this->assertEquals('general.permission', $response->content->view);
    }

    /**
     * Test ajax get next question of course
     */
    public function testAjaxCourseNextQuestion() {
        $catalog = $this->course->catalog()->first();
        $question = $catalog->questions()->first();

        $post_data = array(
            'question'  => $question->id,
            'course'    => $this->course->id,
            'answer'    => 'true',
            'section'   => 'course'
        );
        $response = $this->ajax_post('learning/next', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }

    /**
     * Test ajax get next question of catalog
     */
    public function testAjaxCatalogNextQuestion() {
        $catalog = $this->course->catalog()->first();
        $question = $catalog->questions()->first();

        $post_data = array(
            'question'  => $question->id,
            'catalog'   => $catalog->id,
            'answer'    => 'true',
            'section'   => 'catalog'
        );
        $response = $this->ajax_post('learning/next', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }

    /**
     * Test ajax get next question of favorites
     */
    public function testAjaxFavoritesNextQuestion() {
        Sentry::logout();
        $catalog = $this->course->catalog()->first();
        $question = $catalog->questions()->first();

        $post_data = array(
            'question'  => $question->id,
            'answer'    => 'true',
            'section'   => 'favorites'
        );
        $response = $this->ajax_post('learning/next', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }

}