<?php

require_once 'testcase.course.php';

class TestCourseWithPermissions extends CourseTestCase {

    protected $names_create = array('Test xy', 'Test yz', 'Test xz');
    protected $names_delete = array('Test xy', 'Test yz', 'Test xz');

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

    public function tearDown() {
        //Delete created course
        $course = Course::where('name', 'LIKE', 'Test yz')->first();

        if($course !== null) {
            $catalog = $course->catalog()->first();
            Helper_Course::removeQuestionsOfSubCatalogs($catalog);

            $course->delete();
            $catalog->delete();
        }

        parent::tearDown();
    }


    /**
     * Test the view to show all courses
     */
    public function testCoursesView() {
        $response = $this->get('courses');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test the view to show all courses with ajax
     */
    public function testCoursesViewWithAjax() {
        $response = $this->ajax_get('courses');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test the view to show all courses with pagination and sorting
     */
    public function testCoursesViewWithPaginationAndSorting() {
        $get_data = array(
            'page'      => '2',
            'per_page'  => '1',
            'sort'      => 'created_at',
            'sort_dir'  => 'desc'
        );
        $response = $this->get('courses', $get_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $data = $response->content->data['content']->data();
        $this->assertArrayHasKey('courses', $data);
        $this->assertCount(1, $data['courses']);
    }


    /**
     * Test the view to get all courses with ajax, pagination and sorting
     */
    public function testCoursesViewWithAjaxPaginationAndSorting() {
        $get_data = array(
            'page'      => '2',
            'per_page'  => '1',
            'sort'      => 'created_at',
            'sort_dir'  => 'desc'
        );
        $response = $this->ajax_get('courses', $get_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $data = $response->content->data();
        $this->assertArrayHasKey('courses', $data);
        $this->assertCount(1, $data['courses']);
    }


    /**
     * Test the view to show a existing course
     */
    public function testCourseViewExistingID() {
        $response = $this->get('course/' . $this->course->id);
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test the view to show a not existing course
     */
    public function testCourseViewNotExistingID() {
        $id = $this->getNotExistingID('Course');
        $response = $this->get('course/' . $id);
        $this->assertEquals('404', $response->foundation->getStatusCode());
    }


    /**
     * Test search course with valid result
     */
    public function testCourseSearchWithValidResult() {
        $get_data = array(
            'search'        => 'xy',
            'csrf_token'    => Session::token()
        );
        $response = $this->get('courses/search', $get_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $data = $response->content->data['content']->data();
        $this->assertArrayHasKey('courses', $data);
        $this->assertCount(2, $data['courses']);
        $this->checkIfNoErrorsExist();
    }


    /**
     * Test ajax search course with valid result
     */
    public function testCourseSearchAjaxWithValidResult() {
        $get_data = array(
            'search'        => 'xy',
            'csrf_token'    => Session::token()
        );
        $response = $this->ajax_get('courses/search', $get_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $data = $response->content->data();
        $this->assertArrayHasKey('courses', $data);
        $this->assertCount(2, $data['courses']);
        $this->checkIfNoErrorsExist();
    }


    /**
     * Test search course with no result
     */
    public function testCourseSearchWithNoResult() {
        $get_data = array(
            'search'        => 'wxyz',
            'csrf_token'    => Session::token()
        );
        $response = $this->get('courses/search', $get_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $data = $response->content->data['content']->data();
        $this->assertArrayHasKey('courses', $data);
        $this->assertCount(0, $data['courses']);
        $this->checkIfNoErrorsExist();
    }


    /**
     * Test ajax search course with no result
     */
    public function testCourseSearchAjaxWithNoResult() {
        $get_data = array(
            'search'        => 'wxyz',
            'csrf_token'    => Session::token()
        );
        $response = $this->ajax_get('courses/search', $get_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $data = $response->content->data();
        $this->assertArrayHasKey('courses', $data);
        $this->assertCount(0, $data['courses']);
        $this->checkIfNoErrorsExist();
    }


    /**
     * Test the view to create a course
     */
    public function testCourseCreateView() {
        $response = $this->get('course/create');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test create course with a too short data
     *
     * @depends testCourseCreateView
     */
    public function testCourseCreatePostWithTooShortData() {
        $post_data = array(
            'name'        => 'Test',
            'description' => 'Test'
        );
        $response = $this->post('course/create', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('course/create', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test create course with valid data and no group
     *
     * @depends testCourseCreatePostWithTooShortData
     */
    public function testCourseCreatePostWithValidDataAndNoGroup() {
        $post_data = array(
            'name'        => 'Test yz',
            'description' => 'This is a testcourse that shows the right functionality of the controller.'
        );
        $response = $this->post('course/create', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('course/[0-9]+', $response);
        $this->checkIfNoErrorsExist();
    }


    /**
     * Test create course with valid data and group
     *
     * @depends testCourseCreatePostWithValidDataAndNoGroup
     */
    public function testCourseCreatePostWithValidDataAndGroup() {
        $group = Learngroup::where('name', 'LIKE', 'Group Test xy')->first();

        $post_data = array(
            'name'        => 'Test yz',
            'description' => 'This is a testcourse that shows the right functionality of the controller.',
            'groups'       => array($group->id)
        );
        $response = $this->post('course/create', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('course/[0-9]+', $response);
        $this->checkIfNoErrorsExist();
    }


    /**
     * Test create course with valid data and groups
     *
     * @depends testCourseCreatePostWithValidDataAndGroup
     */
    public function testCourseCreatePostWithValidDataAndGroups() {
        $group1 = Learngroup::where('name', 'LIKE', 'Group Test xy')->first();
        $group2 = Learngroup::where('name', 'LIKE', 'Group Test yz')->first();

        $post_data = array(
            'name'        => 'Test yz',
            'description' => 'This is a testcourse that shows the right functionality of the controller.',
            'groups'       => array($group1->id, $group2->id)
        );
        $response = $this->post('course/create', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('course/[0-9]+', $response);
        $this->checkIfNoErrorsExist();
    }


    /**
     * Test the view to edit a existing course
     */
    public function testCourseEditExistingID() {
        $response = $this->get('course/' . $this->course->id . '/edit');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test the view to edit a not existing course
     */
    public function testCourseEditNotExistingID() {
        $id = $this->getNotExistingID('Course');
        $response = $this->get('course/' . $id . '/edit');
        $this->assertEquals('404', $response->foundation->getStatusCode());
    }


    /**
     * Test edit course with not existing id
     */
    public function testCourseEditPostWithNotExistingID() {
        $id = $this->getNotExistingID('Course');

        $post_data = array(
            'id'            => $id,
            'name'          => 'Test yz',
            'description'   => 'This is a testcourse that shows the right functionality of the controller.'
        );
        $response = $this->post('course/edit', $post_data);
        $this->assertEquals('404', $response->foundation->getStatusCode());
    }


    /**
     * Test edit course with too short data
     *
     * @depends testCourseEditPostWithNotExistingID
     */
    public function testCourseEditPostWithTooShortData() {
        $post_data = array(
            'id'            => $this->course->id,
            'name'          => 'Test',
            'description'   => 'Test'
        );
        $response = $this->post('course/edit', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkIfErrorsExist();
    }


    /**
     * Test edit course with valid data and same group
     *
     * @depends testCourseEditPostWithTooShortData
     */
    public function testCourseEditPostWithValidDataAndSameGroup() {
        $group = $this->course->learngroups()->first();

        $post_data = array(
            'id'            => $this->course->id,
            'name'          => 'Test yz',
            'description'   => 'This is a testcourse that shows the right functionality of the controller.',
            'groups'         => array($group->id)
        );
        $response = $this->post('course/edit', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('course/' . $this->course->id, $response);
        $this->checkIfNoErrorsExist();

        //Check changed data
        $course = Course::find($this->course->id);

        $this->assertNotNull($course);
        $this->assertEquals($course->name, 'Test yz');
        
        $description = 'This is a testcourse that shows the right functionality of the controller.';
        $this->assertEquals($course->description, $description);

        $catalog = $course->catalog()->first();
        $this->assertEquals($catalog->name, 'Test yz');

        $group = $course->learngroups()->first();
        $this->assertEquals($group->name, 'Group Test xy');
    }


    /**
     * Test edit course with valid data and from group to group
     *
     * @depends testCourseEditPostWithValidDataAndSameGroup
     */
    public function testCourseEditPostWithValidDataAndFromGroupToGroup() {
        //Add favorite
        $userID = Sentry::user()->get('id');
        $user = User::find($userID);
        $catalog = $this->course->catalog()->first();
        $catalog2 = $catalog->children()->first();
        $user->favorites()->attach($catalog2);


        $group_new = Learngroup::where('name', 'LIKE', 'Group Test yz')->first();

        $post_data = array(
            'id'            => $this->course->id,
            'name'          => 'Test yz',
            'description'   => 'This is a testcourse that shows the right functionality of the controller.',
            'groups'         => array($group_new->id)
        );
        $response = $this->post('course/edit', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('course/' . $this->course->id, $response);
        $this->checkIfNoErrorsExist();

        //Check changed data
        $course = Course::find($this->course->id);

        $this->assertNotNull($course);
        $this->assertEquals($course->name, 'Test yz');

        $description = 'This is a testcourse that shows the right functionality of the controller.';
        $this->assertEquals($course->description, $description);

        $catalog = $course->catalog()->first();
        $this->assertEquals($catalog->name, 'Test yz');

        $this->assertCount(1, $course->learngroups()->get());
        $group = $course->learngroups()->first();
        $this->assertEquals($group->name, 'Group Test yz');
        
        $check = $this->isSavedAsFavorite($catalog2->id);
        $this->assertTrue($check);
    }


    /**
     * Test edit course with valid data and from group to groups
     *
     * @depends testCourseEditPostWithValidDataAndFromGroupToGroup
     */
    public function testCourseEditPostWithValidDataAndFromGroupToGroups() {
        $group_new1 = Learngroup::where('name', 'LIKE', 'Group Test xz')->first();
        $group_new2 = Learngroup::where('name', 'LIKE', 'Group Test yz')->first();

        $post_data = array(
            'id'            => $this->course->id,
            'name'          => 'Test yz',
            'description'   => 'This is a testcourse that shows the right functionality of the controller.',
            'groups'         => array($group_new1->id, $group_new2->id)
        );
        $response = $this->post('course/edit', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('course/' . $this->course->id, $response);
        $this->checkIfNoErrorsExist();

        //Check changed data
        $course = Course::find($this->course->id);

        $this->assertNotNull($course);
        $this->assertEquals($course->name, 'Test yz');

        $description = 'This is a testcourse that shows the right functionality of the controller.';
        $this->assertEquals($course->description, $description);

        $catalog = $course->catalog()->first();
        $this->assertEquals($catalog->name, 'Test yz');

        $groups = $course->learngroups()->get();
        $this->assertCount(2, $groups);
        
        foreach($groups as $group) {
            $this->assertContains($group->name, array('Group Test xz', 'Group Test yz'));
        }
    }


    /**
     * Test edit course with valid data and from group to no group
     *
     * @depends testCourseEditPostWithValidDataAndFromGroupToGroups
     */
    public function testCourseEditPostWithValidDataAndFromGroupToNoGroup() {
        $post_data = array(
            'id'            => $this->course->id,
            'name'          => 'Test yz',
            'description'   => 'This is a testcourse that shows the right functionality of the controller.',
            'groups'         => ''
        );
        $response = $this->post('course/edit', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('course/' . $this->course->id, $response);
        $this->checkIfNoErrorsExist();

        //Check changed data
        $course = Course::find($this->course->id);

        $this->assertNotNull($course);
        $this->assertEquals($course->name, 'Test yz');
        
        $description = 'This is a testcourse that shows the right functionality of the controller.';
        $this->assertEquals($course->description, $description);

        $catalog = $course->catalog()->first();
        $this->assertEquals($catalog->name, 'Test yz');

        $group = $course->learngroups()->first();
        $this->assertNull($group);
    }


    /**
     * Test edit course with valid data and from no group to group
     *
     * @depends testCourseEditPostWithValidDataAndFromGroupToNoGroup
     */
    public function testCourseEditPostWithValidDataAndFromNoGroupToGroup() {
        $group_new = Learngroup::where('name', 'LIKE', 'Group Test yz')->first();

        $this->course->learngroups()->delete();

        $post_data = array(
            'id'            => $this->course->id,
            'name'          => 'Test yz',
            'description'   => 'This is a testcourse that shows the right functionality of the controller.',
            'groups'         => array($group_new->id)
        );
        $response = $this->post('course/edit', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('course/' . $this->course->id, $response);
        $this->checkIfNoErrorsExist();

        //Check changed data
        $course = Course::find($this->course->id);

        $this->assertNotNull($course);
        $this->assertEquals($course->name, 'Test yz');

        $description = 'This is a testcourse that shows the right functionality of the controller.';
        $this->assertEquals($course->description, $description);

        $catalog = $course->catalog()->first();
        $this->assertEquals($catalog->name, 'Test yz');

        $group = $course->learngroups()->first();
        $this->assertEquals($group->name, 'Group Test yz');
    }


    /**
     * Test edit course with valid data and from no group to groups
     *
     * @depends testCourseEditPostWithValidDataAndFromNoGroupToGroup
     */
    public function testCourseEditPostWithValidDataAndFromNoGroupToGroups() {
        $group_new1 = Learngroup::where('name', 'LIKE', 'Group Test xz')->first();
        $group_new2 = Learngroup::where('name', 'LIKE', 'Group Test yz')->first();

        $this->course->learngroups()->delete();

        $post_data = array(
            'id'            => $this->course->id,
            'name'          => 'Test yz',
            'description'   => 'This is a testcourse that shows the right functionality of the controller.',
            'groups'         => array($group_new1->id, $group_new2->id)
        );
        $response = $this->post('course/edit', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('course/' . $this->course->id, $response);
        $this->checkIfNoErrorsExist();

        //Check changed data
        $course = Course::find($this->course->id);

        $this->assertNotNull($course);
        $this->assertEquals($course->name, 'Test yz');

        $description = 'This is a testcourse that shows the right functionality of the controller.';
        $this->assertEquals($course->description, $description);

        $catalog = $course->catalog()->first();
        $this->assertEquals($catalog->name, 'Test yz');

        $groups = $course->learngroups()->get();
        $this->assertCount(2, $groups);
        
        foreach($groups as $group) {
            $this->assertContains($group->name, array('Group Test xz', 'Group Test yz'));
        }
    }


    /**
     * Test delete course with not an existing id
     */
    public function testCourseDeleteNotExistingID() {
        $id = $this->getNotExistingID('Course');
        $response = $this->get('course/' . $id . '/delete');
        $this->assertEquals('404', $response->foundation->getStatusCode());
    }


    /**
     * Test delete course with valid data
     *
     * @depends testCourseDeleteNotExistingID
     */
    public function testCourseDeleteWithValidData() {
        $response = $this->get('course/' . $this->course->id . '/delete');
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('courses', $response);

        $course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();
        $catalog = Catalog::where('name', 'LIKE', 'Catalog of course 1 -  group Test xy')->first();

        $this->assertNull($course);
        $this->assertNull($catalog);
    }


    /**
     * Test delete course with valid data and favorites
     *
     * @depends testCourseDeleteWithValidData
     */
    public function testCourseDeleteWithValidDataAndFavorites() {
        //Add favorite
        $userID = Sentry::user()->get('id');
        $user = User::find($userID);
        $course = Course::where('name', 'LIKE', 'Course 2 of group Test xy')->first();
        $catalog = $course->catalog()->first();
        $catalog2 = $catalog->children()->first();
        $user->favorites()->attach($catalog2);

        $response = $this->get('course/' . $course->id . '/delete');
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('courses', $response);

        //Check if favorite is saved
        $courseCheck = Course::where('name', 'LIKE', 'Course 2 of group Test xy')->first();
        $this->assertNull($courseCheck);

        $check = $this->isSavedAsFavorite($catalog2->id);
        $this->assertFalse($check);
    }


    /**
     * Test the view to show a course was deleted
     */
    public function testCourseDeletedView() {
        $response = $this->get('course/deleted');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }

}