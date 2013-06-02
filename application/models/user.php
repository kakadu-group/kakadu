<?php

class User extends Eloquent {

    public static $hidden = array(
                                'password',
                                'password_reset_hash',
                                'temp_password',
                                'remember_me',
                                'activation_hash',
                                'ip_address',
                                'status',
                                'activated',
                                'permissions',
                            );


    public function metadata() {
        return $this->has_one('User_metadata', 'id');
    }

    public function learngroups() {
        return $this->has_many_and_belongs_to('Learngroup', 'user_learngroups')->with('role_id');
    }

    public function favorites() {
        return $this->has_many_and_belongs_to('Catalog', 'favorites');
    }

    public function flashcards() {
        return $this->has_many('Flashcard');
    }

}