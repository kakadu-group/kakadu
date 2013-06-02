<?php

require_once 'testcase.user.php';

class TestLogin extends UserTestCase {

    /**
     * Test login with no data.
     */
    public function testLoginWithNoData()
    {
        $post_data = array();
        $response = $this->post('auth/login', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('/', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test login with just the email.
     *
     * @depands testLoginWithNoData
     */
    public function testLoginWithEmail()
    {
        $post_data = array(
            'email' => 'alex@example.com'
        );
        $response = $this->post('auth/login', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('/', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test login with just the password.
     *
     * @depands testLoginWithEmail
     */
    public function testLoginWithPassword()
    {
        $post_data = array(
            'password' => 'password'
        );
        $response = $this->post('auth/login', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('/', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test login with a invalid data.
     *
     * @depands
     */
    public function testLoginWithInvalidData()
    {
        $post_data = array(
            'email' => 'test@test.com',
            'password' => 'password'
        );
        $response = $this->post('auth/login', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('/', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test login with a valid data.
     *
     * @depands testLoginWithInvalidData
     */
    public function testLoginWithValidData()
    {
        $post_data = array(
            'email' => 'alex@example.com',
            'password' => 'password'
        );
        $response = $this->post('auth/login', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('/', $response);
        $this->checkIfNoErrorsExist();
        $this->assertTrue(Sentry::check());
    }


    /**
     * Test logout.
     */
    public function testLogout()
    {
        try {
            Sentry::login('alex@example.com', 'password');
        } catch(Sentry\SentryException $e) {
            print($e->getMessage());
        }

        //Check if user is logged in
        $this->assertTrue(Sentry::check());

        //Call logout site
        $response = $this->get('auth/logout');
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('/', $response);

        //User is logged out
        $this->assertFalse(Sentry::check());
    }

}