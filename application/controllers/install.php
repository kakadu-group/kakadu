<?php

class Install_Controller extends Controller {

    public $restful = true;
    public $layout = 'layouts.install';


    public function __construct(){
        parent::__construct();
        
        //csrf
        $this->filter('before', 'csrf')->on('post');
    }


    /**
     * Display the install screen.
     */
    public function get_index() {
        $view = View::make('install.index');
        $this->layout->content = $view;
    }


    /**
     * Make the installation
     */
    public function post_install() {

        $redirect_success = 'install/finished';
        $redirect_error = 'install';

        //Validate input
        $rules = array(
            'user_displayname'  => 'required',
            'user_email'        => 'required|email',
            'user_password'     => 'required|confirmed',

            'db_host'           => 'required',
            'db_database'       => 'required',
            'db_username'       => 'required',
            'db_password'       => 'required|confirmed',
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            $messages = $validation->errors->all();
            return Redirect::to_route($redirect_error)->with_errors($messages)->with_input();
        }

        //Get database settings
        $host = Input::get('db_host');
        $database = Input::get('db_database');
        $username = Input::get('db_username');
        $password = Input::get('db_password');

        //Set database runtime settings
        $connections = Config::get('database.connections');

        $mysql = $connections['mysql'];
        $mysql['host'] = $host;
        $mysql['database'] = $database;
        $mysql['username'] = $username;
        $mysql['password'] = $password;
        $connections['mysql'] = $mysql;

        Config::set('database.connections', $connections);

        //Save database settings in file
        $content = '<?php return array(\'connections\' => ' . var_export($connections, true) . ');';
        $content = preg_replace('/array \(/', 'array(', $content);
        File::put(getcwd() . '/application/config/kakadu/database.php', $content);

        //Run artisan commands
        $displayname = Input::get('user_displayname');
        $email = Input::get('user_email');
        $password = Input::get('user_password');

        //Suppress output
        ob_start();

        //Run tasks
        Laravel\CLI\Command::run(array('install'));
        Laravel\CLI\Command::run(array('install:setup', $displayname, $email, $password));

        //Get suppressed output
        $output = ob_get_clean();

        return Redirect::to_route($redirect_success);
    }


    /**
     * Display the finished install screen.
     */
    public function get_finished() {
        $view = View::make('install.finished');
        $this->layout->content = $view;
    }

}