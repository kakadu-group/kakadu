<?php

require_once 'testcase.course.php';

class TestFavoriteWithoutPermissions extends CourseTestCase {

    protected $names_create = array('Test xy');
    protected $names_delete = array('Test xy');

    private $course = null;


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
                Sentry::user('georg@example.com', true)->delete();
            }
        } catch(Sentry\SentryException $e) {
            printf($e->getMessage());
        }

        parent::tearDown();


        //Delete the course with no group
        $course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();

        if($course !== NULL) {
            $catalog = $course->catalog()->first();
            Helper_Course::removeQuestionsOfSubCatalogs($catalog);

            $course->delete();
            $catalog->delete();
        }
    }


    /**
     * Test the view to show all favorites
     */
    public function testFavorites() {
        Sentry::logout();

        $response = $this->get('profile/favorites');
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('auth/login', $response);
    }


    /**
     * Test ajax add favorite course
     */
    public function testAjaxAddFavoriteCourse() {
        $post_data = array(
            'id'    => $this->course->id,
            'type'  => 'course'
        );
        $response = $this->ajax_post('favorites/add', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);

        //Check if favorite is saved
        $id = $this->course->catalog()->first()->id;
        $check = $this->isSavedAsFavorite($id);
        $this->assertFalse($check);
    }


    /**
     * Test ajax add favorite catalog
     */
    public function testAjaxAddFavoriteCatalog() {
        $catalog1 = $this->course->catalog()->first();
        $catalog2 = $catalog1->children()->first();

        $post_data = array(
            'id'    => $catalog2->id,
            'type'  => 'catalog'
        );
        $response = $this->ajax_post('favorites/add', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);

        //Check if favorite is saved
        $check = $this->isSavedAsFavorite($catalog2->id);
        $this->assertFalse($check);
    }


    /**
     * Test ajax remove favorite course
     */
    public function testAjaxRemoveFavoriteCourse() {
        $post_data = array(
            'id'    => $this->course->id,
            'type'  => 'course'
        );
        $response = $this->ajax_post('favorites/remove', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);

        //Check if favorite is saved
        $catalog = $this->course->catalog()->first();
        $check = $this->isSavedAsFavorite($catalog->id);
        $this->assertFalse($check);
    }


    /**
     * Test ajax remove favorite catalog
     */
    public function testAjaxRemoveFavoriteCatalog() {
        $catalog = $this->course->catalog()->first();
        $catalog2 = $catalog->children()->first();

        $post_data = array(
            'id'    => $catalog2->id,
            'type'  => 'catalog'
        );
        $response = $this->ajax_post('favorites/remove', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);

        //Check if favorite is saved
        $check = $this->isSavedAsFavorite($catalog2->id);
        $this->assertFalse($check);
    }

}