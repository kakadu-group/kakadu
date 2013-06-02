<?php

require_once 'testcase.course.php';

class TestGroupWithPermissions extends CourseTestCase {

    protected $names_create = array('Test xy');
    protected $names_delete = array('Test xy', 'Test yz');

    private $group = null;


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
            Sentry::login('alex@example.com', 'password');
        } catch (Sentry\SentryException $e) {
            printf($e->getMessage());
        }
    }


    public function tearDown() {
        try {
            if(Sentry::user_exists('georg@example.com')) {
                Sentry::user('georg@example.com', true)->delete();
            }
        } catch (Sentry\SentryException $e) {
            printf($e->getMessage());
        }

        parent::tearDown();
    }


    /**
     * Test the view to show all groups
     */
    public function testGroupsView() {
        $response = $this->get('groups');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test the view to show all groups with ajax
     */
    public function testGroupsViewWithAjax() {
        $response = $this->ajax_get('groups');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test the view to show all groups with pagination and sorting
     */
    public function testGroupsViewWithPaginationAndSorting() {
        $get_data = array(
            'page'      => '2',
            'per_page'  => '1',
            'sort'      => 'created_at',
            'sort_dir'  => 'desc'
        );
        $response = $this->get('groups', $get_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $data = $response->content->data['content']->data();
        $this->assertArrayHasKey('groups', $data);
        $this->assertCount(1, $data['groups']);
    }


    /**
     * Test the view to get all groups with ajax, pagination and sorting
     */
    public function testGroupsViewWithAjaxPaginationAndSorting() {
        $get_data = array(
            'page'      => '2',
            'per_page'  => '1',
            'sort'      => 'created_at',
            'sort_dir'  => 'desc'
        );
        $response = $this->ajax_get('groups', $get_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $data = $response->content->data();
        $this->assertArrayHasKey('groups', $data);
        $this->assertCount(1, $data['groups']);
    }


    /**
     * Test the view to show a existing group
     */
    public function testGroupViewExistingID() {
        $response = $this->get('group/' . $this->group->id);
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test the view to show a not existing group
     */
    public function testGroupViewNotExistingID() {
        $id = $this->getNotExistingID('Group');
        $response = $this->get('group/' . $id);
        $this->assertEquals('404', $response->foundation->getStatusCode());
    }


    /**
     * Test the view to create a group
     */
    public function testGroupCreateView() {
        $response = $this->get('group/create');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test create group with a too short data
     *
     * @depends testGroupCreateView
     */
    public function testGroupCreatePostWithTooShortData() {
        $post_data = array(
            'name'        => 'Test',
            'description' => 'Test'
        );
        $response = $this->post('group/create', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('group/create', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test create group with valid data
     *
     * @depends testGroupCreatePostWithTooShortData
     */
    public function testGroupCreatePostWithValidData() {
        $post_data = array(
            'name'        => 'Group Test yz',
            'description' => 'This is a testgroup that shows the right functionality of the controller.'
        );
        $response = $this->post('group/create', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('group/[0-9]+', $response);
        $this->checkIfNoErrorsExist();

        //Reset Sentry cache
        $this->resetSentry();

        //Check if user is in groups
        $url = explode('group/', $response->headers()->get('location'));
        
        $user = Sentry::user('alex@example.com');
        $role = Role::where('name', 'LIKE', 'admin')->first();

        $allocation = $this->group->users()->pivot()
                            ->where('user_id', '=', $user->get('id'))
                            ->where('role_id', '=', $role->id)
                            ->first();

        $this->assertNotNull($allocation);
    }


    /**
     * Test the view to edit a existing group
     */
    public function testGroupEditExistingID() {
        $response = $this->get('group/' . $this->group->id . '/edit');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test the view to edit a not existing group
     */
    public function testGroupEditNotExistingID() {
        $id = $this->getNotExistingID('Group');
        $response = $this->get('group/' . $id . '/edit');
        $this->assertEquals('404', $response->foundation->getStatusCode());
    }


    /**
     * Test edit group with not existing id
     */
    public function testGroupEditPostWithNotExistingID() {
        $id = $this->getNotExistingID('Group');
        $post_data = array(
            'id'            => $id,
            'name'          => 'Test yz',
            'description'   => 'This is a testgroup that shows the right functionality of the controller.'
        );
        $response = $this->post('group/edit', $post_data);
        $this->assertEquals('404', $response->foundation->getStatusCode());
    }


    /**
     * Test edit group with too short data
     *
     * @depends testGroupEditPostWithNotExistingID
     */
    public function testGroupEditPostWithTooShortData() {
        $post_data = array(
            'id'            => $this->group->id,
            'name'          => 'Test',
            'description'   => 'Test'
        );
        $response = $this->post('group/edit', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkIfErrorsExist();
    }


    /**
     * Test edit group with valid data
     *
     * @depends testGroupEditPostWithTooShortData
     */
    public function testGroupEditPostWithValidData() {
        $post_data = array(
            'id'            => $this->group->id,
            'name'          => 'Group Test yz',
            'description'   => 'This is a testgroup that shows the right functionality of the controller.'
        );
        $response = $this->post('group/edit', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('group/' . $this->group->id, $response);
        $this->checkIfNoErrorsExist();

        //Check changed data
        $group = Learngroup::find($this->group->id);

        $this->assertNotNull($group);
        $this->assertEquals($group->name, 'Group Test yz');

        $description = 'This is a testgroup that shows the right functionality of the controller.';
        $this->assertEquals($group->description, $description);
    }


    /**
     * Test delete group with not an existing id
     */
    public function testGroupDeleteNotExistingID() {
        $id = $this->getNotExistingID('Group');
        $response = $this->get('group/' . $id . '/delete');
        $this->assertEquals('404', $response->foundation->getStatusCode());
    }


    /**
     * Test delete group with valid data
     *
     * @depends testGroupDeleteNotExistingID
     */
    public function testGroupDeleteWithValidData() {
        $response = $this->get('group/' . $this->group->id . '/delete');
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('groups', $response);

        $group = Learngroup::where('name', 'LIKE', 'Group Test xy')->first();
        $this->assertNull($group);

        $course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();
        $this->assertNull($course);

        //Reset Sentry cache
        $this->resetSentry();
    }


    /**
     * Test delete group with valid data and course with two learngroups
     *
     * @depends testGroupDeleteWithValidData
     */
    public function testGroupDeleteWithValidDataAndCourseWithTwoLearngroups() {
        $group = new Learngroup();
        $group->name = 'Group Test yz';
        $group->description = 'This is the description of group Test yz. It has to be very long.';
        $group->save();

        $course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();
        $course->learngroups()->attach($group);

        $response = $this->get('group/' . $this->group->id . '/delete');
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('groups', $response);

        $group = Learngroup::where('name', 'LIKE', 'Group Test xy')->first();
        $this->assertNull($group);

        $course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();
        $this->assertNotNull($course);
        $this->assertEquals(1, $course->learngroups()->count());

        //Reset Sentry cache
        $this->resetSentry();
    }


    /**
     * Test the view to show a group was deleted
     */
    public function testGroupDeletedView() {
        $response = $this->get('group/deleted');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }

}