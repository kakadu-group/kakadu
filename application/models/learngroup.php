<?php

class Learngroup extends Eloquent {

    public static $timestamps = TRUE;


    public function courses() {
        return $this->has_many_and_belongs_to('Course', 'learngroup_courses');
    }

    public function users() {
        return $this->has_many_and_belongs_to('User', 'user_learngroups')->with('role_id');
    }

}