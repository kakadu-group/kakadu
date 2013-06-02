<?php

abstract class ControllerTestCase extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        Session::load();
    }

    public function tearDown() {
        $_POST = array();
        $_SERVER = array();
        Request::$route = NULL;
        Session::$instance = NULL;
    }


    /**
     * Make a GET request for a site
     *
     * @param  string $destination
     * @param  array  $get_data
     * @return \Laravel\Response
     */
    public function get($destination, $get_data = array()) {
        $this->clean_request();

        Request::setMethod('GET');
        Request::foundation()->headers->remove('X-Requested-With');
        Request::foundation()->request->add($get_data);
        Request::$route = Router::route('GET', $destination);

        return Request::$route->call();
    }


    /**
     * Make a POST request for a site
     *
     * @param  string $destination
     * @param  array  $post_data
     * @return \Laravel\Response
     */
    public function post($destination, $post_data = array()) {
        $this->clean_request();
        $this->setUpToken();

        Request::setMethod('POST');
        Request::foundation()->headers->remove('X-Requested-With');
        Request::foundation()->request->add($post_data);
        Request::$route = Router::route('POST', $destination);

        return Request::$route->call();
    }


    /**
     * Make a DELETE request for a site
     *
     * @param  string $destination
     * @param  array  $delete_data
     * @return \Laravel\Response
     */
    public function delete($destination, $delete_data = array()) {
        $this->clean_request();
        $this->setUpToken();

        Request::setMethod('DELETE');
        Request::foundation()->headers->remove('X-Requested-With');
        Request::foundation()->request->add($delete_data);
        Request::$route = Router::route('DELETE', $destination);

        return Request::$route->call();
    }


    /**
     * Make a AJAX GET request for a site
     *
     * @param  string $destination
     * @param  array  $get_data
     * @return \Laravel\Response
     */
    public function ajax_get($destination, $get_data = array()) {
        $this->clean_request();
        $this->setUpToken();

        Request::setMethod('GET');
        Request::foundation()->headers->add(array('X-Requested-With' => 'XMLHttpRequest'));
        Request::foundation()->request->add($get_data);
        Request::$route = Router::route('GET', $destination);

        return Request::$route->call();
    }


    /**
     * Make a AJAX POST request for a site
     *
     * @param  string $destination
     * @param  array  $post_data
     * @return \Laravel\Response
     */
    public function ajax_post($destination, $post_data = array()) {
        $this->clean_request();
        $this->setUpToken();

        Request::setMethod('POST');
        Request::foundation()->headers->add(array('X-Requested-With' => 'XMLHttpRequest'));
        Request::foundation()->request->add($post_data);
        Request::$route = Router::route('POST', $destination);

        return Request::$route->call();
    }


    /**
     * Sets a valid token for the post requests
     */
    private function setUpToken() {
        Request::foundation()->request->add(array('csrf_token' => Session::token()));
    }


    /**
     * Clean the old request informations
     */
    private function clean_request() {
        $request = Request::foundation()->request;

        foreach ($request->keys() as $key) {
            $request->remove($key);
        }
    }


    /**
     * Checks if the response has the right destination uri
     * @param  string $destination
     * @param  string $response
     */
    protected function checkResponseLocation($destination, $response) {
        $this->assertRegExp('#^[http://:/]+' . $destination . '$#', $response->headers()->get('location'));
    }


    /**
     * Check if errors exist
     */
    protected function checkIfErrorsExist() {
        $errors = Session::instance()->get('errors');
        $this->assertNotEmpty($errors);
    }

    
    /**
     * Check if no errors exist
     */
    protected function checkIfNoErrorsExist() {
        $errors = Session::instance()->get('errors');
        $this->assertEmpty($errors);
    }

}
