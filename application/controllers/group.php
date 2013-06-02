<?php 

class Group_Controller extends Base_Kakadu_Controller {

    private $rules = array(
                            'name'          => 'required|min:5|max:100',
                            'description'   => 'required|min:50|max:500'
                        );


    /**
     * Shows all groups with pagination.
     * 
     * On a ajax request just the list will be returned
     *
     * GET variables:
     * - sort: name, id, created_at (Sorting value)
     * - sort_dir: asc, desc (Sorting direction)
     * - per_page: number (20) (Items per page)
     * - page: number (Actuall page)
     */
    public function get_groups() {

        //Check permissions
        $permission = $this->checkPermissions(Const_Action::ALL);

        if($permission === Const_Permission::DENIED) {
            return View::make('general.permission');
        }


        //Set up the optins
        $append = array();

        if(Input::has('sort')) {
            $sort = Input::get('sort');
            $append['sort'] = $sort;
        } else {
            $sort = 'name';
        }

        if(Input::has('sort_dir')) {
            $sort_dir = Input::get('sort_dir');
            $append['sort_dir'] = $sort_dir;
        } else {
            $sort_dir = 'asc';
        }

        if(Input::has('per_page')) {
            $per_page = Input::get('per_page');
            $append['per_page'] = $per_page;
        } else {
            $per_page = 20;
        }


        //Create view
        $groups = Learngroup::order_by($sort, $sort_dir)->paginate($per_page);
        $tmp = array();

        foreach($groups->results as $group) {
            $tmp[] = $this->getGroupArray($group);
        }


        if(Request::ajax()) {
            $view = View::make('group.list');
        } else {
            $view = View::make('group.groups');
        }

        $view->groups = $tmp;
        $view->links = $groups->appends($append)->links();


        if(Request::ajax()) {
            return $view;
        } else {
            $this->layout->content = $view;
        }
    }


    /**
     * Shows a group.
     * 
     * On an ajax request just the members table will be returned
     */
    public function get_group($id) {

        //Get group
        $this->group = Learngroup::find($id);

        if($this->group === NULL) {
            return Response::error('404');
        }

        //Check permissions
        $permission = $this->checkPermissions(Const_Action::SHOW);

        if($permission === Const_Permission::DENIED) {
            return View::make('general.permission');
        }

        //Admins and users
        $pivot = $this->group->users()->pivot()->get();
        $role_admin = Role::where('name', 'LIKE', 'admin')->first();
        $role_member = Role::where('name', 'LIKE', 'member')->first();

        $admins = array();
        $users = array();

        foreach($pivot as $allocation) {
            $user = Sentry::user((int)$allocation->user_id);

            $data = array(
                'email'         => $user->get('email'),
                'displayname'   => $user->get('metadata.displayname')
            );

            switch($allocation->role_id) {
                case $role_admin->id:
                    $admins[] = $data;
                    $users[] = $data;
                    break;
                    
                case $role_member->id:
                    $users[] = $data;
                    break;

                default:
                    break;
            }
        }

        //Courses
        $result = $this->group->courses()->order_by('name', 'ASC')->get();
        $courses = array();

        foreach($result as $course) {
            $courses[] = array(
                    'id'            => $course->id,
                    'name'          => $course->name,
                    'description'   => $course->description,
                    'created_at'    => $course->created_at,
                    'updated_at'    => $course->updated_at
                );
        }

        //Create view
        if(Request::ajax()) {
            $view = View::make('group.members');
        } else {
            $view = View::make('group.group');
        }
        
        $view->group = $this->getGroupArray($this->group);
        $view->admins = $admins;
        $view->users = $users;
        $view->courses = $courses;

        //Check ajax request
        if(Request::ajax()) {
            return $view;
        }else{
            $this->layout->content = $view;
        }
        
    }


    /**
     * Shows the view to create a group
     */
    public function get_create() {

        //Check permissions
        $permission = $this->checkPermissions(Const_Action::CREATE);

        if($permission === Const_Permission::DENIED) {
            return View::make('general.permission');
        }

        //Create view
        $this->layout->content = View::make('group.create');
    }


    /**
     * Create a group
     */
    public function post_create() {

        $redirect_success = 'group';
        $redirect_error = 'group/create';

        //Validate input
        $response = $this->validateInput($this->rules);

        if ($response !== true) {
            return $this->redirectWithErrors($redirect_error, $response);
        }

        //Check permissions
        $permission = $this->checkPermissions(Const_Action::CREATE);

        if($permission === Const_Permission::DENIED) {
            return View::make('general.permission');
        }

        //Create the group
        $group = new Learngroup;
        $group->name = Input::get('name');
        $group->description = Input::get('description');
        $group->save();

        //Add group admin
        try {
            $user_sentry = Sentry::user();
            $user_kakadu = User::find($user_sentry->get('id'));
        } catch (Sentry\SentryException $e) {
            $messages = array($e->getMessage());
            return $this->redirectWithErrors($redirect_error, $messages);
        }

        $role = Role::where('name', 'LIKE', 'admin')->first();
        $group->users()->attach($user_kakadu, array('role_id' => $role->id));

        return Redirect::to_route($redirect_success, array($group->id));
    }


    /**
     * Shows the view to edit a group
     */
    public function get_edit($id) {
        
        //Get group
        $this->group = Learngroup::find($id);

        if($this->group === NULL) {
            return Response::error('404');
        }

        //Check permissions
        $permission = $this->checkPermissions(Const_Action::EDIT);

        if($permission === Const_Permission::DENIED) {
            return View::make('general.permission');
        }

        //Create view
        $view = View::make('group.edit');
        $this->layout->content = $view;
        $view->group = $this->getGroupArray($this->group);
    }
    
    
    /**
     * Edit a group
     */
    public function post_edit() {

        $redirect_success = 'group';
        $redirect_error = 'group/edit';

        //Validate input
        $this->rules['id'] = 'required|integer|min:0';
        $response = $this->validateInput($this->rules);

        if ($response !== true) {
            $parameters = array(Input::get('id'));
            return $this->redirectWithErrors($redirect_error, $response, $parameters);
        }

        //Get group
        $this->group = Learngroup::find(Input::get('id'));

        if($this->group === NULL) {
            return Response::error('404');
        }

        //Check permissions
        $permission = $this->checkPermissions(Const_Action::EDIT);

        if($permission === Const_Permission::DENIED) {
            return View::make('general.permission');
        }

        //Edit the group
        $this->group->name = Input::get('name');
        $this->group->description = Input::get('description');
        $this->group->save();

        return Redirect::to_route($redirect_success, array($this->group->id));
    }


    /**
     * Delete a group
     */
    public function get_delete($id) {

        $redirect_success = 'groups';
        $redirect_error = 'group';

        //Get the group
        $this->group = Learngroup::find($id);

        if($this->group === NULL) {
            return Response::error('404');
        }

        //Check permissions
        $permission = $this->checkPermissions(Const_Action::DELETE);

        if($permission === Const_Permission::DENIED) {
            return View::make('general.permission');
        }

        //Delete the group
        Helper_Group::deleteGroupAndCheckRelatedCourses($this->group);

        return Redirect::to_route($redirect_success);
    }


    /**
     * Show the view that the group was deleted
     */
    public function get_deleted() {
        $this->layout->content = View::make('group.deleted');
    }


    /**
     * Validate input with the given rules
     * 
     * @return array|boolean Returns a error array when there is validation error or true on a valid validation
     */
    private function validateInput($rules) {
        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            return $validation->errors->all();
        }

        return true;
    }

}
