<?php

require_once 'testcase.user.php';

class TestProfileWithPermissions extends UserTestCase {

    public function setUp() {
        parent::setUp();

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
                Sentry::user('georg@example.com', TRUE)->delete();
            }
        } catch (Sentry\SentryException $e) {
            printf($e->getMessage());
        }

        try {
            if(Sentry::user_exists('a@example.com')) {
                Sentry::user('a@example.com', TRUE)->delete();
            }
        } catch(Sentry\SentryException $e) {
            printf($e->getMessage());
        }

        parent::tearDown();
    }
    


    /**
     * Test the profile edit view.
     */
    public function testProfileEditView() {
        $response = $this->get('profile/edit');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test profile edit with no data.
     *
     * @depends testProfileEditView
     */
    public function testProfileEditWithNoData() {
        $response = $this->post('profile/edit');
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('profile/edit', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test profile edit with not valid email.
     *
     * @depends testProfileEditWithNoData
     */
    public function testProfileEditWithNotValidEmail() {
        $post_data = array(
            'displayname'   => 'Alex',
            'email'         => 'email',
            'language'      => 'en'
        );
        $response = $this->post('profile/edit', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('profile/edit', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test profile edit with existing email.
     *
     * @depends testProfileEditWithNoData
     */
    public function testProfileEditWithExistingEmail() {
        $post_data = array(
            'displayname'   => 'Alex',
            'email'         => 'georg@example.com',
            'language'      => 'en'
        );
        $response = $this->post('profile/edit', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('profile/edit', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test profile edit with valid data.
     *
     * @depends testProfileEditWithExistingEmail
     */
    public function testProfileEditWithValidData() {
        $post_data = array(
            'displayname'   => 'Alex',
            'email'         => 'a@example.com',
            'language'      => 'de'
        );
        $response = $this->post('profile/edit', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('profile/edit', $response);
        $this->checkIfNoErrorsExist();
    }


    /**
     * Test profile change password with no data.
     *
     * @depends testProfileEditWithValidData
     */
    public function testProfileChangePasswordWithNoData() {
        $response = $this->post('profile/changepassword');
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('profile/edit', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test profile change password with wrong old password.
     *
     * @depends testProfileChangePasswordWithNoData
     */
    public function testProfileChangePasswordWithWrongOldPassword() {
        $post_data = array(
            'password_old' => 'pass',
            'password' => 'new_password',
            'password_confirmation' => 'new_password'
        );
        $response = $this->post('profile/changepassword', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('profile/edit', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test profile change password with wrong confirmation password.
     *
     * @depends testProfileChangePasswordWithWrongOldPassword
     */
    public function testProfileChangePasswordWithWrongConfirmationPassword() {
        $post_data = array(
            'password_old' => 'password',
            'password' => 'new_password',
            'password_confirmation' => 'new_pass'
        );
        $response = $this->post('profile/changepassword', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('profile/edit', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test profile change password with valid data.
     *
     * @depends testProfileChangePasswordWithWrongConfirmationPassword
     */
    public function testProfileChangePasswordWithValidData() {
        $post_data = array(
            'password_old' => 'password',
            'password' => 'new_password',
            'password_confirmation' => 'new_password'
        );
        $response = $this->post('profile/changepassword', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('profile/edit', $response);
        $this->checkIfNoErrorsExist();
    }


    /**
     * Test the profile delete view.
     */
    public function testProfileDeleteView() {
        $response = $this->get('profile/delete');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test the profile delete.
     *
     * @depends testProfileDeleteView
     */
    public function testProfileDelete() {
        $response = $this->delete('profile/delete');
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('/', $response);
        $this->checkIfNoErrorsExist();

        //Check if user is logged in and exists
        $this->assertFalse(Sentry::check());
        $this->assertFalse(Sentry::user_exists('alex@example.com'));
    }

}