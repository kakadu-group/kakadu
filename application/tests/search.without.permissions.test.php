<?php

require_once 'testcase.user.php';

class TestSearchWithoutPermissions extends UserTestCase {


    /**
     * Test ajax search user
     */
    public function testUserAjax() {
        $post_data = array(
            'search'    => 'lex'
        );
        $response = $this->ajax_post('users/search', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax search groups
     */
    public function testGroupAjax() {
        $post_data = array(
            'search'    => 'group'
        );
        $response = $this->ajax_post('groups/search', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }

}