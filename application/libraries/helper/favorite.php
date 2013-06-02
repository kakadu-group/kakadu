<?php

class Helper_Favorite {


    /**
     * Returns an array with all favorites of a given user
     * 
     * @param  User $userSentry An instance of a Sentry user
     * @return array            An array with the favorite courses and catalogs of the user
     */
    public static function getFavorites($userSentry) {
        //Get the user
        $userID = $userSentry->get('id');
        $user = User::find($userID);

        //Get all favorites
        $favorites = $user->favorites()->get();

        //Sort the favorites
        $courses = array();
        $catalogs = array();

        foreach($favorites as $favorite) {
            if($favorite->parent === null) {
                //Course
                $course = $favorite->course()->first();
                $courses[] = array(
                        'id'            => $course->id,
                        'name'          => $course->name,
                        'description'   => $course->description,
                        'created_at'    => $course->created_at,
                        'updated_at'    => $course->updated_at
                    );
            } else {
                $course = Helper_Course::getCourseOfCatalog($favorite);

                //Catalog
                $catalogs[] = array(
                        'id'            => $favorite->id,
                        'name'          => $favorite->name,
                        'number'        => $favorite->number,
                        'created_at'    => $favorite->created_at,
                        'updated_at'    => $favorite->updated_at,

                        'course'    => array(
                                'id'            => $course->id,
                                'name'          => $course->name,
                                'description'   => $course->description,
                                'created_at'    => $course->created_at,
                                'updated_at'    => $course->updated_at
                        )
                    );
            }
        }


        $result = array(
                'courses'   => $courses,
                'catalogs'  => $catalogs
            );


        return $result;
    }


    /**
     * Checks if a catalog is a favorite of a given user
     * 
     * @param  Catalog $catalog     A catalog instance
     * @param  User    $userSentry  The Sentry user
     * @return boolean
     */
    public static function isCatalogFavoriteOfUser($catalog, $userSentry) {
        $user_id = $userSentry->get('id');
        $user = User::find($user_id);

        $favorites = $user->favorites()->where('catalog_id', '=', $catalog->id)->get();

        if(count($favorites) > 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Checks if a parent catalog is a favorite of a given user
     * 
     * @param  Catalog $catalog     A catalog instance
     * @param  User    $userSentry  The Sentry user
     * @return boolean
     */
    public static function isParentCatalogFavoriteOfUser($catalog, $userSentry) {
        $user_id = $userSentry->get('id');
        $user = User::find($user_id);

        while(($parent = $catalog->parent()->first()) !== null) {

            $favorites = $user->favorites()->where('catalog_id', '=', $parent->id)->get();

            if(count($favorites) > 0) {
                return true;
            }

            $catalog = $parent;
        }

        return false;
    }

}