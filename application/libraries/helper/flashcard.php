<?php

class Helper_Flashcard {


    /**
     * Get the next question of the given catalogs
     * @param User   $user     Sentry user object
     * @param Course $catalogs An arry with all selected catalogs catalogs
     * @return Question
     */
    public static function getNextQuestion($user, $catalogs) {

        if(count($catalogs) <= 0) {
            return false;
        }

        //Get question and flashcard informations
        $infos = static::getQuestionsAndFlashcardsOfCatalogs($user, $catalogs);

        //Get basic variables
        $max_index = 0;
        $block = 0;
        $indices = array();

        //Generate max_index and indices
        foreach($infos as $i) {
            $index = $i->index;

            if($max_index < $index) {
                $max_index = $index;
            }

            if(array_key_exists($index, $indices)) {
                $indices[$index]++;
            } else {
                $indices[$index] = 1;
            }
        }

        //Generate block
        foreach($indices as $key => $value) {
            $unit = (($max_index - $key) + 1) * 2;
            $block += $unit * $value;
        }

        //Generate random number
        if($block === 0) {
            return false;
        } else {
            $random = rand(0, $block - 1);
        }

        //Get selected question
        $block_max = 0;

        foreach($indices as $key => $value) {
            $unit = (($max_index - $key) + 1) * 2;
            $block_max += $unit * $value;

            if($random < $block_max) {
                $block_min = $block_max - ($unit * $value);
                $block_value = $random - $block_min;
                $number_of_question = (int)($block_value / $unit);
                $number_of_key = $key;
                break;
            }
        }

        $number = 0;
        foreach ($indices as $key => $value) {
            if($key === $number_of_key) {
                $number += $number_of_question;
                break;
            }

            $number += $value;
        }

        //Get question
        $question_id = $infos[$number]->question_id;
        $catalog_id = $infos[$number]->catalog_id;

        $result = array(
                    'question'  => Question::find($question_id),
                    'catalog'   => Catalog::find($catalog_id)
                  );

        return $result;
    }


    /**
     * Get all question and flashcard informations of given catalogs
     * @param User  $user     Sentry User object
     * @param array $catalogs An arry with all selected catalogs catalogs
     * @return array          An array with all question and flashcard informations
     */
    private static function getQuestionsAndFlashcardsOfCatalogs($user, $catalogs) {

        //Check if catalogs a given
        if(count($catalogs) <= 0) {
            return array();
        }

        //Get user id
        $user_id = $user->get('id');


        //Get all questions and flashcards
        $query = static::getQuery($user_id, $catalogs);
        $result = $query->get(array(
                                 'questions.id as question_id',
                                 'questions.question as question',
                                 'questions.answer as answer',
                                 'catalogs.id as catalog_id',
                                 'flashcards.index as index',
                                 'flashcards.offset as offset',
                                 'flashcards.number_correct as number_correct',
                                 'flashcards.number_incorrect as number_incorrect',
                                 'flashcards.user_id as user_id',
                              ));
        

        //Fill the null values
        foreach($result as $row) {
            if(is_null($row->user_id)) {
                $row->index = 0;
                $row->offset = 0;
                $row->number_correct = 0;
                $row->number_incorrect = 0;
                $row->user_id = $user_id;
            }
        }


        //Check if there are no questions
        $numberOfQuestions = count($result);
        $offset = 5;

        if($numberOfQuestions <= 0) {
            return $result;
        } else if($numberOfQuestions > 50) {
            $offset = 0;
        } else if($numberOfQuestions > 30) {
            $offset = 1;
        } else if($numberOfQuestions > 10) {
            $offset = 3;
        }         


        //Select the question and flashcards with the specific offset
        do {
            $questions = static::selectQuestions($result, $offset);
            $offset += 2;
        } while(count($questions) <= 0);

        return $questions;
    }


    /**
     * Save the flashcard and set the card index.
     *
     * @param User     $user     Sentry user object
     * @param Question $question The question object
     * @param boolean  $answer   The answer of the question
     * @param array $catalogs An arry with all selected catalogs catalogs
     */
    public static function saveFlashcard($user, $question, $answer, $catalogs) {

        //Get user id
        $user_id = $user->get('id');


        //Decrement offset of flashcards
        $query = static::getQuery($user_id, $catalogs);
        $result = $query->where('flashcards.offset', '>', 0)
                        ->get('flashcards.id as id');

        if(count($result) > 0) {
            $flashcardIDs = array();

            foreach($result as $row) {
                $flashcardIDs[] = $row->id;
            }

            DB::table('flashcards')
                ->where_in('flashcards.id', $flashcardIDs)
                ->decrement('flashcards.offset');
        }

        //Get answer flashcard
        $flashcard = Flashcard::where('question_id', '=', $question->id)
                            ->where('user_id', '=', $user_id)
                            ->first();


        //Create not existing flashcard
        if($flashcard === null) {
            $flashcard = new Flashcard;
            $flashcard->question_id = $question->id;
            $flashcard->user_id = $user_id;
            $flashcard->index = 0;
            $flashcard->offset = 0;
            $flashcard->number_correct = 0;
            $flashcard->number_incorrect = 0;
            $flashcard->save();
        }


        //Save the answer and update index
        if($answer === 'true') {
            $flashcard->number_correct = $flashcard->number_correct + 1;
            $flashcard->index = $flashcard->index + 1;
            $flashcard->offset = rand(10, 20);
        } else {
            $flashcard->number_incorrect = $flashcard->number_incorrect + 1;

            if($flashcard->index > 3) {
                $flashcard->index = $flashcard->index - 3;
            } else {
                $flashcard->index = 0;
            }

            $flashcard->offset = rand(5, 10);
        }
        
        $flashcard->save();
    }


    /**
     * Get the query to get the flashcards and the question informations
     * @param  integer $user_id  User id
     * @param  Array   $catalogs An array with all the IDs of the catalogs where to search
     * @return Array             Query
     */
    private static function getQuery($user_id, $catalogs) {
        $query = DB::table('questions')
              ->where_in('catalogs.id', $catalogs)

              ->join('catalog_questions', 'catalog_questions.question_id', '=', 'questions.id')
              ->join('catalogs', 'catalog_questions.catalog_id', '=', 'catalogs.id')
              ->left_join('flashcards', function($join) use ($user_id) {
                              $join->on('flashcards.question_id', '=', 'questions.id');
                              $join->on('flashcards.user_id', '=', DB::raw($user_id));
                          })

              ->order_by('flashcards.index', 'asc')

              ->group_by('questions.id');

        return $query;
    }


    /**
     * Get all questions with a offset smaller equals the given offset
     * @param  Array   $questions An array of question instances
     * @param  integer $offset    The given offset
     * @return Array              An array with all questions that have a offset smaller equals the given offset
     */
    private static function selectQuestions($questions, $offset) {
        $selected = array();

        foreach($questions as $question) {
            if($question->offset <= $offset) {
                $selected[] = $question;
            }
        }

        return $selected;
    }
}