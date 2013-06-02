<?php

require_once 'testcase.course.php';

class TestGroupMemberWithPermissions extends CourseTestCase {

    protected $names_create = array('Test xy', 'Test yz');
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
     * Test ajax user add to group with not valid data
     */
    public function testAjaxGroupUserAddWithNotValidData() {
        $post_data = array(
            'id'    => $this->group->id
        );
        $response = $this->ajax_post('group/user/add', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax user add to group with not existing group
     *
     * @depends testAjaxGroupUserAddWithNotValidData
     */
    public function testAjaxGroupUserAddWithNotExistingGroup() {
        $id = $this->getNotExistingID('Group');

        $post_data = array(
            'id'    => $id,
            'user'  => 'georg@example.com'
        );
        $response = $this->ajax_post('group/user/add', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax user add to group with not existing user
     *
     * @depends testAjaxGroupUserAddWithNotExistingGroup
     */
    public function testAjaxGroupUserAddWithNotExistingUser() {
        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'g@example.com'
        );
        $response = $this->ajax_post('group/user/add', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax user add to group with valid data
     *
     * @depends testAjaxGroupUserAddWithNotExistingUser
     */
    public function testAjaxGroupUserAddWithValidData() {
        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'georg@example.com'
        );
        $response = $this->ajax_post('group/user/add', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Ok"', $content);


        //Reset Sentry cache
        $this->resetSentry();

        //Check if is in group
        $user = Sentry::user('georg@example.com');
        $role = Role::where('name', 'LIKE', 'member')->first();

        $allocation = $this->group->users()->pivot()
                            ->where('user_id', '=', $user->get('id'))
                            ->where('role_id', '=', $role->id)
                            ->first();

        $this->assertNotNull($allocation);
    }


    /**
     * Test ajax user remove to group with not valid data
     */
    public function testAjaxGroupUserRemoveWithNotValidData() {
        $post_data = array(
            'id'    => $this->group->id
        );
        $response = $this->ajax_post('group/user/remove', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax user remove to group with not existing group
     *
     * @depends testAjaxGroupUserRemoveWithNotValidData
     */
    public function testAjaxGroupUserRemoveWithNotExistingGroup() {
        $id = $this->getNotExistingID('Group');

        $post_data = array(
            'id'    => $id,
            'user'  => 'georg@example.com'
        );
        $response = $this->ajax_post('group/user/remove', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax user remove to group with not existing user
     *
     * @depends testAjaxGroupUserRemoveWithNotExistingGroup
     */
    public function testAjaxGroupUserRemoveWithNotExistingUser() {
        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'g@example.com'
        );
        $response = $this->ajax_post('group/user/add', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax user remove to group with valid data
     *
     * @depends testAjaxGroupUserRemoveWithNotExistingUser
     */
    public function testAjaxGroupUserRemovedWithValidData() {
        $user_sentry = Sentry::user('georg@example.com');
        $user = User::find($user_sentry->get('id'));
        $role = Role::where('name', 'LIKE', 'member')->first();
        $this->group->users()->attach($user, array('role_id' => $role->id));

        //Add favorite
        $course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();
        $catalog = $course->catalog()->first();
        $catalog2 = $catalog->children()->first();
        $user->favorites()->attach($catalog2);

        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'georg@example.com'
        );
        $response = $this->ajax_post('group/user/remove', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Ok"', $content);


        //Reset Sentry cache
        $this->resetSentry();

        //Check if is in group
        $userSentry = Sentry::user('georg@example.com');
        $user = User::find($userSentry->get('id'));
        $allocation = $this->group->users()->pivot()->where('user_id', '=', $user->id)->first();
        $this->assertNull($allocation);

        //Check favorites
        $check = $this->isSavedAsFavorite($catalog2->id, $user);
        $this->assertFalse($check);
    }


    /**
     * Test ajax user remove to group with valid data and two related learngroups
     *
     * @depends testAjaxGroupUserRemovedWithValidData
     */
    public function testAjaxGroupUserRemovedWithValidDataAndTwoRelatedLearngroups() {
        $user_sentry = Sentry::user('georg@example.com');
        $user = User::find($user_sentry->get('id'));

        $role = Role::where('name', 'LIKE', 'member')->first();
        $this->group->users()->attach($user, array('role_id' => $role->id));

        $group2 = Learngroup::where('name', 'LIKE', 'Group Test yz')->first();
        $group2->users()->attach($user, array('role_id' => $role->id));

        //Add second learngroup
        $course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();
        $course->learngroups()->attach($group2);

        //Add favorite
        $catalog = $course->catalog()->first();
        $catalog2 = $catalog->children()->first();
        $user->favorites()->attach($catalog2);
        
        $check = $this->isSavedAsFavorite($catalog2->id, $user);
        $this->assertTrue($check);

        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'georg@example.com'
        );
        $response = $this->ajax_post('group/user/remove', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Ok"', $content);

        //Reset Sentry cache
        $this->resetSentry();

        //Check if is in group
        $userSentry = Sentry::user('georg@example.com');
        $user = User::find($userSentry->get('id'));
        $allocation = $this->group->users()->pivot()->where('user_id', '=', $user->id)->first();
        $this->assertNull($allocation);

        //Check favorites
        $check = $this->isSavedAsFavorite($catalog2->id, $user);
        $this->assertTrue($check);
    }


    /**
     * Test ajax admin add to group with not valid data
     */
    public function testAjaxGroupAdminAddWithNotValidData() {
        $post_data = array(
            'id'    => $this->group->id
        );
        $response = $this->ajax_post('group/admin/add', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax admin add to group with not existing group
     *
     * @depends testAjaxGroupAdminAddWithNotValidData
     */
    public function testAjaxGroupAdminAddWithNotExistingGroup() {
        $id = $this->getNotExistingID('Group');

        $post_data = array(
            'id'    => $id,
            'user'  => 'georg@example.com'
        );
        $response = $this->ajax_post('group/admin/add', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax admin add to group with not existing user
     *
     * @depends testAjaxGroupAdminAddWithNotExistingGroup
     */
    public function testAjaxGroupAdminAddWithNotExistingUser() {
        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'g@example.com'
        );
        $response = $this->ajax_post('group/admin/add', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax admin add to group with valid data
     *
     * @depends testAjaxGroupAdminAddWithNotExistingUser
     */
    public function testAjaxGroupAdminAddWithValidData() {
        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'georg@example.com'
        );
        $response = $this->ajax_post('group/admin/add', $post_data);

        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Ok"', $content);


        //Reset Sentry cache
        $this->resetSentry();

        //Check if is in group
        $user = Sentry::user('georg@example.com');
        $role = Role::where('name', 'LIKE', 'admin')->first();

        $allocation = $this->group->users()->pivot()
                            ->where('user_id', '=', $user->get('id'))
                            ->where('role_id', '=', $role->id)
                            ->first();

        $this->assertNotNull($allocation);
    }


    /**
     * Test ajax admin remove to group with not valid data
     */
    public function testAjaxGroupAdminRemoveWithNotValidData() {
        $post_data = array(
            'id'    => $this->group->id
        );
        $response = $this->ajax_post('group/admin/remove', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax admin remove to group with not existing group
     *
     * @depends testAjaxGroupAdminRemoveWithNotValidData
     */
    public function testAjaxGroupAdminRemoveWithNotExistingGroup() {
        $id = $this->getNotExistingID('Group');

        $post_data = array(
            'id'    => $id,
            'user'  => 'georg@example.com'
        );
        $response = $this->ajax_post('group/admin/remove', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax admin remove to group with not existing user
     *
     * @depends testAjaxGroupAdminRemoveWithNotExistingGroup
     */
    public function testAjaxGroupAdminRemoveWithNotExistingUser() {
        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'g@example.com'
        );
        $response = $this->ajax_post('group/admin/add', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax admin remove to group with valid data
     *
     * @depends testAjaxGroupAdminRemoveWithNotExistingUser
     */
    public function testAjaxGroupAdminRemovedWithValidData() {
        $user_sentry = Sentry::user('georg@example.com');
        $user_kakadu = User::find($user_sentry->get('id'));
        $role = Role::where('name', 'LIKE', 'admin')->first();
        $this->group->users()->attach($user_kakadu, array('role_id' => $role->id));

        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'georg@example.com'
        );
        $response = $this->ajax_post('group/admin/remove', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Ok"', $content);


        //Reset Sentry cache
        $this->resetSentry();

        //Check if is in group
        $user = Sentry::user('georg@example.com');
        $allocation = $this->group->users()->pivot()->where('user_id', '=', $user->get('id'))->first();
        $role = Role::where('name', 'LIKE', 'member')->first();
        $this->assertEquals($allocation->role_id, $role->id);
    }

}