<?php

class User_Metadata extends Eloquent {

    public static $table = 'user_metadata';


    public function user() {
        return $this->belongs_to('User', 'user_id');
    }

}