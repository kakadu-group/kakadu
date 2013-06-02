<?php

require_once 'testcase.controller.php';

abstract class UserTestCase extends ControllerTestCase
{

    public function setUp() {
        parent::setUp();
        Bundle::start('sentry');

        try {
            $user = array(
                'email'     => 'alex@example.com',
                'password'  => 'password',
                'metadata'  => array(
                    'displayname'   => 'Alex',
                    'language'      => 'en'
                )
            );
            
            Sentry::user()->create($user);
        } catch (Sentry\SentryException $e) {
            printf($e->getMessage());
        }


        $this->resetSentry();
    }


    public function tearDown() {
        Sentry::logout();

        try {
            if(Sentry::user_exists('alex@example.com')) {
                Sentry::user('alex@example.com', true)->delete();
            }
        } catch(Sentry\SentryException $e) {
            printf($e->getMessage());
        }

        parent::tearDown();
    }

    /**
     * Reset the sentry cache
     */
    public function resetSentry() {
        //Reset Sentry cache
        $sentry = new ReflectionClass('Sentry');

        $current_user = $sentry->getProperty('current_user');
        $current_user->setAccessible(true);
        $current_user->setValue(null);

        $user_cache = $sentry->getProperty('user_cache');
        $user_cache->setAccessible(true);
        $user_cache->setValue(array());
    }

}