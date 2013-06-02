<?php

class Authentification_Controller extends Base_Controller {


    /**
     * Show the login site
     */
    public function get_login() {
        return View::make('authentification.login');
    }



    /**
     * Log the user in
     */
    public function post_login() {
        
        //Validate input
        $rules = array(
            'email'         => 'required|email',
            'password'      => 'required'
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            $messages = $validation->errors->all();
            return Redirect::back()->with_errors($messages)->with_input();
        }


        //Try to log the user in
        try
        {
            $valid_login = Sentry::login(Input::get('email'), Input::get('password'), Input::get('remember'));

            if ($valid_login) {
                //Set language
                $user = Sentry::user(Input::get('email'));
                Cookie::forever('language', $user->get('metadata.language'));
                
                return Redirect::back();
            } else {
                $messages = array(__('authentification.email_or_password_not_correct'));
                return Redirect::back()->with_errors($messages)->with_input();
            }
        }
        catch (Sentry\SentryException $e)
        {
            $messages = array($e->getMessage());
            return Redirect::back()->with_errors($messages)->with_input();
        }
    }
    


    /**
     * Log the user out
     */
    public function get_logout() {
        Sentry::logout();
        return Redirect::to_route('home');
    }



    /**
     * Show the registration site
     */
    public function get_register() {
        $this->layout->content = View::make('authentification.register');
    }



    /**
     * Register the user
     */
    public function post_register() {
        
        $redirect_success = 'auth/confirmemail';
        $redirect_error = 'auth/register';


        //Validate input
        $rules = array(
            'displayname'   => 'required',
            'email'         => 'required|email',
            'password'      => 'required|confirmed'
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            $messages = $validation->errors->all();
            return $this->redirectWithErrors($redirect_error, $messages);
        }


        //Try to register the user
        try
        {
            // create the user
            $user = Sentry::user()->register(array(
                'email' => trim(Input::get('email')),
                'password' => Input::get('password'),
                'metadata' => array(
                    'displayname' => trim(Input::get('displayname')),
                    'language' => 'en'
                )
            ));

            if ($user) {
                //Sending activation link
                $link = URL::to('auth/activate/' . $user['hash']);

                $mailer = IoC::resolve('phpmailer');
                $mailer->AddAddress(Input::get('email'));
                $mailer->Subject  = 'Kakadu - ' . __('authentification.activation_subject');
                $mailer->Body     = __('authentification.activation_message') . $link;
                $mailer->Send();
                
                return Redirect::to_route($redirect_success);
            } else {
                $messages = array(__('authentification.registration_faild'));
                return $this->redirectWithErrors($redirect_error, $messages);
            }
        }
        catch (Sentry\SentryException $e)
        {
            $messages = array($e->getMessage());
            return $this->redirectWithErrors($redirect_error, $messages);
        }
        catch (Exception $e)
        {
            $messages = array(__('mail.message_not_send') . $e->getMessage());
            return $this->redirectWithErrors($redirect_error, $messages);
        }
    }



    /**
     * Activate the user
     * @param  [type] $email Decoded email
     * @param  [type] $code  Activation code
     */
    public function get_activate($email = null, $code = null) {

        $redirect_success = 'auth/activate';
        $redirect_error = 'auth/activate';

        //Check if email and code exist
        if($email === null || $code === null) {
            $this->layout->content = View::make('authentification.activate');
            return;
        }

        //Try to activate the user
        try
        {
            $activate_user = Sentry::activate_user($email, $code);

            if ($activate_user) {
                return Redirect::to_route($redirect_success)->with('info', __('authentification.activation_success'));
            } else {
                $messages = array(__('authentification.activation_faild'));
                return $this->redirectWithErrors($redirect_error, $messages);
            }
        }
        catch (Sentry\SentryException $e)
        {
            $messages = array($e->getMessage());
            return $this->redirectWithErrors($redirect_error, $messages);
        }
    }



    /**
     * Dispaly the message that an email was send to confirm the registration
     */
    public function get_confirmemail() {
        $this->layout->content = View::make('authentification.confirmemail');
    }



    /**
     * Show the site to reset the password if the user forgot it
     */
    public function get_forgotpassword() {
        $this->layout->content = View::make('authentification.forgotpassword');
    }



    /**
     * Reset the password and send the new password to the email address
     */
    public function post_forgotpassword() {
        
        $redirect_success = 'auth/forgotpassword';
        $redirect_error = 'auth/forgotpassword';


        //Validate input
        $rules = array(
            'email'         => 'required|email'
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            $messages = $validation->errors->all();
            return $this->redirectWithErrors($redirect_error, $messages);
        }


        //Try to set a new password
        try
        {
            $new_password = Str::random(32);

            //Update user
            $user = Sentry::user(Input::get('email'));
            $update = $user->update(array(
                'password' => $new_password
            ));

            if ($update) {
                $mailer = IoC::resolve('phpmailer');
                $mailer->AddAddress(Input::get('email'));
                $mailer->Subject  = 'Kakadu - ' . __('authentification.password_reset_subject');
                $mailer->Body     = __('authentification.password_reset_message') . $new_password;
                $mailer->Send();

                return Redirect::to_route($redirect_success)
                                ->with('info', __('authentification.password_reset_success'));
            } else {
                $messages = array(__('authentification.password_reset_faild'));
                return $this->redirectWithErrors($redirect_error, $messages);
            }
        }
        catch (Sentry\SentryException $e)
        {
            $messages = array($e->getMessage());
            return $this->redirectWithErrors($redirect_error, $messages);
        }
        catch (Exception $e)
        {
            $messages = array(__('mail.message_not_send'));
            return $this->redirectWithErrors($redirect_error, $messages);
        }
    }

}