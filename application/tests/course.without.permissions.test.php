<?php

require_once 'testcase.course.php';

class TestCourseWithoutPermissions extends CourseTestCase {

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
     * Test the view to create a course
     */
    public function testCourseCreateView() {
        Sentry::logout();
        $response = $this->get('course/create');
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $this->assertEquals('general.permission', $response->content->view);
    }


    /**
     * Test create course with valid data and no group
     */
    public function testCourseCreatePostWithValidDataAndNoGroup() {
        Sentry::logout();
        $post_data = array(
            'name'        => 'Test yz',
            'description' => 'This is a testcourse that shows the right functionality of the controller.'
        );
        $response = $this->post('course/create', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $this->assertEquals('general.permission', $response->content->view);
    }


    /**
     * Test create course with valid data and group
     */
    public function testCourseCreatePostWithValidDataAndGroup() {
        $group = Learngroup::where('name', 'LIKE', 'Group Test xy')->first();

        $post_data = array(
            'name'        => 'Test yz',
            'description' => 'This is a testcourse that shows the right functionality of the controller.',
            'groups'      => array($group->id)
        );
        $response = $this->post('course/create', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('course/create', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test the view to edit a existing course
     */
    public function testCourseEditExistingID() {
        $response = $this->get('course/' . $this->course->id . '/edit');
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $this->assertEquals('general.permission', $response->content->view);
    }


    /**
     * Test edit course with valid data
     */
    public function testCourseEditPostWithValidData() {
        $post_data = array(
            'id'            => $this->course->id,
            'name'          => 'Test yz',
            'description'   => 'This is a testcourse that shows the right functionality of the controller.'
        );
        $response = $this->post('course/edit', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $this->assertEquals('general.permission', $response->content->view);
    }


    /**
     * Test edit course with valid data and same group
     */
    public function testCourseEditPostWithValidDataAndSameGroup() {
        $group = $this->course->learngroups()->first();

        $post_data = array(
            'id'            => $this->course->id,
            'name'          => 'Test yz',
            'description'   => 'This is a testcourse that shows the right functionality of the controller.',
            'groups'        => array($group->id)
        );
        $response = $this->post('course/edit', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $this->assertEquals('general.permission', $response->content->view);
    }


    /**
     * Test edit course with valid data and from group to group
     */
    public function testCourseEditPostWithValidDataAndFromGroupToGroup() {
        $group_new = Learngroup::where('name', 'LIKE', 'Group Test xy')->first();

        $post_data = array(
            'id'            => $this->course->id,
            'name'          => 'Test yz',
            'description'   => 'This is a testcourse that shows the right functionality of the controller.',
            'groups'        => array($group_new->id)
        );
        $response = $this->post('course/edit', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $this->assertEquals('general.permission', $response->content->view);
    }


    /**
     * Test edit course with valid data and from group to no group
     */
    public function testCourseEditPostWithValidDataAndFromGroupToNoGroup() {
        $post_data = array(
            'id'            => $this->course->id,
            'name'          => 'Test yz',
            'description'   => 'This is a testcourse that shows the right functionality of the controller.',
            'groups'        => ''
        );
        $response = $this->post('course/edit', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $this->assertEquals('general.permission', $response->content->view);
    }


    /**
     * Test edit course with valid data and from no group to group
     */
    public function testCourseEditPostWithValidDataAndFromNoGroupToGroup() {
        $group_new = Learngroup::where('name', 'LIKE', 'Group Test xy')->first();
        $this->course->learngroups()->delete();

        $post_data = array(
            'id'            => $this->course->id,
            'name'          => 'Test yz',
            'description'   => 'This is a testcourse that shows the right functionality of the controller.',
            'groups'        => array($group_new->id)
        );
        $response = $this->post('course/edit', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('course/' . $this->course->id . '/edit', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test delete course with valid data
     */
    public function testCourseDeleteWithValidData() {
        $response = $this->get('course/' . $this->course->id . '/delete');
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $this->assertEquals('general.permission', $response->content->view);
    }

}