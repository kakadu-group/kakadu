<?php

abstract class QuestionType {

    protected $id = null;
    protected $type = null;
    protected $question = null;
    protected $answer = null;
    protected $created_at = null;
    protected $updated_at = null;
    protected $catalogs = array();


    /**
     * Returns the ID of the question
     * 
     * @return integer The ID
     */
    public function getID() {
        return $this->id;
    }

    /**
     * Returns the type of the question
     * 
     * @return string The type
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Returns the text of the question
     * 
     * @return string The text of the question
     */
    abstract protected function getQuestion();

    /**
     * Returns the answer of the question
     * 
     * @return string The answer of the question
     */
    abstract protected function getAnswer();

    /**
     * Returns the time when the question was created
     * 
     * @return DateTime The time when the question was created
     */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
     * Returns the time when the question was updated
     * 
     * @return DateTime The time when the question was updated
     */
    public function getUpdatedAt() {
        return $this->updated_at;
    }



    /**
     * Save the question to the database
     * If the question is allready in the database, then the question will be updated
     * If the question is not in the database, then the question will be created
     * 
     * @return Question The saved Question instance or null if an error occurs
     */
    public function save() {
        //Create or get the element
        if($this->id == null) {
            $element = new Question;
            $element->type = $this->type;
        } else {
            $element = Question::find($this->id);

            if($element == null) {
                return null;
            }
        }

        //Set informations
        $element->question = $this->getJsonQuestion();
        $element->answer = $this->getJsonAnswer();
        $element->save();

        return $element;
    }

    /**
     * Reads the information of the question form the input or return an error message
     * 
     * @return boolean|string True if no error occured or false if there was an error
     */
    protected function getQuestionFromInput() {
        if(Input::has('id')) {
            $this->id = Input::get('id');
        }

        $this->question = Input::get('question');
        $this->answer = Input::get('answer');

        return true;
    }

    /**
     * Reads the information of the question form the import data or return an error message
     *
     * @param array    $data An array with all informations
     * @return boolean True if no error occured or false if there was an error
     */
    protected function getQuestionFromImportData($data) {
        $this->question = $data['question'];
        $this->answer = $data['answer'];

        return true;
    }

    /**
     * Sets the question informations from a Question instance
     * 
     * @param Question $question A Question instance
     */
    protected function setInfosFromQuestion($question) {
        $this->id = $question->id;
        $this->created_at = $question->created_at;
        $this->updated_at = $question->updated_at;
    }

    /**
     * Converts the question informations to JSON
     * The result can be stored in the database
     * 
     * @return string The JSON response of the question
     */
    abstract protected function getJsonQuestion();

    /**
     * Converts the answer informations to JSON
     * The result can be stored in the database
     * 
     * @return string The JSON response of the answer
     */
    abstract protected function getJsonAnswer();

    /**
     * Returns the question in a valid format for the view
     * 
     * @return array An array with all the informations of the question
     */
    protected function getViewElement() {
        $element = array(
            'id'            => $this->id,
            'type'          => $this->type,
            'created_at'     => $this->created_at,
            'updated_at'    => $this->updated_at
        );

        return $element;
    }




    /**
     * Gets a QuestionType instance with all set question informations from a Question instance
     * 
     * @param Question $question A Question instance
     */
    public static function getQuestionFromQuestion($question) {
        if($question === null) {
            return null;
        }

        $questionType = static::getQuestionType($question->type);

        if($questionType === null) {
            return null;
        }

        $questionType->setInfosFromQuestion($question);
        return $questionType;
    }

    /**
     * Reads the informations of the question from the database and return a valid instance
     * 
     * @param  integer      $id The ID of the question
     * @return QuestionType     A QuestionType instance or null if an error occured
     */
    public static function getQuestionFromDatabase($id) {
        $question = Question::find($id);

        if($question === null) {
            return null;
        }

        $questionType = static::getQuestionType($question->type);

        if($questionType === null) {
            return null;
        }

        $questionType->setInfosFromQuestion($question);
        return $questionType;
    }

    /**
     * Get the right QuestionType instance
     * 
     * @param  string $type The name of the QuestionType
     * @return QuestionType The QuestionType instance
     */
    public static function getQuestionType($type) {
        switch($type) {
            case 'simple':
                return new Simple;

            case 'multiple':
                return new Multiple;

            default:
                return null;
        }
    }

}