<?php

class Helper_Course {


    /**
     * Gets the course of a catalog
     * 
     * @param  Catalog $catalog The Catalog object, who's course is searched
     * @return Course           The Course object
     */
    public static function getCourseOfCatalog($catalog) {
        while(($tmp = $catalog->parent()->first()) !== null) {
            $catalog = $tmp;
        }

        return $catalog->course()->first();
    }


    /**
     * Gets the course of the question
     * @param  Model $question Question
     * @return Model           Course
     */
    public static function getCourseOfQuestion($question) {
        $catalog = $question->catalogs()->first();

        if($catalog === null) {
            return null;
        }

        return static::getCourseOfCatalog($catalog);
    }


    /**
     * Checks if a catalog is a subcatalog of a course
     * 
     * @param  Catalog $catalog A Catalog object that has to be checked
     * @param  Course  $course  A Course object
     * @return boolean          Result
     */
    public static function isCatalogPartOfCourse($catalog, $course) {
        while(($tmp = $catalog->parent()->first()) !== null) {
            $catalog = $tmp;
        }

        $c = $catalog->course()->first();

        if($course->id !== $c->id) {
            return false;
        }

        return true;
    }


    /**
     * Checks if the given catalogs are part of the given course
     * @param  Course $course     Course object
     * @param  array  $catalogIDs Catalog id's
     * @return int    1 = true, 0 = false, -1 = error
     */
    public static function areCatalogsPartOfCourse($course, $catalogIDs) {
        foreach($catalogIDs as $c) {
            $catalog = Catalog::find($c);

            if($catalog === null) {
                return -1;
            }

            while(($tmp = $catalog->parent()->first()) !== null) {
                $catalog = $tmp;
            }

            $course = $catalog->course()->first();
            
            if($course->id !== $course->id) {
                return 0;
            }
        }

        return 1;
    }


    /**
     * Checks if a question is part of a catalog or his subcatalogs
     * 
     * @param  Question $question A Question instance that has to be checked
     * @param  Catalog  $catalog  A Catalog instance
     * @return boolean            Result
     */
    public static function isQuestionPartOfCatalog($question, $catalog) {
        $catalogs = $question->catalogs()->get();
        $ids = static::getSubCatalogIDsOfCatalog($catalog);

        foreach($ids as $id) {
            foreach($catalogs as $c) {
                if($c->id === $id)
                    return true;
            }
        }

        return false;
    }


    /**
     * Gets all ids of the subcatalogs of a given catalog
     * @return Array An array with all subcatalog ids
     */
    public static function getSubCatalogIDsOfCatalog($catalog) {
        $catalogs = array();

        $catalogs[] = $catalog->id;

        foreach($catalog->children()->order_by('number', 'asc')->get() as $subCatalog) {
            $catalogs = array_merge($catalogs, static::getSubCatalogIDsOfCatalog($subCatalog));
        }

        return $catalogs;
    }


    /**
     * Gets all sub catalogs of a given catalog with an given indent symbol for every step
     * 
     * @param  Catalog $catalog  The catalog where to start
     * @param  Catalog $excluded The excluded catalog
     * @param  string  $step     The current step symbols
     * @param  string  $symbol   A symbol for one step
     * @return array             An array with all catalogs
     */
    public static function getSubCatalogsOfCatalogWithIndent($catalog, $excluded = null, $step = '', $symbol = '&nbsp;') {
        $catalogs = array();

        if(is_null($excluded) || $catalog->id !== $excluded->id) {
            $catalogs[$catalog->id] = $step . $catalog->name;

            foreach($catalog->children()->order_by('number', 'asc')->get() as $subCatalog) {
                $catalogs = $catalogs + static::getSubCatalogsOfCatalogWithIndent($subCatalog, $excluded, $step . $symbol);
            }
        }   

        return $catalogs;
    }


    /**
     * Get the tree structure of a given catalog
     * 
     * @param  Catalog $catalog The catalog where to start
     * @return array            An array with the given tree structure of the catalogs
     */
    public static function getTreeStructureOfCatalog($catalog) {
        $catalogs = array();

        $catalogs['id'] = $catalog->id;
        $catalogs['name'] = $catalog->name;
        $catalogs['number'] = $catalog->number;
        $children = array();

        foreach($catalog->children()->order_by('number', 'asc')->get() as $subCatalog) {
            $children[] = static::getTreeStructureOfCatalog($subCatalog);
        }

        $catalogs['children'] = $children;

        return $catalogs;
    }


    /**
     * Remove all questions of all sub catalogs of a given catalog
     * 
     * @param  Catalog $catalog The Catalog object
     */
    public static function removeQuestionsOfSubCatalogs($catalog) {
        $questions = $catalog->questions()->get();

        foreach($questions as $question) {
            $question->delete();
        }

        foreach($catalog->children()->order_by('number', 'asc')->get() as $subCatalog) {
            static::removeQuestionsOfSubCatalogs($subCatalog);
        }
    }

}