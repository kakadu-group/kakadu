<?php

class Roles {

    /**
     * Make changes to the database.
     *
     * @return void
     */
    public function up()
    {
        $role1 = new Role();
        $role1->name = 'admin';
        $role1->description = 'Administrator';
        $role1->save();

        $role2 = new Role();
        $role2->name = 'member';
        $role2->description = 'Member';
        $role2->save();
    }

    /**
     * Revert the changes to the database.
     *
     * @return void
     */
    public function down()
    {
        DB::query('DELETE FROM roles WHERE name LIKE "admin"');
        DB::query('DELETE FROM roles WHERE name LIKE "member"');
    }

}