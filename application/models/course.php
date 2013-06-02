<?php

class Course extends Eloquent {

    public static $timestamps = TRUE;


    public function catalog() {
        return $this->belongs_to('Catalog', 'catalog');
    }

    public function learngroups() {
        return $this->has_many_and_belongs_to('Learngroup', 'learngroup_courses');
    }

}