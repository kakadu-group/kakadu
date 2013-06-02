<?php

class Helper_Group {


    /**
     * Gets all learngrous of a given user
     * @param  User $user A instance of a Sentry user
     * @return Array      The learngroups, where the user is a member
     */
    public static function getLearngroupsOfUser($user) {
        $userId = $user->get('id');

        //Get kakadu user
        $userKakadu = User::find($userId);

        //Get learngroups
        $groups = array();

        foreach($userKakadu->learngroups()->order_by('name')->get() as $g) {
            $groups[] = array(
                'id'            => $g->id,
                'name'          => $g->name,
                'description'   => $g->description
            );
        }

        return $groups;
    }


    /**
     * Deletes a learngroup.
     * It deletes all related courses if the learngroup is the last group of the course
     * 
     * @param  Learngroup $group A learngroup instance
     */
    public static function deleteGroupAndCheckRelatedCourses($group) {
        //Delete all courses
        foreach($group->courses()->get() as $course) {
            //Check if just one group is referenced to the course
            if($course->learngroups()->count() > 1) {
                $newGroups = $course->learngroups()->where('learngroups.id', '<>', $group->id)->get();
                $oldGroups = array($group);

                static::deleteFavoritesOfLearngroupMembers($course, $newGroups, $oldGroups);
                continue;
            }

            //Delete all questions
            $catalog = $course->catalog()->first();
            Helper_Course::removeQuestionsOfSubCatalogs($catalog);
            
            //Delete course and catalog
            $course->delete();
            $catalog->delete();
        }

        //Delete group
        $group->delete();
    }


    /**
     * Deletes all favorites of a user, that is removed from a learngroup.
     * The favorites are not deleted, if the user is a member of a other related learngroup.
     * 
     * @param  Learngroup $group A Learngroup instance
     * @param  User       $user  A User instance
     */
    public static function deleteFavoritesOfLearngroupMember($group, $user) {

        foreach($group->courses()->get() as $course) {
            //Check if user is member of other learngroup
            $check = false;
            foreach($course->learngroups()->where('learngroups.id', '<>', $group->id)->get() as $other) {
                if($other->users()->where('user_id', '=', $user->id)->count() > 0) {
                    $check = true;
                    break;
                }
            }

            //Delete favorites
            if($check === false) {
                $catalog = $course->catalog()->first();
                $catalogIDs = Helper_Course::getSubCatalogIDsOfCatalog($catalog);
                $user->favorites()->pivot()->where_in('catalog_id', $catalogIDs)->delete();
            }
        }

    }


    /**
     * Deletes all favorites of the users, that are removed with a specific learngroup.
     * The favorites are not deleted, if the user is a member of a other related learngroup.
     * 
     * @param  Course $course     A Course instance
     * @param  array  $newGroups  An array with the new Learngroup instances
     * @param  array  $oldGroups  An array with the old Learngroup instances
     */
    public static function deleteFavoritesOfLearngroupMembers($course, $newGroups, $oldGroups) {
        //Get course catalogs
        $catalog = $course->catalog()->first();
        $catalogIDs = Helper_Course::getSubCatalogIDsOfCatalog($catalog);

        //Get all user IDs of the new groups
        $newGroupsUserIDs = array();

        foreach($newGroups as $newGroup) {
            $tmp = array();

            foreach($newGroup->users()->get() as $user) {
                $tmp[] = $user->id;
            }

            $newGroupsUserIDs = array_merge($newGroupsUserIDs, $tmp);
        }

        $newGroupsUserIDs = array_unique($newGroupsUserIDs);

        //Get all user IDs of the old groups
        $oldGroupsUserIDs = array();

        foreach($oldGroups as $oldGroup) {
            $tmp = array();

            foreach($oldGroup->users()->get() as $user) {
                $tmp[] = $user->id;
            }

            $oldGroupsUserIDs = array_merge($oldGroupsUserIDs, $tmp);
        }

        $oldGroupsUserIDs = array_unique($oldGroupsUserIDs);

        //Check if user is in new learngroups
        foreach($oldGroupsUserIDs as $oldUserID) {
            if(!in_array($oldUserID, $newGroupsUserIDs)) {
                //Delete favorites of the user
                $user = User::find($oldUserID);

                if($user === null) {
                    continue;
                }

                $user->favorites()->where_in('catalog_id', $catalogIDs)->delete();
            }
        }
    }

}