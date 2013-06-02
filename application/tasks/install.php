<?php

class Install_Task {


    /**
     * Installing
     */
    public function run($arguments) {
        require path('sys').'cli/dependencies'.EXT;

        //key:generate
        $args = array('key:generate');
        $this->runCommand($args);

        //migrate:install
        $args = array('migrate:install');
        $this->runCommand($args);
    }


    /**
     * Seting up the migrations
     * @param  array  $arguments The arguments must contain the informations of the admin
     *                           containing displayname, email and password
     *                           (php artisan install Admin admin@example.com password)
     */
    public function setup($arguments) {
        require path('sys').'cli/dependencies'.EXT;

        //Check arguments
        if(count($arguments) != 3) {
            echo 'No informations set in the arguments for the admin account.';
            return;
        }

        $displayname = $arguments[0];
        $email = $arguments[1];
        $password = $arguments[2];

        //migrate
        $args = array('migrate');
        $this->runCommand($args);


        //Logout and reset sentry cache
        Sentry::logout();

        $sentry = new ReflectionClass('Sentry');

        $current_user = $sentry->getProperty('current_user');
        $current_user->setAccessible(true);
        $current_user->setValue(null);

        $user_cache = $sentry->getProperty('user_cache');
        $user_cache->setAccessible(true);
        $user_cache->setValue(array());


        //Create admin
        $vars = array(
            'email' => $email,
            'password' => $password,
            'metadata' => array(
                'displayname' => $displayname,
                'language' => 'en'
            )
        );

        $id = Sentry::user()->create($vars);
        $user = Sentry::user($id);
        $user->add_to_group('admin');
    }


    /**
     * Reseting the migrations
     */
    public function reset($arguments) {
        require path('sys').'cli/dependencies'.EXT;
        
        //migrate:reset
        $args = array('migrate');
        $this->runCommand($args);
    }


    /**
     * Runs a command with the given arguments
     * @param  array $args
     */
    private function runCommand($args) {
        Laravel\CLI\Command::run($args);
    }

}