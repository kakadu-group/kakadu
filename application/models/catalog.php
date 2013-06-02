<?php

class Catalog extends Eloquent {

    public static $timestamps = TRUE;


    public function parent() {
        return $this->belongs_to('Catalog', 'parent');
    }

    public function children() {
        return $this->has_many('Catalog', 'parent');
    }

    public function course() {
        return $this->has_one('Course', 'catalog');
    }

    public function questions() {
        return $this->has_many_and_belongs_to('Question', 'catalog_questions');
    }

    public function favorite() {
        return $this->has_many_and_belongs_to('User', 'favorites');
    }

}