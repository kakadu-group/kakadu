<?php

require_once 'testcase.user.php';

class TestAuthentificationLogin extends UserTestCase {

    public function setUp() {
        parent::setUp();
        Bundle::start('phpmailer');
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
     * Test the register view
     */
    public function testRegisterView() {
        $response = $this->get('auth/register');

        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test register with no data.
     *
     * @depends testRegisterView
     */
    public function testRegisterWithNoData()
    {
        $post_data = array();
        $response = $this->post('auth/register', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('auth/register', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test register with displayname.
     *
     * @depends testRegisterWithNoData
     */
    public function testRegisterWithDisplayname()
    {
        $post_data = array(
            'displayname' => 'Georg'
        );
        $response = $this->post('auth/register', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('auth/register', $response);
        $this->checkIfErrorsExist();
    }

    
    /**
     * Test register with email.
     *
     * @depends testRegisterWithDisplayname
     */
    public function testRegisterWithEmail()
    {
        $post_data = array(
            'email' => 'georg@example.com'
        );
        $response = $this->post('auth/register', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('auth/register', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test register with password.
     *
     * @depends testRegisterWithEmail
     */
    public function testRegisterWithPassword()
    {
        $post_data = array(
            'password'              => 'password',
            'password_confirmation' => 'password'
        );
        $response = $this->post('auth/register', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('auth/register', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test register with valid data.
     *
     * @depends testRegisterWithPassword
     */
    public function testRegisterWithValidDataAndActivation()
    {
        $post_data = array(
            'displayname'           => 'Georg',
            'email'                 => 'georg@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password'
        );
        $response = $this->post('auth/register', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('auth/confirmemail', $response);
        $this->checkIfNoErrorsExist();


        //Clear the old request
        $_POST = array();
        $_SERVER = array();
        Request::$route = null;
        Session::$instance = null;
        Session::load();


        //Activate user
        $mailer = IoC::resolve('phpmailer');

        $link = explode('auth/activate', $mailer->Body, 2);
        $response = $this->get('auth/activate' . $link[1]);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('auth/activate', $response);
        $this->checkIfNoErrorsExist();
    }


    /**
     * Test the confirmemail view
     */
    public function testConfirmemailView() {
        $response = $this->get('auth/confirmemail');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test the forgot password view
     */
    public function testForgotPasswordView() {
        $response = $this->get('auth/forgotpassword');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test forgot password with no data.
     *
     * @depends testForgotPasswordView
     */
    public function testForgotPasswordWithNoData()
    {
        $post_data = array();
        $response = $this->post('auth/forgotpassword', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('auth/forgotpassword', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test forgot password with valid data.
     *
     * @depends testForgotPasswordWithNoData
     */
    public function testForgotPasswordWithValidData()
    {
        $post_data = array(
            'email' => 'alex@example.com'
        );
        $response = $this->post('auth/forgotpassword', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('auth/forgotpassword', $response);
        $this->checkIfNoErrorsExist();
        
        //Check login with old password
        $this->assertFalse(Sentry::login('alex@example.com', 'password', false));
    }

}