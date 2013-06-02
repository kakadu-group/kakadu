<?php

require_once 'testcase.controller.php';

class TestProfileWithoutPermissions extends ControllerTestCase {

    public function setUp() {
        parent::setUp();
        Bundle::start('sentry');
        Sentry::logout();
    }



    /**
     * Test the profile edit view.
     */
    public function testProfileEditView() {
        $response = $this->get('profile/edit');
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('auth/login', $response);
    }


    /**
     * Test the profile edit post.
     */
    public function testProfileEditPost() {
        $response = $this->post('profile/edit');
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('auth/login', $response);
    }


    /**
     * Test the profile change password post.
     */
    public function testProfileChangePasswordPost() {
        $response = $this->post('profile/changepassword');
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('auth/login', $response);
    }


    /**
     * Test the profile delete view.
     */
    public function testProfileDeleteView() {
        $response = $this->get('profile/delete');
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('auth/login', $response);
    }


    /**
     * Test the profile delete.
     */
    public function testProfileDelete() {
        $response = $this->delete('profile/delete');
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('auth/login', $response);
    }

}