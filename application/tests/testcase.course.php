<?php

require_once 'testcase.user.php';

abstract class CourseTestCase extends UserTestCase
{

    protected $names_create = array();
    protected $names_delete = array();


    public function setUp() {
        parent::setUp();

        $user_sentry = Sentry::user('alex@example.com');
        $user_kakadu = User::find($user_sentry->get('id'));
        $role = Role::where('name', 'LIKE', 'admin')->first();
        
        foreach($this->names_create as $name) {
            $group = new Learngroup();
            $group->name = 'Group ' . $name;
            $group->description = 'This is the description of group ' . $name . '. It has to be very long.';
            $group->save();

            //Add admin
            $group->users()->attach($user_kakadu, array('role_id' => $role->id));

            for($i = 1; $i <= 2; $i++) {
                $catalog = new Catalog();
                $catalog->name = 'Course ' . $i . ' of group ' . $name;
                $catalog->number = 1;
                $catalog->save();

                $question1 = $this->createSimpleQuestion($i, $name, '1');
                $question2 = $this->createSimpleQuestion($i, $name, '2');

                $catalog->questions()->insert($question1);
                $catalog->questions()->insert($question2);

                //Chapter 1
                $catalog1 = new Catalog();
                $catalog1->name = 'Catalog of course ' . $i . ' -  group ' . $name . ' - chapter 1';
                $catalog1->number = 1;
                $catalog1->parent = $catalog->id;
                $catalog1->save();

                $question11 = $this->createSimpleQuestion($i, $name, '1.1');
                $question12 = $this->createSimpleQuestion($i, $name, '1.2');
                $question12->save();

                $catalog1->questions()->insert($question11);
                $catalog1->questions()->attach($question12);
                $catalog->questions()->attach($question12);

                //Chapter 2
                $catalog2 = new Catalog();
                $catalog2->name = 'Catalog of course ' . $i . ' -  group ' . $name . ' - chapter 2';
                $catalog2->number = 2;
                $catalog2->parent = $catalog->id;
                $catalog2->save();

                $question21 = $this->createMultipleQuestion($i, $name, '2.1');
                $question22 = $this->createMultipleQuestion($i, $name, '2.2');

                $catalog2->questions()->insert($question21);
                $catalog2->questions()->insert($question22);

                $catalog1->save();
                $catalog2->save();

                //Kurs 1
                $description = 'This is the description of course ' . $i . ' - group '
                                . $name . '. It has to be very long.';

                $course = new Course();
                $course->name = 'Course ' . $i . ' of group ' . $name;
                $course->description = $description;
                $course->catalog = $catalog->id;
                $course->save();

                $course->learngroups()->attach($group);
            }
        }
    }

    public function tearDown() {

        foreach($this->names_delete as $name) {
            $group = Learngroup::where('name', 'LIKE', 'Group ' . $name)->first();
            
            if($group === null) {
                continue;
            }

            //Delete all courses
            foreach($group->courses()->get() as $course) {
                //Delete all questions
                $catalog = $course->catalog()->first();
                Helper_Course::removeQuestionsOfSubCatalogs($catalog);
                
                //Delete course and catalog
                $course->delete();
                $catalog->delete();
            }

            //Delete group
            $group->delete();
        }

        parent::tearDown();
    }


    /**
     * Returns an id of a not exiting element in the database
     * @param  string  $type The type of the element (modelname)
     * @return integer       ID
     */
    protected function getNotExistingID($type) {
        for($i = 500;; $i++) {
            switch($type) {
                case 'Group':
                    $instance = Learngroup::find($i);
                    break;
                case 'Course':
                    $instance = Course::find($i);
                    break;
                case 'Catalog':
                    $instance = Catalog::find($i);
                    break;
                case 'Question':
                    $instance = Question::find($i);
                    break;
            }

            if($instance === null) {
                return $i;
            }
        }
    }


    /**
     * Checks if the catalog is saved as favorite
     *
     * @param  integer $catalogId The catalog id
     * @param  User    $user      The user instance
     * @return boolean
     */
    protected function isSavedAsFavorite($catalogId, $user = null) {
        if($user === null) {
            $userID = Sentry::user()->get('id');
            $user = User::find($userID);
        }

        foreach($user->favorites()->get() as $favorite) {
            if($favorite->id === $catalogId) {
                return true;
            }
        }

        return false;
    }


    /**
     * Create a simple question with example question and answer
     * 
     * @param  string  $courseNumber   A course number to show the referenced course
     * @param  string  $groupNumber    A name of a group to show the referenced groups
     * @param  string  $questionNumber A course number to show the referenced course
     * @return Question                A Question instance
     */
    private function createSimpleQuestion($courseNumber, $groupName, $questionNumber) {
        $q = array(
            'question' => 'This is question ' . $questionNumber . ' of course ' . $courseNumber . ' - group ' . $groupName
        );

        $a = array(
            'answer'    => 'This is answer ' . $questionNumber . ' of course ' . $courseNumber . ' - group ' . $groupName
        );

        $question = new Question();
        $question->type = 'simple';
        $question->question = json_encode($q);
        $question->answer = json_encode($a);
        return $question;
    }



    /**
     * Create a multiple choice question with example question and answer
     * 
     * @param  string  $courseNumber   A course number to show the referenced course
     * @param  string  $groupNumber    A name of a group to show the referenced groups
     * @param  string  $questionNumber A course number to show the referenced course
     * @return Question                A Question instance
     */
    private function createMultipleQuestion($courseNumber, $groupName, $questionNumber) {
        $q = array(
            'question' => 'This is question ' . $questionNumber . ' of course ' . $courseNumber . ' - group ' . $groupName
        );

        $a = array(
            'answer'    => array(
                '2',
                '3'
            ),
            'choices'   => array(
                'This is answer 1',
                'This is answer 2 - The right answer',
                'This is answer 3 - The right answer',
                'This is answer 4'
            )
        );

        $question = new Question();
        $question->type = 'multiple';
        $question->question = json_encode($q);
        $question->answer = json_encode($a);
        return $question;
    }

}
