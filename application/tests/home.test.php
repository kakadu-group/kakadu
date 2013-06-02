<?php

require_once 'testcase.user.php';

class TestHome extends UserTestCase {


    /**
     * Test the home view
     */
    public function testHomeView() {
        $response = $this->get('/');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test the help view
     */
    public function testHelpView() {
        $response = $this->get('help');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }

}