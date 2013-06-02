<?php

class Multiple extends QuestionType {

    protected $type = 'multiple';
    protected $choices = array();


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
     * Returns the choices of the question
     * 
     * @return array The choices of the questions
     */
    public function getChoices() {
        return $this->choices;
    }




    /**
     * Reads the information of the question form the input or return an error message
     * 
     * @return boolean|string True if no error occured or false if there was an error
     */
    public function getQuestionFromInput() {
        if(parent::getQuestionFromInput() === false) {
            return false;
        }

        $this->choices = Input::get('choices');


        if(!is_array($this->choices) || count($this->choices) < 2) {
            return __('question.multiple_min_two_answers');
        }

        if(!is_array($this->answer) || count($this->answer) < 1) {
            return __('question.multiple_index_not_valid');
        }

        $numberOfAnswers = count($this->choices);
        foreach($this->answer as $index) {
            if($index < 0 || $index >= $numberOfAnswers) {
                return __('question.multiple_index_not_valid');
            }
        }

        return true;
    }

    /**
     * Reads the information of the question form the import data or return an error message
     *
     * @param array    $data An array with all informations
     * @return boolean True if no error occured or false if there was an error
     */
    public function getQuestionFromImportData($data) {
        if(parent::getQuestionFromImportData($data) === false) {
            return false;
        }

        $this->choices = $data['choices'];

        return true;
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

        foreach($jsonAnswer->{'answer'} as $answer) {
            $this->answer[] = $answer;
        }

        foreach($jsonAnswer->{'choices'} as $choice) {
            $this->choices[] = $choice;
        }
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
            'answer'    => $this->answer,
            'choices'   => $this->choices
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
        $element['choices'] = $this->choices;

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
        $answer = preg_split('/[ ]*,[ ]*/', $cell->getValue());

        if(count($answer) <= 0) {
            return false;
        }

        //Question - Choices
        $choices = array();
        $cellIterator->next();

        while($cellIterator->valid()) {
            $cell = $cellIterator->current();
            $choices[] = $cell->getValue();
            $cellIterator->next();
        }

        $numberOfChoices = count($choices);

        if($numberOfChoices < 2) {
            return false;
        }

        foreach($answer as $a) {
            if($a < 0 || $a >= $numberOfChoices) {
                return false;
            }
        }

        return array(
            'question'  => $question,
            'answer'    => $answer,
            'choices'   => $choices
        );
    }

}