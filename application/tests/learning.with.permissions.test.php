<?php

require_once 'testcase.course.php';

class TestLearningWithPermissions extends CourseTestCase {

    protected $names_create = array('Test xy');
    protected $names_delete = array('Test xy');

    private $course = null;


    public function setUp() {
        parent::setUp();
        $this->course = Course::where('name', 'LIKE', 'Course 1 of group Test xy')->first();

        //Login in
        try {
            Sentry::login('alex@example.com', 'password');
        } catch (Sentry\SentryException $e) {
            printf($e->getMessage());
        }

        //Add course as favorite
        $userID = Sentry::user()->get('id');
        $user = User::find($userID);
        $catalog = $this->course->catalog()->first();
        $user->favorites()->attach($catalog);

        //Generate flashcard
        $question = Question::where('question', 'LIKE', '%This is question 1.1 of course 1 - group Test xy%')
                            ->first();

        $flashcard = new Flashcard;
        $flashcard->question_id = $question->id;
        $flashcard->user_id = Sentry::user()->get('id');
        $flashcard->index = 3;
        $flashcard->number_correct = 4;
        $flashcard->number_incorrect = 1;
        $flashcard->save();
    }


    /**
     * Test the view to learn a course
     */
    public function testCoursesView() {
        $response = $this->get('course/' . $this->course->id . '/learning');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }

    /**
     * Test the view to learn a catalog
     */
    public function testCatalogView() {
        $response = $this->get('catalog/' . $this->course->catalog()->first()->id . '/learning');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }

    /**
     * Test the view to learn favorites
     */
    public function testFavoriteView() {
        $response = $this->get('favorites/learning');
        $this->assertEquals('200', $response->foundation->getStatusCode());
    }

