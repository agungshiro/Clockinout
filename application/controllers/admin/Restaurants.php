<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Restaurants extends Admin_Controller {

    /**
     * @var string
     */
    private $_redirect_url;


    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        // load the language files
        $this->lang->load('restaurants');

        // load the restaurants model
        $this->load->model('restaurants_model');

        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/restaurants'));
        define('DEFAULT_LIMIT', $this->settings->per_page_limit);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "name");
        define('DEFAULT_DIR', "asc");

        // use the url in session (if available) to return to the previous filter/sorted/paginated list
        if ($this->session->userdata(REFERRER))
        {
            $this->_redirect_url = $this->session->userdata(REFERRER);
        }
        else
        {
            $this->_redirect_url = THIS_URL;
        }
    }


    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/


    /**
     * User list page
     */
    function index()
    {
        // get parameters
        $limit  = $this->input->get('limit')  ? $this->input->get('limit', TRUE)  : DEFAULT_LIMIT;
        $offset = $this->input->get('offset') ? $this->input->get('offset', TRUE) : DEFAULT_OFFSET;
        $sort   = $this->input->get('sort')   ? $this->input->get('sort', TRUE)   : DEFAULT_SORT;
        $dir    = $this->input->get('dir')    ? $this->input->get('dir', TRUE)    : DEFAULT_DIR;

        // get filters
        $filters = array();

        if ($this->input->get('name'))
        {
            $filters['name'] = $this->input->get('name', TRUE);
        }

        // build filter string
        $filter = "";
        foreach ($filters as $key => $value)
        {
            $filter .= "&{$key}={$value}";
        }

        // save the current url to session for returning
        $this->session->set_userdata(REFERRER, THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}&offset={$offset}{$filter}");

        // are filters being submitted?
        if ($this->input->post())
        {
            if ($this->input->post('clear'))
            {
                // reset button clicked
                redirect(THIS_URL);
            }
            else
            {
                // apply the filter(s)
                $filter = "";

                if ($this->input->post('name'))
                {
                    $filter .= "&name=" . $this->input->post('name', TRUE);
                }

                // redirect using new filter(s)
                redirect(THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}&offset={$offset}{$filter}");
            }
        }

        // get list
        $restaurants = $this->restaurants_model->get_all($limit, $offset, $filters, $sort, $dir);

        // build pagination
        $this->pagination->initialize(array(
            'base_url'   => THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}{$filter}",
            'total_rows' => $restaurants['total'],
            'per_page'   => $limit
        ));

        // setup page header data
		$this
			->add_js_theme('restaurants.js', TRUE )
			->set_title(lang('restaurants title restaurant_list'));

        $data = $this->includes;

        // set content data
        $content_data = array(
            'this_url'   => THIS_URL,
            'restaurants'=> $restaurants['results'],
            'total'      => $restaurants['total'],
            'filters'    => $filters,
            'filter'     => $filter,
            'pagination' => $this->pagination->create_links(),
            'limit'      => $limit,
            'offset'     => $offset,
            'sort'       => $sort,
            'dir'        => $dir
        );

        // load views
        $data['content'] = $this->load->view('admin/restaurants/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }


    /**
     * Add new user
     */
    function add()
    {
        // validators
        $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
        $this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[5]|max_length[30]|callback__check_name[]');
        $this->form_validation->set_rules('address', 'Address', 'trim|min_length[2]');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|min_length[2]|max_length[13]|numeric');
        $this->form_validation->set_rules('email', 'Email', 'trim|max_length[128]|valid_email|callback__check_email[]');
        $this->form_validation->set_rules('open_hour', 'Open Hour', 'required');
        $this->form_validation->set_rules('shift_hour', 'Shift Hour', 'required');
        $this->form_validation->set_rules('close_hour', 'Close Hour', 'required');

        if ($this->form_validation->run() == TRUE)
        {
            // save the new user
            $saved = $this->restaurants_model->add_restaurant($this->input->post());

            if ($saved)
            {
                $this->session->set_flashdata('message', sprintf(lang('restaurants msg add_restaurant_success'), $this->input->post('first_name') . " " . $this->input->post('last_name')));
            }
            else
            {
                $this->session->set_flashdata('error', sprintf(lang('restaurants error add_restaurant_failed'), $this->input->post('first_name') . " " . $this->input->post('last_name')));
            }

            // return to list and display message
            redirect($this->_redirect_url);
        }

        // setup page header data
        $this
            ->add_js_theme('clockpicker.js', TRUE )
            ->add_css_theme('clockpicker.css', TRUE )
            ->add_js_theme('restaurants.js', TRUE )
            ->set_title(lang('restaurants title user_add'));

        $data = $this->includes;

        // set content data
        $content_data = array(
            'cancel_url'        => $this->_redirect_url,
            'restaurant'              => NULL,
            'password_required' => TRUE
        );

        // load views
        $data['content'] = $this->load->view('admin/restaurants/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }


    /**
     * Edit existing user
     *
     * @param  int $id
     */
    function edit($id=NULL)
    {
        // make sure we have a numeric id
        if (is_null($id) OR ! is_numeric($id))
        {
            redirect($this->_redirect_url);
        }

        // get the data
        $restaurant = $this->restaurants_model->get_restaurant($id);

        // if empty results, return to list
        if ( ! $restaurant)
        {
            redirect($this->_redirect_url);
        }

        // validators
        $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
        $this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[5]|max_length[30]|callback__check_name['.$restaurant['name'].']');
        $this->form_validation->set_rules('address', 'Address', 'trim|min_length[2]');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|min_length[2]|max_length[13]|numeric');
        $this->form_validation->set_rules('email', 'Email', 'trim|max_length[128]|valid_email|callback__check_email['.$restaurant['email'].']');
        $this->form_validation->set_rules('open_hour', 'Open Hour', 'required');
        $this->form_validation->set_rules('shift_hour', 'Shift Hour', 'required');
        $this->form_validation->set_rules('close_hour', 'Close Hour', 'required');

        if ($this->form_validation->run() == TRUE)
        {
            // save the changes
            $saved = $this->restaurants_model->edit_restaurant($this->input->post());

            if ($saved)
            {
                $this->session->set_flashdata('message', sprintf(lang('restaurants msg edit_restaurant_success'), $this->input->post('first_name') . " " . $this->input->post('last_name')));
            }
            else
            {
                $this->session->set_flashdata('error', sprintf(lang('restaurants error edit_restaurant_failed'), $this->input->post('first_name') . " " . $this->input->post('last_name')));
            }

            // return to list and display message
            redirect($this->_redirect_url);
        }

        // setup page header data
        $this
            ->add_js_theme('clockpicker.js', TRUE )
            ->add_css_theme('clockpicker.css', TRUE )
            ->add_js_theme('restaurants.js', TRUE )
            ->set_title(lang('restaurants title restaurant_edit'));

        $data = $this->includes;

        // set content data
        $content_data = array(
            'cancel_url'        => $this->_redirect_url,
            'restaurant'              => $restaurant,
            'restaurant_id'           => $id,
            'password_required' => FALSE
        );

        // load views
        $data['content'] = $this->load->view('admin/restaurants/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }


    /**
     * Delete a user
     *
     * @param  int $id
     */
    function delete($id=NULL)
    {
        // make sure we have a numeric id
        if ( ! is_null($id) OR ! is_numeric($id))
        {
            // get user details
            $restaurant = $this->restaurants_model->get_restaurant($id);

            if ($restaurant)
            {
                // soft-delete the user
                $delete = $this->restaurants_model->delete_restaurant($id);

                if ($delete)
                {
                    $this->session->set_flashdata('message', sprintf(lang('restaurants msg delete_restaurant'), $restaurant['first_name'] . " " . $restaurant['last_name']));
                }
                else
                {
                    $this->session->set_flashdata('error', sprintf(lang('restaurants error delete_restaurant'), $restaurant['first_name'] . " " . $restaurant['last_name']));
                }
            }
            else
            {
                $this->session->set_flashdata('error', lang('restaurants error user_not_exist'));
            }
        }
        else
        {
            $this->session->set_flashdata('error', lang('restaurants error user_id_required'));
        }

        // return to list and display message
        redirect($this->_redirect_url);
    }


    /**
     * Export list to CSV
     */
    function export()
    {
        // get parameters
        $sort = $this->input->get('sort') ? $this->input->get('sort', TRUE) : DEFAULT_SORT;
        $dir  = $this->input->get('dir')  ? $this->input->get('dir', TRUE)  : DEFAULT_DIR;

        // get filters
        $filters = array();

        if ($this->input->get('name'))
        {
            $filters['name'] = $this->input->get('name', TRUE);
        }

        // get all restaurants
        $restaurants = $this->restaurants_model->get_all(0, 0, $filters, $sort, $dir);

        if ($restaurants['total'] > 0)
        {
            // manipulate the output array
            foreach ($restaurants['results'] as $key=>$restaurant)
            {
                unset($restaurants['results'][$key]['password']);
                unset($restaurants['results'][$key]['deleted']);

                if ($restaurant['status'] == 0)
                {
                    $restaurants['results'][$key]['status'] = lang('admin input inactive');
                }
                else
                {
                    $restaurants['results'][$key]['status'] = lang('admin input active');
                }
            }

            // export the file
            array_to_csv($restaurants['results'], "restaurants");
        }
        else
        {
            // nothing to export
            $this->session->set_flashdata('error', lang('core error no_results'));
            redirect($this->_redirect_url);
        }

        exit;
    }

    function joblist($id = NULL) {
        $this->load->model('joblist_model');

        $restaurant = $this->restaurants_model->get_restaurant($id);

         // get parameters
         $limit  = $this->input->get('limit')  ? $this->input->get('limit', TRUE)  : DEFAULT_LIMIT;
         $offset = $this->input->get('offset') ? $this->input->get('offset', TRUE) : DEFAULT_OFFSET;
         $sort   = $this->input->get('sort')   ? $this->input->get('sort', TRUE)   : DEFAULT_SORT;
         $dir    = $this->input->get('dir')    ? $this->input->get('dir', TRUE)    : DEFAULT_DIR;
 
         // get filters
         $filters = array();
 
         if ($this->input->get('name'))
         {
             $filters['name'] = $this->input->get('name', TRUE);
         }
 
         // build filter string
         $filter = "";
         foreach ($filters as $key => $value)
         {
             $filter .= "&{$key}={$value}";
         }
 
         // save the current url to session for returning
         $this->session->set_userdata(REFERRER, THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}&offset={$offset}{$filter}");
 
         // are filters being submitted?
         if ($this->input->post())
         {
             if ($this->input->post('clear'))
             {
                 // reset button clicked
                 redirect(THIS_URL);
             }
             else
             {
                 // apply the filter(s)
                 $filter = "";
 
                 if ($this->input->post('name'))
                 {
                     $filter .= "&name=" . $this->input->post('name', TRUE);
                 }
 
                 // redirect using new filter(s)
                 redirect(THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}&offset={$offset}{$filter}");
             }
         }
 
         // get list
         $joblist = $this->joblist_model->get_all($id,$limit, $offset, $filters, $sort, $dir);
 
         // build pagination
         $this->pagination->initialize(array(
             'base_url'   => THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}{$filter}",
             'total_rows' => $joblist['total'],
             'per_page'   => $limit
         ));
 
         // setup page header data
         $this
             ->add_js_theme('joblist.js', TRUE )
             ->set_title($restaurant['name']."'s Job List");
 
         $data = $this->includes;
 
         // set content data
         $content_data = array(
             'this_url'   => THIS_URL,
             'joblist'    => $joblist['results'],
             'total'      => $joblist['total'],
             'filters'    => $filters,
             'filter'     => $filter,
             'pagination' => $this->pagination->create_links(),
             'limit'      => $limit,
             'offset'     => $offset,
             'sort'       => $sort,
             'dir'        => $dir,
             'id_restaurant' => $restaurant['id']
         );
 
         // load views
         $data['content'] = $this->load->view('admin/restaurants/joblist', $content_data, TRUE);
         $this->load->view($this->template, $data);
    }

    function add_job($id_restaurant) {
        $this->load->model('joblist_model');
        // validators
        $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
        $this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[5]|max_length[100]|callback__check_jobname[]');
        $this->form_validation->set_rules('time_limit', 'Hour Limit', 'required|numeric');
        $this->form_validation->set_rules('shift_limit', 'Shift Limit', 'required|numeric');
        $this->form_validation->set_rules('id_restaurant', 'ID Restaurant', 'required|numeric');

        if ($this->form_validation->run() == TRUE)
        {
            // save the new user
            $saved = $this->joblist_model->add($this->input->post());

            if ($saved)
            {
                $this->session->set_flashdata('message', sprintf('A new job successfully added : '.$this->input->post('name')));
            }
            else
            {
                $this->session->set_flashdata('error', 'Adding new job failed');
            }

            // return to list and display message
            redirect($this->_redirect_url);
        }

        // setup page header data
        $this
            ->set_title('Add Job');

        
        // set content data
        $content_data = array(
            'cancel_url'        => $this->_redirect_url,
            'id_restaurant'        => $id_restaurant
        );

        $data = $this->includes;


        $data['content'] = $this->load->view('admin/restaurants/jobform', $content_data, TRUE);
        $this->load->view($this->template, $data);
    } 

    function edit_job($id) {
        $this->load->model('joblist_model');

        // make sure we have a numeric id
        if (is_null($id) OR ! is_numeric($id))
        {
            redirect($this->_redirect_url);
        }

        // get the data
        $job = $this->joblist_model->get($id);

        // if empty results, return to list
        if ( ! $job)
        {
            redirect($this->_redirect_url);
        }

        // validators
        $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
        $this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[5]|max_length[100]|callback__check_jobname['.$job['name'].']');
        $this->form_validation->set_rules('time_limit', 'Hour Limit', 'required|numeric');
        $this->form_validation->set_rules('shift_limit', 'Shift Limit', 'required|numeric');
        $this->form_validation->set_rules('id_restaurant', 'ID Restaurant', 'required|numeric');

        if ($this->form_validation->run() == TRUE)
        {
            // save the changes
            $saved = $this->joblist_model->edit($this->input->post());

            if ($saved)
            {
                $this->session->set_flashdata('message', 'Successfully editing job '.$this->input->post('name'));
            }
            else
            {
                $this->session->set_flashdata('error', 'Error editing job '.$job['name']);
            }

            // return to list and display message
            redirect($this->_redirect_url);
        }

        // setup page header data
        $this
            ->set_title('Editing job');

        $data = $this->includes;

        // set content data
        $content_data = array(
            'cancel_url'        => $this->_redirect_url,
            'job'              => $job,
            'id_restaurant'     => $job['id_restaurant'],
            'id_job'            => $id
        );


        $data['content'] = $this->load->view('admin/restaurants/jobform', $content_data, TRUE);
         $this->load->view($this->template, $data);
    }

    function delete_job($id) {
        $this->load->model('joblist_model');
        // make sure we have a numeric id
        if ( ! is_null($id) OR ! is_numeric($id))
        {
            // get user details
            $job = $this->joblist_model->get($id);

            if ($job)
            {
                // soft-delete the user
                $delete = $this->joblist_model->delete($id);

                if ($delete)
                {
                    $this->session->set_flashdata('message', $job['name'].' successfully deleted' );
                }
                else
                {
                    $this->session->set_flashdata('error', 'Fail to delete '.$job['name']);
                }
            }
            else
            {
                $this->session->set_flashdata('error', 'This Job is not exist');
            }
        }
        else
        {
            $this->session->set_flashdata('error', "Job's id is required");
        }

        // return to list and display message
        redirect($this->_redirect_url);
    }


    /**************************************************************************************
     * PRIVATE VALIDATION CALLBACK FUNCTIONS
     **************************************************************************************/


    /**
     * Make sure name is available
     *
     * @param  string $name
     * @param  string|null $current
     * @return int|boolean
     */
    function _check_name($name, $current)
    {
        if (trim($name) != trim($current) && $this->restaurants_model->name_exists($name))
        {
            $this->form_validation->set_message('_check_name', sprintf(lang('restaurants error name_exists'), $name));
            return FALSE;
        }
        else
        {
            return $name;
        }
    }


    /**
     * Make sure email is available
     *
     * @param  string $email
     * @param  string|null $current
     * @return int|boolean
     */
    function _check_email($email, $current)
    {
        if (trim($email) != trim($current) && $this->restaurants_model->email_exists($email))
        {
            $this->form_validation->set_message('_check_email', sprintf(lang('restaurants error email_exists'), $email));
            return FALSE;
        }
        else
        {
            return $email;
        }
    }

    function _check_jobname($name, $current)
    {
        $this->load->model('joblist_model');
        if (trim($name) != trim($current) && $this->joblist_model->job_exists($name))
        {
            $this->form_validation->set_message('_check_jobname', sprintf('Job exist : '.$name));
            return FALSE;
        }
        else
        {
            return $name;
        }
    }

}
