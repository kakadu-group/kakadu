<?php

class Tables {

    /**
     * Make changes to the database.
     *
     * @return void
     */
    public function up()
    {
        //Role
        $attributes = array(
                array('name' => 'name', 'type' => 'string', 'length' => 100),
                array('name' => 'description', 'type' => 'string', 'length' => 200),
            );
        $this->createTable('roles', $attributes, array());


        //Learngroups
        $attributes = array(
                array('name' => 'name', 'type' => 'string', 'length' => 100),
                array('name' => 'description', 'type' => 'string', 'length' => 500),
            );
        $this->createTable('learngroups', $attributes, array());

        //User_Learngroups
        $foreignKeys = array(
                array('name' => 'role_id', 'type' => 'integer', 'unsigned' => true, 'nullable' => false, 'table' => 'roles', 'referenced' => true),
                array('name' => 'learngroup_id', 'type' => 'integer', 'unsigned' => true, 'nullable' => false, 'table' => 'learngroups', 'referenced' => true),
                array('name' => 'user_id', 'type' => 'integer', 'unsigned' => true, 'nullable' => false, 'table' => 'users', 'referenced' => false),
            );
        $this->createTable('user_learngroups', array(), $foreignKeys);

        //Catalog
        $attributes = array(
                array('name' => 'name', 'type' => 'string', 'length' => 100),
                array('name' => 'number', 'type' => 'integer', 'unsigned' => true, 'nullable' => false),
            );
        $foreignKeys = array(
                array('name' => 'parent', 'type' => 'integer', 'unsigned' => true, 'nullable' => true, 'table' => 'catalogs', 'referenced' => true),
            );
        $this->createTable('catalogs', $attributes, $foreignKeys);

        //Questions
        $attributes = array(
                array('name' => 'type', 'type' => 'string', 'length' => 10),
                array('name' => 'question', 'type' => 'text'),
                array('name' => 'answer', 'type' => 'text'),
            );
        $this->createTable('questions', $attributes, array());

        //Catalog_Questions
        $foreignKeys = array(
                array('name' => 'question_id', 'type' => 'integer', 'unsigned' => true, 'nullable' => false, 'table' => 'questions', 'referenced' => true),
                array('name' => 'catalog_id', 'type' => 'integer', 'unsigned' => true, 'nullable' => false, 'table' => 'catalogs', 'referenced' => true),
            );
        $this->createTable('catalog_questions', array(), $foreignKeys);

        //Courses
        $attributes = array(
                array('name' => 'name', 'type' => 'string', 'length' => 100),
                array('name' => 'description', 'type' => 'string', 'length' => 500),
            );
        $foreignKeys = array(
                array('name' => 'catalog', 'type' => 'integer', 'unsigned' => true, 'nullable' => false, 'table' => 'catalogs', 'referenced' => true),
            );
        $this->createTable('courses', $attributes, $foreignKeys);

        //Learngroup_Courses
        $foreignKeys = array(
                array('name' => 'learngroup_id', 'type' => 'integer', 'unsigned' => true, 'nullable' => false, 'table' => 'learngroups', 'referenced' => true),
                array('name' => 'course_id', 'type' => 'integer', 'unsigned' => true, 'nullable' => false, 'table' => 'courses', 'referenced' => true),
            );
        $this->createTable('learngroup_courses', array(), $foreignKeys);

        //Favorites
        $foreignKeys = array(
                array('name' => 'catalog_id', 'type' => 'integer', 'unsigned' => true, 'nullable' => false, 'table' => 'catalogs', 'referenced' => true),
                array('name' => 'user_id', 'type' => 'integer', 'unsigned' => true, 'nullable' => false, 'table' => 'users', 'referenced' => false),
            );
        $this->createTable('favorites', array(), $foreignKeys);

        //Flashcards
        $attributes = array(
                array('name' => 'index', 'type' => 'integer', 'unsigned' => true, 'nullable' => false),
                array('name' => 'offset', 'type' => 'integer', 'unsigned' => true, 'nullable' => false),
                array('name' => 'number_correct', 'type' => 'integer', 'unsigned' => true, 'nullable' => false),
                array('name' => 'number_incorrect', 'type' => 'integer', 'unsigned' => true, 'nullable' => false),
            );
        $foreignKeys = array(
                array('name' => 'question_id', 'type' => 'integer', 'unsigned' => true, 'nullable' => false, 'table' => 'questions', 'referenced' => true),
                array('name' => 'user_id', 'type' => 'integer', 'unsigned' => true, 'nullable' => false, 'table' => 'users', 'referenced' => false),
            );
        $this->createTable('flashcards', $attributes, $foreignKeys);
    }

    /**
     * Revert the changes to the database.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('flashcards');
        Schema::drop('favorites');
        Schema::drop('learngroup_courses');
        Schema::drop('courses');
        Schema::drop('catalog_questions');
        Schema::drop('questions');
        Schema::drop('catalogs');
        Schema::drop('user_learngroups');
        Schema::drop('learngroups');
        Schema::drop('roles');
    }

    /**
     * Create a table
     * @param  string $name        Table name
     * @param  array $attributes   An array with all attributes
     * @param  array $foreignKeys  An array with all foreign keys
     */
    private function createTable($name, $attributes, $foreignKeys) {
        Schema::create($name, function($table) use ($attributes, $foreignKeys)
        {
            $table->engine = 'InnoDB';
            
            $table->increments('id');

            foreach($attributes as $attribute) {
                switch($attribute['type']) {
                    case 'string':
                        $table->string($attribute['name'], $attribute['length']);
                        break;


                    case 'text':
                        $table->text($attribute['name']);
                        break;


                    case 'integer':
                        $column = $table->integer($attribute['name']);

                        if($attribute['unsigned'] === true){
                            $column->unsigned();
                        }

                        if($attribute['nullable'] === true) {
                            $column->nullable();
                        }

                        break;
                }
            }
            
            $table->timestamps();

            foreach($foreignKeys as $foreignKey) {
                switch($foreignKey['type']) {
                    case 'integer':
                        $column = $table->integer($foreignKey['name']);

                        if($foreignKey['unsigned'] === true){
                            $column->unsigned();
                        }

                        if($foreignKey['nullable'] === true) {
                            $column->nullable();
                        }
                }

                if($foreignKey['referenced'] === true) {
                    $table->foreign($foreignKey['name'])->references('id')->on($foreignKey['table'])
                        ->on_delete('cascade')
                        ->on_update('cascade');
                }
            }
        });
    }

}
