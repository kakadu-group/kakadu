<?php

require_once 'testcase.user.php';

class TestLanguage extends UserTestCase {

    /**
     * Test the change language post with no data.
     */
    public function testChangeLanguage() {
        $response = $this->post('language/edit');

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('/', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test the change language post with not falid language.
     *
     * @depends testChangeLanguage
     */
    public function testChangeLanguageWithNotFalidLanguage() {
        $post_data = array(
            'language' => 'xy'
        );
        $response = $this->post('language/edit', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('/', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test the change language post as guest.
     *
     * @depends testChangeLanguageWithNotFalidLanguage
     */
    public function testChangeLanguageAsGuest() {
        //Check if not logged in
        $this->assertFalse(Sentry::check());

        //Change language
        $post_data = array(
            'language' => 'de'
        );
        $response = $this->post('language/edit', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('/', $response);
        $this->checkIfNoErrorsExist();

        //Check set language
        $this->assertEquals('de', Cookie::get('language'));
    }


    /**
     * Test the change language post as user.
     *
     * @depends testChangeLanguageAsGuest
     */
    public function testChangeLanguageAsUser() {
        //Log in
        try {
            Sentry::login('alex@example.com', 'password');
        } catch(Sentry\SentryException $e) {
            print($e->getMessage());
        }

        //Check if not logged in
        $this->assertTrue(Sentry::check());

        //Change language
        $post_data = array(
            'language' => 'de'
        );
        $response = $this->post('language/edit', $post_data);
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('/', $response);
        $this->checkIfNoErrorsExist();

        //Check set language
        $this->assertEquals('de', Cookie::get('language'));

        try {
            $user = Sentry::user();
            $this->assertEquals('de', $user->get('metadata.language'));
        } catch(Sentry\SentryException $e) {
            print($e->getMessage());
        }
    }

}