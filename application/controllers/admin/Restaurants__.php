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
        // $this->lang->load('users');

        // load the users model
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
            $filters['username'] = $this->input->get('username', TRUE);
        }

        // build filter string
        $filter = "";
        foreach ($filters as $key => $value)
        {
            $filter .= "&{$key}={$value}";
        }

        // save the current url to session for returning
        $this->session->set_restaurantdata(REFERRER, THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}&offset={$offset}{$filter}");

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
			->add_js_theme('restaurants_i18n.js', TRUE )
			->set_title('List of Restaurants');

        $data = $this->includes;

        // set content data
        $content_data = array(
            'this_url'   => THIS_URL,
            'restaurants'      => $restaurants['results'],
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
        $this->form_validation->set_rules('close_hour', 'Close Hour', 'required');

        if ($this->form_validation->run() == TRUE)
        {
            // save the new user
            $saved = $this->restaurants_model->add_restaurant($this->input->post());

            if ($saved)
            {
                $this->session->set_flashdata('message', sprintf('Adding Restaurant Successfull', $this->input->post('name')));
            }
            else
            {
                $this->session->set_flashdata('error', sprintf('Error adding restaurant ', $this->input->post('name')));
            }

            // return to list and display message
            redirect($this->_redirect_url);
        }

        // setup page header data
        $this->set_title('Add restaurant');

        $data = $this->includes;

        // set content data
        $content_data = array(
            'cancel_url'        => $this->_redirect_url,
            'user'              => NULL,
            'password_required' => TRUE
        );

        // load views
        $data['content'] = $this->load->view('admin/restaurats/form', $content_data, TRUE);
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
        $this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[5]|max_length[30]|callback__check_name[]');
        $this->form_validation->set_rules('address', 'Address', 'trim|min_length[2]');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|min_length[2]|max_length[13]|numeric');
        $this->form_validation->set_rules('email', 'Email', 'trim|max_length[128]|valid_email|callback__check_email[]');
        $this->form_validation->set_rules('open_hour', 'Open Hour', 'required');
        $this->form_validation->set_rules('close_hour', 'Close Hour', 'required');

        if ($this->form_validation->run() == TRUE)
        {
            // save the changes
            $saved = $this->restaurants_model->edit_restaurant($this->input->post());

            if ($saved)
            {
                $this->session->set_flashdata('message', sprintf('Editing resturant successfull', $this->input->post('name')));
            }
            else
            {
                $this->session->set_flashdata('error', sprintf('Editing resturant error', $this->input->post('name')));
            }

            // return to list and display message
            redirect($this->_redirect_url);
        }

        // setup page header data
        $this->set_title(lang('users title user_edit'));

        $data = $this->includes;

        // set content data
        $content_data = array(
            'cancel_url'        => $this->_redirect_url,
            'user'              => $restaurant,
            'user_id'           => $id,
            'password_required' => FALSE
        );

        // load views
        $data['content'] = $this->load->view('admin/users/form', $content_data, TRUE);
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
                    $this->session->set_flashdata('message', sprintf(lang('users msg delete_restaurant'), $restaurant['first_name'] . " " . $restaurant['last_name']));
                }
                else
                {
                    $this->session->set_flashdata('error', sprintf(lang('users error delete_restaurant'), $restaurant['first_name'] . " " . $restaurant['last_name']));
                }
            }
            else
            {
                $this->session->set_flashdata('error', lang('users error user_not_exist'));
            }
        }
        else
        {
            $this->session->set_flashdata('error', lang('users error user_id_required'));
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

        if ($this->input->get('username'))
        {
            $filters['username'] = $this->input->get('username', TRUE);
        }

        if ($this->input->get('first_name'))
        {
            $filters['first_name'] = $this->input->get('first_name', TRUE);
        }

        if ($this->input->get('last_name'))
        {
            $filters['last_name'] = $this->input->get('last_name', TRUE);
        }

        // get all users
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
            array_to_csv($restaurants['results'], "users");
        }
        else
        {
            // nothing to export
            $this->session->set_flashdata('error', lang('core error no_results'));
            redirect($this->_redirect_url);
        }

        exit;
    }


    /**************************************************************************************
     * PRIVATE VALIDATION CALLBACK FUNCTIONS
     **************************************************************************************/


    /**
     * Make sure username is available
     *
     * @param  string $restaurantname
     * @param  string|null $current
     * @return int|boolean
     */
    function _check_name($name, $current)
    {
        if (trim($name) != trim($current) && $this->restaurant_model->name_exists($name))
        {
            $this->form_validation->set_message('_check_name', sprintf(lang('users error username_exists'), $name));
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
            $this->form_validation->set_message('_check_email', sprintf(lang('users error email_exists'), $email));
            return FALSE;
        }
        else
        {
            return $email;
        }
    }

}
