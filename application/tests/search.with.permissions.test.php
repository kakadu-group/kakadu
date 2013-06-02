<?php

require_once 'testcase.user.php';

class TestSearchWithPermissions extends UserTestCase {

    public function setUp() {
        parent::setUp();

        $group1 = new Learngroup;
        $group1->name = 'Group xy';
        $group1->description = 'Group xy';
        $group1->save();

        $group2 = new Learngroup;
        $group2->name = 'Group xz';
        $group2->description = 'Group xz';
        $group2->save();

        try {
            $user = array(
                'email'     => 'lex@example.com',
                'password'  => 'password',
                'metadata'  => array(
                    'displayname'   => 'Lex',
                    'language'      => 'de'
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
        Learngroup::where('name', 'LIKE', 'Group x%')->delete();

        try {
            if(Sentry::user_exists('lex@example.com')) {
                Sentry::user('lex@example.com', TRUE)->delete();
            }
        } catch(Sentry\SentryException $e) {
            printf($e->getMessage());
        }

        parent::tearDown();
    }


    /**
     * Test ajax search user with no data
     */
    public function testUserAjaxWithNoData() {
        $response = $this->ajax_post('users/search');
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax search user with valid result
     */
    public function testUserAjaxWithValidResult() {
        $post_data = array(
            'search'    => 'lex'
        );
        $response = $this->ajax_post('users/search', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Ok"', $content);
        $this->assertContains('users', $content);
        $this->assertEquals(2, substr_count($content, 'id'));
    }


    /**
     * Test ajax search group with no data
     */
    public function testGroupAjaxWithNoData() {
        $response = $this->ajax_post('groups/search');
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax search group with valid result
     */
    public function testGroupAjaxWithValidResult() {
        $post_data = array(
            'search'    => 'Group x'
        );
        $response = $this->ajax_post('groups/search', $post_data);

        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Ok"', $content);
        $this->assertContains('groups', $content);
        $this->assertEquals(2, substr_count($content, 'id'));
    }

}