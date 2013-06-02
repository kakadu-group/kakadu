<?php

require_once 'testcase.course.php';

class TestGroupMemberWithoutPermissions extends CourseTestCase {

    protected $names_create = array('Test xy');
    protected $names_delete = array('Test xy');

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
     * Test ajax user add to group with valid data
     */
    public function testAjaxGroupUserAddWithValidData() {
        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'georg@example.com'
        );
        $response = $this->ajax_post('group/user/add', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax user remove to group with valid data
     */
    public function testAjaxGroupUserRemovedWithValidData() {
        $user_sentry = Sentry::user('georg@example.com');
        $user_kakadu = User::find($user_sentry->get('id'));
        $role = Role::where('name', 'LIKE', 'member')->first();
        $this->group->users()->attach($user_kakadu, array('role_id' => $role->id));
        

        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'georg@example.com'
        );
        $response = $this->ajax_post('group/user/remove', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }

    /**
     * Test ajax admin add to group with valid data
     */
    public function testAjaxGroupAdminAddWithValidData() {
        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'georg@example.com'
        );
        $response = $this->ajax_post('group/admin/add', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax admin remove to group with valid data
     */
    public function testAjaxGroupAdminRemovedWithValidData() {
        $user_sentry = Sentry::user('georg@example.com');
        $user_kakadu = User::find($user_sentry->get('id'));
        $role = Role::where('name', 'LIKE', 'member')->first();
        $this->group->users()->attach($user_kakadu, array('role_id' => $role->id));
        

        $post_data = array(
            'id'    => $this->group->id,
            'user'  => 'georg@example.com'
        );
        $response = $this->ajax_post('group/admin/remove', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }

}