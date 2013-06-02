<?php

class Question extends Eloquent {

    public static $timestamps = TRUE;
    

    public function catalogs() {
        return $this->has_many_and_belongs_to('Catalog', 'catalog_questions');
    }

    public function flashcards() {
        return $this->has_many('Falshcard');
    }

}