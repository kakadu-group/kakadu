<?php

class Simple extends QuestionType {

    protected $type = 'simple';


    /**
     * Returns the text of the question
     * 
     * @return string The text of the question
     */
    public function getQuestion() {
        return $this->question;
    }

    /**
     * Returns the answer of the question
     * 
     * @return string The answer of the question
     */
    public function getAnswer() {
        return $this->answer;
    }




    /**
     * Reads the information of the question form the input or return an error message
     * 
     * @return boolean|string True if no error occured or false if there was an error
     */
    public function getQuestionFromInput() {
        return parent::getQuestionFromInput();
    }

    /**
     * Reads the information of the question form the import data or return an error message
     *
     * @param array    $data An array with all informations
     * @return boolean True if no error occured or false if there was an error
     */
    public function getQuestionFromImportData($data) {
        return parent::getQuestionFromImportData($data);
    }

    /**
     * Sets the question informations from a Question instance
     * 
     * @param Question $question A Question instance
     */
    public function setInfosFromQuestion($question) {
        parent::setInfosFromQuestion($question);

        $jsonQuestion = json_decode($question->question);
        $jsonAnswer = json_decode($question->answer);
        
        $this->question = $jsonQuestion->{'question'};
        $this->answer = $jsonAnswer->{'answer'};
    }

    /**
     * Converts the question informations to JSON.
     * The result can be stored in the database.
     * 
     * @return string The JSON response of the question.
     */
    protected function getJsonQuestion() {
        $jsonQuestion = array(
            'question' => $this->question
        );

        return json_encode($jsonQuestion);
    }

    /**
     * Converts the answer informations to JSON.
     * The result can be stored in the database.
     * 
     * @return string The JSON response of the answer.
     */
    protected function getJsonAnswer() {
        $jsonAnswer = array(
            'answer' => $this->answer
        );

        return json_encode($jsonAnswer);
    }

    /**
     * Returns the question in a valid format for the view
     * 
     * @return array An array with all the informations of the question
     */
    public function getViewElement() {
        $element = parent::getViewElement();

        $element['question'] = $this->question;
        $element['answer'] = $this->answer;

        return $element;
    }


    /**
     * Read all question informations form a cell iterator and return an array with the given data
     * 
     * @param  PHPExcel_Worksheet_CellIterator $cellIterator
     * @return array                           An array with all specific informations or false on a syntax error
     */
    public static function readCSVData($cellIterator) {
        //Question - Question
        $cellIterator->next();

        if(!$cellIterator->valid()) {
            return false;
        }

        $cell = $cellIterator->current();
        $question = $cell->getValue();

        //Question - Answer
        $cellIterator->next();

        if(!$cellIterator->valid()) {
            return false;
        }

        $cell = $cellIterator->current();
        $answer = $cell->getValue();

        return array(
            'question'  => $question,
            'answer'    => $answer
        );
    }

}