    /**
     * Test ajax get next question with not set answer
     */
    public function testAjaxNextQuestionWithNoAnswer() {
        $catalog = $this->course->catalog()->first();
        $question = $catalog->questions()->first();

        $post_data = array(
            'question'  => $question->id,
            'course'    => $this->course->id,
            'section'   => 'course'
        );
        $response = $this->ajax_post('learning/next', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax get next question with not valid data
     *
     * @depends testAjaxNextQuestionWithNoAnswer
     */
    public function testAjaxNextQuestionWithNotValidData() {
        $id = $this->getNotExistingID('Question');
        $catalog = $this->course->catalog()->first();

        $post_data = array(
            'question'  => $id,
            'course'    => $this->course->id,
            'answer'    => 'true',
            'section'   => 'course'
        );
        $response = $this->ajax_post('learning/next', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
        $this->assertContains('errors', $content);
    }


    /**
     * Test ajax get next question with valid data and right answer
     *
     * @depends testAjaxNextQuestionWithNotValidData
     */
    public function testAjaxNextQuestionWithValidDataAndRightAnswer() {
        $catalog = $this->course->catalog()->first();
        $question = $catalog->questions()->first();

        $post_data = array(
            'question'  => $question->id,
            'course'    => $this->course->id,
            'answer'    => 'true',
            'section'   => 'course'
        );
        $response = $this->ajax_post('learning/next', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Ok"', $content);

        //Check flashcard
        $flashcard = Flashcard::where('question_id', '=', $question->id)
                                ->where('user_id', '=', Sentry::user()->get('id'))
                                ->first();

        $this->assertEquals(1, $flashcard->index);
        $this->assertEquals(1, $flashcard->number_correct);
        $this->assertEquals(0, $flashcard->number_incorrect);
    }


    /**
     * Test ajax get next question with valid data and wrong answer
     *
     * @depends testAjaxNextQuestionWithValidDataAndRightAnswer
     */
    public function testAjaxNextQuestionWithValidDataAndWrongAnswer() {
        $catalog = $this->course->catalog()->first();
        $question = $catalog->questions()->first();

        $post_data = array(
            'question'  => $question->id,
            'course'    => $this->course->id,
            'answer'    => 'false',
            'section'   => 'course'
        );
        $response = $this->ajax_post('learning/next', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Ok"', $content);

        //Check flashcard
        $flashcard = Flashcard::where('question_id', '=', $question->id)
                                ->where('user_id', '=', Sentry::user()->get('id'))
                                ->first();

        $this->assertEquals(0, $flashcard->index);
        $this->assertEquals(0, $flashcard->number_correct);
        $this->assertEquals(1, $flashcard->number_incorrect);
    }


    /**
     * Test ajax get next question with valid data, existing flashcard and right answer
     *
     * @depends testAjaxNextQuestionWithValidDataAndWrongAnswer
     */
    public function testAjaxNextQuestionWithValidDataExistingFlashcardAndRightAnswer() {
        $question = Question::where('question', 'LIKE', '%This is question 1.1 of course 1 - group Test xy%')
                            ->first();
        $catalog = $this->course->catalog()->first();

        $post_data = array(
            'question'  => $question->id,
            'course'    => $this->course->id,
            'answer'    => 'true',
            'section'   => 'course'
        );
        $response = $this->ajax_post('learning/next', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Ok"', $content);

        //Check flashcard
        $flashcard = Flashcard::where('question_id', '=', $question->id)
                                ->where('user_id', '=', Sentry::user()->get('id'))
                                ->first();

        $this->assertEquals(4, $flashcard->index);
        $this->assertEquals(5, $flashcard->number_correct);
        $this->assertEquals(1, $flashcard->number_incorrect);
    }


    /**
     * Test ajax get next question with valid data, existing flashcard and wrong answer
     *
     * @depends testAjaxNextQuestionWithValidDataExistingFlashcardAndRightAnswer
     */
    public function testAjaxNextQuestionWithValidDataExistingFlashcardAndWrongAnswer() {
        $question = Question::where('question', 'LIKE', '%This is question 1.1 of course 1 - group Test xy%')
                            ->first();
        $catalog = $this->course->catalog()->first();

        $post_data = array(
            'question'  => $question->id,
            'course'    => $this->course->id,
            'answer'    => 'false',
            'section'   => 'course'
        );
        $response = $this->ajax_post('learning/next', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Ok"', $content);

        //Check flashcard
        $flashcard = Flashcard::where('question_id', '=', $question->id)
                                ->where('user_id', '=', Sentry::user()->get('id'))
                                ->first();

        $this->assertEquals(0, $flashcard->index);
        $this->assertEquals(4, $flashcard->number_correct);
        $this->assertEquals(2, $flashcard->number_incorrect);
    }


    /**
     * Test ajax get next question of catalog with valid data and right answer
     *
     * @depends testAjaxNextQuestionWithValidDataExistingFlashcardAndWrongAnswer
     */
    public function testAjaxNextQuestionOfCatalogWithValidDataAndRightAnswer() {
        $catalog = $this->course->catalog()->first();
        $question = $catalog->questions()->first();

        $post_data = array(
            'question'  => $question->id,
            'catalog'   => $catalog->id,
            'answer'    => 'true',
            'section'   => 'catalog'
        );
        $response = $this->ajax_post('learning/next', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Ok"', $content);

        //Check flashcard
        $flashcard = Flashcard::where('question_id', '=', $question->id)
                                ->where('user_id', '=', Sentry::user()->get('id'))
                                ->first();

        $this->assertEquals(1, $flashcard->index);
        $this->assertEquals(1, $flashcard->number_correct);
        $this->assertEquals(0, $flashcard->number_incorrect);
    }


    /**
     * Test ajax get next question of favorites with valid data, right answer and favorites
     *
     * @depends testAjaxNextQuestionOfCatalogWithValidDataAndRightAnswer
     */
    public function testAjaxNextQuestionOfFavoritesWithValidDataAndRightAnswerAndNoFavorites() {
        $catalog = $this->course->catalog()->first();
        $question = $catalog->questions()->first();

        $userID = Sentry::user()->get('id');
        $user = User::find($userID);
        $user->favorites()->detach($catalog->id);        

        $post_data = array(
            'question'  => $question->id,
            'answer'    => 'true',
            'section'   => 'favorites'
        );
        $response = $this->ajax_post('learning/next', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Error"', $content);
    }


    /**
     * Test ajax get next question of favorites with valid data, right answer and favorites
     *
     * @depends testAjaxNextQuestionOfFavoritesWithValidDataAndRightAnswerAndNoFavorites
     */
    public function testAjaxNextQuestionOfFavoritesWithValidDataAndRightAnswerAndFavorites() {
        $catalog = $this->course->catalog()->first();
        $question = $catalog->questions()->first();

        $post_data = array(
            'question'  => $question->id,
            'answer'    => 'true',
            'section'   => 'favorites'
        );
        $response = $this->ajax_post('learning/next', $post_data);
        $this->assertEquals('200', $response->foundation->getStatusCode());
        $content = $response->content;
        $this->assertContains('"status":"Ok"', $content);

        //Check flashcard
        $flashcard = Flashcard::where('question_id', '=', $question->id)
                                ->where('user_id', '=', Sentry::user()->get('id'))
                                ->first();

        $this->assertEquals(1, $flashcard->index);
        $this->assertEquals(1, $flashcard->number_correct);
        $this->assertEquals(0, $flashcard->number_incorrect);
    }

}