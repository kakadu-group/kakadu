<?php

class Default_Groups {

    /**
     * Make changes to the database.
     *
     * @return void
     */
    public function up()
    {
        //Create admin and user group
        Bundle::start('sentry');
        
        try
        {
            $admin_id = Sentry::group()->create(array('name' => 'admin'));
            
            $admin = Sentry::group($admin_id);
            $admin->update_permissions(array(
                    'admin' => 1
            ));
        }
        catch (Sentry\SentryException $e)
        {
            printf($e->getMessage());
        }
    }

    /**
     * Revert the changes to the database.
     *
     * @return void
     */
    public function down()
    {
        //Delete admin and user group
        Bundle::start('sentry');

        try
        {
            Sentry::group('admin')->delete();
        }
        catch (Sentry\SentryException $e)
        {
            printf($e->getMessage());
        }
    }

}