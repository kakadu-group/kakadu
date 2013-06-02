<?php

require_once 'testcase.course.php';

class TestFavoriteWithPermissions extends CourseTestCase {

    protected $names_create = array('Test xy', 'Test yz');
    protected $names_delete = array('Test xy', 'Test yz');

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
    }


    /**
     * Test the view to show all favorites
     */
    public function testFavorites() {
        //Add favorite
        $userID = Sentry::user()->get('id');
        $user = User::find($userID);
        $course2 = Course::where('name', 'LIKE', 'Course 2 of group Test xy')->first();
        $catalog2 = $course2->catalog()->first();
        $subcatalog2 = $catalog2->children()->first();
        $user->favorites()->attach($subcatalog2);

        $course1 = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();
        $catalog1 = $course1->catalog()->first();
        $user->favorites()->attach($catalog1);


        //Send get request
        $response = $this->get('profile/favorites');
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $data = $response->content->data['content']->data();
        $this->assertArrayHasKey('courses', $data);
        $this->assertArrayHasKey('catalogs', $data);
        $this->assertCount(1, $data['courses']);
        $this->assertCount(1, $data['catalogs']);
    }


    /**
     * Test ajax add favorite with no valid type
     */
    public function testAjaxAddFavoriteWithNoValidType() {
        $post_data = array(
            'id'    => '1',
            'type'  => 'question'
        );
        $response = $this->ajax_post('favorites/add', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax add favorite with no existing course
     */
    public function testAjaxAddFavoriteNotExistingCourse() {
        $id = $this->getNotExistingID('Course');

        $post_data = array(
            'id'    => $id,
            'type'  => 'course'
        );
        $response = $this->ajax_post('favorites/add', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax add favorite with no existing catalog
     */
    public function testAjaxAddFavoriteNotExistingCatalog() {
        $id = $this->getNotExistingID('Catalog');

        $post_data = array(
            'id'    => $id,
            'type'  => 'catalog'
        );
        $response = $this->ajax_post('favorites/add', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
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
        $this->assertContains('"status":"Ok"', $content);

        //Check if favorite is saved
        $id = $this->course->catalog()->first()->id;
        $check = $this->isSavedAsFavorite($id);
        $this->assertTrue($check);
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
        $this->assertContains('"status":"Ok"', $content);

        //Check if favorite is saved
        $check = $this->isSavedAsFavorite($catalog2->id);
        $this->assertTrue($check);
    }


    /**
     * Test ajax remove favorite with no valid type
     */
    public function testAjaxRemoveFavoriteWithNoValidType() {
        $post_data = array(
            'id'    => '1',
            'type'  => 'question'
        );
        $response = $this->ajax_post('favorites/remove', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax remove favorite with no existing course
     */
    public function testAjaxRemoveFavoriteNotExistingCourse() {
        $id = $this->getNotExistingID('Course');

        $post_data = array(
            'id'    => $id,
            'type'  => 'course'
        );
        $response = $this->ajax_post('favorites/remove', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax remove favorite with no existing catalog
     */
    public function testAjaxRemoveFavoriteNotExistingCatalog() {
        $id = $this->getNotExistingID('Catalog');

        $post_data = array(
            'id'    => $id,
            'type'  => 'catalog'
        );
        $response = $this->ajax_post('favorites/remove', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax remove favorite course
     */
    public function testAjaxRemoveFavoriteCourse() {
        //Add favorite
        $userID = Sentry::user()->get('id');
        $user = User::find($userID);
        $course = Course::where('name', 'LIKE', 'Course 2 of group Test xy')->first();
        $catalog = $course->catalog()->first();
        $user->favorites()->attach($catalog);

        //Send post request
        $post_data = array(
            'id'    => $course->id,
            'type'  => 'course'
        );
        $response = $this->ajax_post('favorites/remove', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Ok"', $content);

        //Check if favorite is saved
        $check = $this->isSavedAsFavorite($catalog->id);
        $this->assertFalse($check);
    }


    /**
     * Test ajax remove favorite catalog
     */
    public function testAjaxRemoveFavoriteCatalog() {
        //Add favorite
        $userID = Sentry::user()->get('id');
        $user = User::find($userID);
        $course = Course::where('name', 'LIKE', 'Course 2 of group Test xy')->first();
        $catalog = $course->catalog()->first();
        $catalog2 = $catalog->children()->first();
        $user->favorites()->attach($catalog2);


        $post_data = array(
            'id'    => $catalog2->id,
            'type'  => 'catalog'
        );
        $response = $this->ajax_post('favorites/remove', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Ok"', $content);

        //Check if favorite is saved
        $check = $this->isSavedAsFavorite($catalog2->id);
        $this->assertFalse($check);
    }

}