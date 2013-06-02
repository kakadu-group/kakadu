<?php

require_once 'testcase.course.php';

class TestGroupWithoutPermissions extends CourseTestCase {

    protected $names_create = array('Test xy');
    protected $names_delete = array('Test xy');

    private $group = NULL;


    public function setUp() {
        parent::setUp();
        $this->group = Learngroup::where('name', 'LIKE', 'Group Test xy')->first();

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

        //Login in
        try {
            Sentry::login('georg@example.com', 'password');
        } catch (Sentry\SentryException $e) {
            printf($e->getMessage());
        }
    }


    public function tearDown() {
        try {
            if(Sentry::user_exists('georg@example.com')) {
                Sentry::user('georg@example.com', TRUE)->delete();
            }
        } catch (Sentry\SentryException $e) {
            printf($e->getMessage());
        }

        parent::tearDown();
    }
    


    /**
     * Test the view to create a group
     */
    public function testGroupCreateView() {
        Sentry::logout();
        $response = $this->get('group/create');
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $this->assertEquals('general.permission', $response->content->view);
    }


    /**
     * Test create group with valid data
     */
    public function testGroupCreatePostWithValidData() {
        Sentry::logout();
        $post_data = array(
            'name'        => 'Group Test yz',
            'description' => 'This is a testgroup that shows the right functionality of the controller.'
        );
        $response = $this->post('group/create', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $this->assertEquals('general.permission', $response->content->view);
    }


    /**
     * Test the view to edit a existing group
     */
    public function testGroupEditExistingID() {
        $response = $this->get('group/' . $this->group->id . '/edit');
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $this->assertEquals('general.permission', $response->content->view);
    }


    /**
     * Test edit group with valid data
     */
    public function testGroupEditPostWithValidData() {
        $post_data = array(
            'id'            => $this->group->id,
            'name'          => 'Group Test yz',
            'description'   => 'This is a testgroup that shows the right functionality of the controller.'
        );
        $response = $this->post('group/edit', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $this->assertEquals('general.permission', $response->content->view);
    }


    /**
     * Test delete group with valid data
     */
    public function testGroupDeleteWithValidData() {
        $response = $this->get('group/' . $this->group->id . '/delete');
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $this->assertEquals('general.permission', $response->content->view);
    }

}