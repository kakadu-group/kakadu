<?php

class Language_Controller extends Base_Controller {


    /**
     * Save the language of the guest
     */
    public function post_edit() {
        
        $redirect_success = 'home';
        $redirect_error = 'home';

        //Validate input
        $rules = array(
            'language'      => 'required|max:2'
        );

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            $messages = $validation->errors->all();
            return $this->redirectWithErrors($redirect_error, $messages);
        }

        //Check if language exists
        $language = Input::get('language');
        $accepted_languages = Config::get('application.languages_accepted');

        foreach($accepted_languages as $key => $value) {
            if($language === $key) {
                Cookie::forever('language', $language);

                if(Sentry::check()) {
                    //Change the language in the user profile
                    try {
                        $user = Sentry::user();
                        $user->update(array(
                            'metadata' => array(
                                'language' => $language
                            )
                        ));
                    } catch (Sentry\SentryException $e) {
                        $messages = array($e->getMessage());
                        return $this->redirectWithErrors($redirect_error, $messages);
                    }
                }

                return Redirect::back();
            }
        }

        $messages = array(__('language.language_not_found'));
        return $this->redirectWithErrors($redirect_error, $messages);
    }

}