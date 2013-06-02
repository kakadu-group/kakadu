<?php

class Flashcard extends Eloquent {

    public static $timestamps = TRUE;


    public function user() {
        return $this->belongs_to('User');
    }

    public function question() {
        return $this->belongs_to('Question');
    }

}