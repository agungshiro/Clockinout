<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends Admin_Controller {

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
        $this->lang->load('employee');

        // load the employee model
        $this->load->model('employee_model');
        $this->load->model('restaurants_model');

        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/employee'));
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
        $employee = $this->employee_model->get_all($limit, $offset, $filters, $sort, $dir);

        // build pagination
        $this->pagination->initialize(array(
            'base_url'   => THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}{$filter}",
            'total_rows' => $employee['total'],
            'per_page'   => $limit
        ));

        // setup page header data
		$this
			->add_js_theme('employee.js', TRUE )
			->set_title(lang('employee title employee_list'));

        $data = $this->includes;

        // set content data
        $content_data = array(
            'this_url'   => THIS_URL,
            'employee'      => $employee['results'],
            'total'      => $employee['total'],
            'filters'    => $filters,
            'filter'     => $filter,
            'pagination' => $this->pagination->create_links(),
            'limit'      => $limit,
            'offset'     => $offset,
            'sort'       => $sort,
            'dir'        => $dir
        );

        // load views
        $data['content'] = $this->load->view('admin/employee/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function by_restaurant($id) 
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
        $employee = $this->employee_model->get_all_by_restaurant($id, $limit, $offset, $filters, $sort, $dir);
        $restaurant = $this->restaurants_model->get_restaurant($id);

        // build pagination
        $this->pagination->initialize(array(
            'base_url'   => THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}{$filter}",
            'total_rows' => $employee['total'],
            'per_page'   => $limit
        ));

        // setup page header data
		$this
			->add_js_theme('employee.js', TRUE )
			->set_title($restaurant['name']);

        $data = $this->includes;

        // set content data
        $content_data = array(
            'this_url'   => THIS_URL,
            'employee'      => $employee['results'],
            'total'      => $employee['total'],
            'filters'    => $filters,
            'filter'     => $filter,
            'pagination' => $this->pagination->create_links(),
            'limit'      => $limit,
            'offset'     => $offset,
            'sort'       => $sort,
            'dir'        => $dir
        );

        // load views
        $data['content'] = $this->load->view('admin/employee/list', $content_data, TRUE);
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
        $this->form_validation->set_rules('id_restourant', 'Restourant', 'required|numeric');

        if ($this->form_validation->run() == TRUE)
        {
            // save the new user
            $saved = $this->employee_model->add_employee($this->input->post());

            if ($saved)
            {
                $this->session->set_flashdata('message', sprintf(lang('employee msg add_employee_success'), $this->input->post('first_name') . " " . $this->input->post('last_name')));
            }
            else
            {
                $this->session->set_flashdata('error', sprintf(lang('employee error add_employee_failed'), $this->input->post('first_name') . " " . $this->input->post('last_name')));
            }

            // return to list and display message
            redirect($this->_redirect_url);
        }

        // setup page header data
        $this->set_title(lang('employee title user_add'));

        $data = $this->includes;
        
        $this->load->model('restaurants_model');

        $restaurants = $this->restaurants_model->get_all();

        foreach($restaurants['results'] as $resto) {
            $options[$resto['id']] = $resto['name'];
        }

        // set content data
        $content_data = array(
            'cancel_url'        => $this->_redirect_url,
            'employee'              => NULL,
            'password_required' => TRUE,
            'options' => $options
        );

        // load views
        $data['content'] = $this->load->view('admin/employee/form', $content_data, TRUE);
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
        $employee = $this->employee_model->get_employee($id);

        // if empty results, return to list
        if ( ! $employee)
        {
            redirect($this->_redirect_url);
        }

        // validators
        $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
        $this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[5]|max_length[30]|callback__check_name['.$employee['name'].']');
        $this->form_validation->set_rules('address', 'Address', 'trim|min_length[2]');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|min_length[2]|max_length[13]|numeric');
        $this->form_validation->set_rules('email', 'Email', 'trim|max_length[128]|valid_email|callback__check_email['.$employee['email'].']');
        if ($this->form_validation->run() == TRUE)
        {
            // save the changes
            $saved = $this->employee_model->edit_employee($this->input->post());

            if ($saved)
            {
                $this->session->set_flashdata('message', sprintf(lang('employee msg edit_employee_success'), $this->input->post('first_name') . " " . $this->input->post('last_name')));
            }
            else
            {
                $this->session->set_flashdata('error', sprintf(lang('employee error edit_employee_failed'), $this->input->post('first_name') . " " . $this->input->post('last_name')));
            }

            // return to list and display message
            redirect($this->_redirect_url);
        }

        $this->load->model('restaurants_model');

        $restaurants = $this->restaurants_model->get_all();

        foreach($restaurants['results'] as $resto) {
            $options[$resto['id']] = $resto['name'];
        }

        // setup page header data
        $this->set_title(lang('employee title user_edit'));

        $data = $this->includes;

        // set content data
        $content_data = array(
            'cancel_url'        => $this->_redirect_url,
            'employee'              => $employee,
            'employee_id'           => $id,
            'password_required' => FALSE,
            'options'           => $options
        );

        // load views
        $data['content'] = $this->load->view('admin/employee/form', $content_data, TRUE);
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
            $employee = $this->employee_model->get_employee($id);

            if ($employee)
            {
                // soft-delete the user
                $delete = $this->employee_model->delete_employee($id);

                if ($delete)
                {
                    $this->session->set_flashdata('message', sprintf(lang('employee msg delete_employee'), $employee['name'] ));
                }
                else
                {
                    $this->session->set_flashdata('error', sprintf(lang('employee error delete_employee'), $employee['name']));
                }
            }
            else
            {
                $this->session->set_flashdata('error', lang('employee error employee_not_exist'));
            }
        }
        else
        {
            $this->session->set_flashdata('error', lang('employee error employee_id_required'));
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

        // get all employee
        $employee = $this->employee_model->get_all(0, 0, $filters, $sort, $dir);

        if ($employee['total'] > 0)
        {
            // manipulate the output array
            foreach ($employee['results'] as $key=>$employee)
            {
                unset($employee['results'][$key]['password']);
                unset($employee['results'][$key]['deleted']);

                if ($employee['status'] == 0)
                {
                    $employee['results'][$key]['status'] = lang('admin input inactive');
                }
                else
                {
                    $employee['results'][$key]['status'] = lang('admin input active');
                }
            }

            // export the file
            array_to_csv($employee['results'], "employee");
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
     * Make sure name is available
     *
     * @param  string $name
     * @param  string|null $current
     * @return int|boolean
     */
    function _check_name($name, $current)
    {
        if (trim($name) != trim($current) && $this->employee_model->name_exists($name))
        {
            $this->form_validation->set_message('_check_name', sprintf(lang('employee error name_exists'), $name));
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
        if (trim($email) != trim($current) && $this->employee_model->email_exists($email))
        {
            $this->form_validation->set_message('_check_email', sprintf(lang('employee error email_exists'), $email));
            return FALSE;
        }
        else
        {
            return $email;
        }
    }

}
