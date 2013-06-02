<?php

require_once 'testcase.course.php';

class TestCatalogWithPermissions extends CourseTestCase {

    protected $names_create = array('Test xy');
    protected $names_delete = array('Test xy');

    private $course = null;
    private $catalog = null;


    public function setUp() {
        parent::setUp();
        $this->course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();
        $this->catalog = $this->course->catalog()->first();

        //Login in
        try {
            Sentry::login('alex@example.com', 'password');
        } catch (Sentry\SentryException $e) {
            printf($e->getMessage());
        }
    }


    /**
     * Test the view to show a existing catalog
     */
    public function testCatalogViewExistingID() {
        $response = $this->get('catalog/' . $this->catalog->id);
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test the view to show a not existing catalog
     */
    public function testCatalogViewNotExistingID() {
        $id = $this->getNotExistingID('Catalog');
        $response = $this->get('catalog/' . $id);
        $this->assertEquals('404', $response->foundation->getStatusCode());
    }


    /**
     * Test the view to create a catalog
     */
    public function testCatalogCreateView() {
        $response = $this->get('course/' . $this->course->id . '/catalog/create');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test create catalog with a too short data
     *
     * @depends testCatalogCreateView
     */
    public function testCatalogCreatePostWithTooShortData() {
        $post_data = array(
            'course' => '',
            'name'   => '',
            'number' => '',
            'parent' => ''
        );
        $response = $this->post('catalog/create', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('course//catalog/create', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test create catalog with not valid parent
     *
     * @depends testCatalogCreatePostWithTooShortData
     */
    public function testCatalogCreatePostWithNotValidParent() {
        $parent = Catalog::where('name', 'LIKE', 'Catalog of course 2 -  group Test xy - chapter 1')->first();

        $post_data = array(
            'course' => $this->course->id,
            'name'   => 'Test yz',
            'number' => '1',
            'parent' => $parent->id
        );
        $response = $this->post('catalog/create', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('course/' . $this->course->id . '/catalog/create', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test create catalog with valid data
     *
     * @depends testCatalogCreatePostWithNotValidParent
     */
    public function testCatalogCreatePostWithValidData() {
        $post_data = array(
            'course' => $this->course->id,
            'name'   => 'Catalog of course Test xy - Chapter xy',
            'number' => '4',
            'parent' => $this->catalog->id
        );
        $response = $this->post('catalog/create', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('catalog/[0-9]+', $response);
        $this->checkIfNoErrorsExist();

        //Check the catalog
        $catalog = Catalog::where('name', 'LIKE', 'Catalog of course Test xy - Chapter xy')->first();
        $this->assertNotNull($catalog);
        $this->assertEquals(3, $catalog->number);
        $this->assertEquals($this->catalog->id, $catalog->parent);
    }


    /**
     * Test the view to edit a existing catalog
     */
    public function testCatalogEditWithExistingID() {
        $subcatalog = Catalog::where('name', 'LIKE', 'Catalog of course 1 -  group Test xy - chapter 1')->first();
        $response = $this->get('catalog/'. $subcatalog->id . '/edit');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }


    /**
     * Test the view to edit a not existing catalog
     */
    public function testCatalogEditWithNotExistingID() {
        $id = $this->getNotExistingID('Catalog');
        $response = $this->get('catalog/' . $id . '/edit');
        $this->assertEquals('404', $response->foundation->getStatusCode());
    }


    /**
     * Test edit course with not existing id
     */
    public function testCatalogEditPostWithNotExistingID() {
        $id = $this->getNotExistingID('Catalog');

        $post_data = array(
            'course' => $this->course->id,
            'id'     => $id,
            'name'   => 'Catalog of course Test xy - Chapter xy',
            'number' => '4',
            'parent' => $this->catalog->id
        );
        $response = $this->post('catalog/edit', $post_data);
        $this->assertEquals('404', $response->foundation->getStatusCode());
    }


    /**
     * Test edit catalog with too short data
     *
     * @depends testCatalogEditPostWithNotExistingID
     */
    public function testCatalogEditPostWithTooShortData() {
        $post_data = array(
            'course' => '',
            'id'     => '',
            'name'   => '',
            'number' => '',
            'parent' => ''
        );
        $response = $this->post('catalog/edit', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('catalog//edit', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test edit catalog with a catalog not a part of the course
     *
     * @depends testCatalogEditPostWithTooShortData
     */
    public function testCatalogEditPostWithNotCatalogOfCourse() {
        $other = Catalog::where('name', 'LIKE', 'Catalog of course 2 -  group Test xy - chapter 1')->first();

        $post_data = array(
            'course' => $this->course->id,
            'id'     => $other->id,
            'name'   => 'Test yz',
            'number' => '1',
            'parent' => $this->catalog->id
        );
        $response = $this->post('catalog/edit', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('catalog/' . $other->id . '/edit', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test edit catalog with a parent catalog not part of the course
     *
     * @depends testCatalogEditPostWithNotCatalogOfCourse
     */
    public function testCatalogEditPostWithNotParentCatalogOfCourse() {
        $subcatalog = Catalog::where('name', 'LIKE', 'Catalog of course 1 -  group Test xy - chapter 1')->first();
        $other = Catalog::where('name', 'LIKE', 'Catalog of course 2 -  group Test xy - chapter 1')->first();

        $post_data = array(
            'course' => $this->course->id,
            'id'     => $subcatalog->id,
            'name'   => 'Test yz',
            'number' => '1',
            'parent' => $other->id
        );
        $response = $this->post('catalog/edit', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('catalog/' . $subcatalog->id . '/edit', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test edit catalog with a not editable catalog
     *
     * @depends testCatalogEditPostWithNotParentCatalogOfCourse
     */
    public function testCatalogEditPostWithNotEditableCatalog() {
        $post_data = array(
            'course' => $this->course->id,
            'id'     => $this->catalog->id,
            'name'   => 'Test yz',
            'number' => '1',
            'parent' => $this->catalog->id
        );
        $response = $this->post('catalog/edit', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('catalog/' . $this->catalog->id . '/edit', $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test edit catalog with valid data
     *
     * @depends testCatalogEditPostWithNotEditableCatalog
     */
    public function testCatalogEditPostWithValidData() {
        $subcatalog = Catalog::where('name', 'LIKE', 'Catalog of course 1 -  group Test xy - chapter 1')->first();

        $post_data = array(
            'course' => $this->course->id,
            'id'     => $subcatalog->id,
            'name'   => 'Test yz',
            'number' => '1',
            'parent' => $this->catalog->id
        );
        $response = $this->post('catalog/edit', $post_data);

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('catalog/' . $subcatalog->id, $response);
        $this->checkIfNoErrorsExist();

        //Check the catalog
        $catalog = Catalog::where('name', 'LIKE', 'Test yz')->first();
        $this->assertNotNull($catalog);
        $this->assertEquals(2, $catalog->number);
        $this->assertEquals($this->catalog->id, $catalog->parent);
    }


    /**
     * Test delete catalog with not an existing course
     */
    public function testCatalogDeleteNotExistingID() {
        $id = $this->getNotExistingID('Catalog');
        $response = $this->get('catalog/' . $id . '/delete');
        $this->assertEquals('404', $response->foundation->getStatusCode());
    }


    /**
     * Test delete catalog with not an existing course
     *
     * @depends testCatalogDeleteNotExistingID
     */
    public function testCatalogDeleteWithNotEditableCatalog() {
        $response = $this->get('catalog/' . $this->catalog->id . '/delete');

        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('catalog/' . $this->catalog->id, $response);
        $this->checkIfErrorsExist();
    }


    /**
     * Test delete catalog with valid data
     *
     * @depends testCatalogDeleteWithNotEditableCatalog
     */
    public function testCatalogDeleteWithValidData() {
        $subcatalog = Catalog::where('name', 'LIKE', 'Catalog of course 1 -  group Test xy - chapter 1')->first();

        $response = $this->get('catalog/' . $subcatalog->id . '/delete');
        $this->assertEquals('302', $response->foundation->getStatusCode());
        $this->checkResponseLocation('course/' . $this->course->id, $response);

        $subcatalog = Catalog::where('name', 'LIKE', 'Catalog of course 1 -  group Test xy - chapter 1')->first();
        $this->assertNull($subcatalog);

        $parent = Catalog::where('name', 'LIKE', 'Course 1 of group Test xy')->first();
        $this->assertCount(4, $parent->questions()->get());
    }


    /**
     * Test the view to show a catalog was deleted
     */
    public function testCatalogDeletedView() {
        $response = $this->get('catalog/deleted');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }

}