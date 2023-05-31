<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Scheduling extends Admin_Controller {

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
        $this->lang->load('scheduling');

        // load the scheduling model
        $this->load->model('scheduling_model');
        $this->load->model('employee_model');
        $this->load->model('restaurants_model');


        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/scheduling'));
        define('BOUNCE_BACK_URL', base_url('admin/employee'));
        define('DEFAULT_LIMIT', $this->settings->per_page_limit);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "start_period");
        define('DEFAULT_DIR', "asc");

        // use the url in session (if available) to return to the previous filter/sorted/paginated list
        if ($this->session->userdata(REFERRER))
        {
            $this->_redirect_url = $this->session->userdata(REFERRER);
        }
        else
        {
            $this->_redirect_url = BOUNCE_BACK_URL;
        }
    }


    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    function index()
    {
        redirect($this->_redirect_url);
    }
    

    /**
     * User list page
     */
    function by_employee($id = NULL)
    {
        // make sure we have a numeric id
        if (is_null($id) OR ! is_numeric($id))
        {
            redirect($this->_redirect_url);
        }

        // get parameters
        $limit  = $this->input->get('limit')  ? $this->input->get('limit', TRUE)  : DEFAULT_LIMIT;
        $offset = $this->input->get('offset') ? $this->input->get('offset', TRUE) : DEFAULT_OFFSET;
        $sort   = $this->input->get('sort')   ? $this->input->get('sort', TRUE)   : DEFAULT_SORT;
        $dir    = $this->input->get('dir')    ? $this->input->get('dir', TRUE)    : DEFAULT_DIR;

        // get filters
        $filters = array();

        if ($this->input->get('start_period'))
        {
            $filters['start_period'] = $this->input->get('start_period', TRUE);
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

                if ($this->input->post('start_period'))
                {
                    $filter .= "&start_period=" . $this->input->post('start_period', TRUE);
                }

                // redirect using new filter(s)
                redirect(THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}&offset={$offset}{$filter}");
            }
        }

        // get list
        $scheduling = $this->scheduling_model->get_all($id,$limit, $offset, $filters, $sort, $dir);
        $dayoff= $this->scheduling_model->get_day_off($id);

        // build pagination
        $this->pagination->initialize(array(
            'base_url'   => THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}{$filter}",
            'total_rows' => $scheduling['total'],
            'per_page'   => $limit
        ));

        // setup page header data
		$this
			->add_js_theme('scheduling.js', TRUE )
			->set_title(lang('scheduling title period_list'));

        $data = $this->includes;

        // Get Employee information 
        $employee = $this->employee_model->get_employee($id);

        // set content data
        $content_data = array(
            'this_url'   => THIS_URL,
            'scheduling' => $scheduling['results'],
            'employee'   => $employee,
            'total'      => $scheduling['total'],
            'filters'    => $filters,
            'filter'     => $filter,
            'pagination' => $this->pagination->create_links(),
            'limit'      => $limit,
            'offset'     => $offset,
            'sort'       => $sort,
            'dir'        => $dir,
            'day_off'    => $dayoff
        );

        // load views
        $data['content'] = $this->load->view('admin/scheduling/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }


    /**
     * Add new user
     */
    function add($id)
    {
        if($this->scheduling_model->id_exists($id) == FALSE){
            redirect($this->_redirect_url);
        }

        // validators
        $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
        $this->form_validation->set_rules('start_period', 'Start Period', 'required');
        $this->form_validation->set_rules('end_period', 'End Period', 'required');
        $this->form_validation->set_rules('total_hours', 'Total Hours', 'required|numeric');


        if ($this->form_validation->run() == TRUE)
        {
            // save the new user
            $saved = $this->scheduling_model->add_period($this->input->post());

            if ($saved)
            {
                $this->session->set_flashdata('message', sprintf(lang('scheduling msg add_scheduling_success')));
            }
            else
            {
                $this->session->set_flashdata('error', sprintf(lang('scheduling error add_scheduling_failed')));
            }

            // return to list and display message
            redirect(base_url('admin/scheduling/by_employee/'.$id));
        }

        // setup page header data
        $this->set_title('Add period');

        $this
			->add_css_theme('bootstrap-datepicker.css')
			->add_js_theme('bootstrap-datepicker.js');

        $data = $this->includes;

        

        // set content data
        $content_data = array(
            'cancel_url'        => $this->_redirect_url,
            'scheduling'        => NULL,
            'password_required' => TRUE,
            'id_employee'       => $id
        );

        // load views
        $data['content'] = $this->load->view('admin/scheduling/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }


    function add_day_off($id) 
    {
        
        // Cecking ID to be valid
        if($this->scheduling_model->period_exists($id) == FALSE){
            redirect($this->_redirect_url);
        }

        

        // Get period range
        $periods = $this->scheduling_model->get_period($id);

        if($post = $this->input->post()) {
            $this->cek_input_post($post,$id,$periods['total_hours']);
        }

        $period = new DatePeriod (
            new DateTime($periods['start_period'].' 00:00:00'),
            new DateInterval('P1D'),
            new DateTime($periods['end_period'].' 00:00:00')
        );

        // Brake Periode Range into several date
        foreach($period as $key=>$val){
            $p[] = $val->format('Y-m-d');
        }

        // setup page header data
		$this
            ->add_js_theme('scheduling_i18n.js', TRUE )
            ->set_title(lang('scheduling title dayoff_list'));

        $data = $this->includes;

        // put them into array
        // Working with the checkbox
        $content_data = array(
            'cancel_url'        => $this->_redirect_url,
            'days'  => $p,
            'start_period' => $periods['start_period'],
            'end_period' => $periods['end_period'],
            'id_period' => $id,
            'id_employee' => $periods['id_employee']
        );

        // load views
        $data['content'] = $this->load->view('admin/scheduling/form_day_off', $content_data, TRUE);
        $this->load->view($this->template, $data);

    }

    function cek_input_post($post,$id,$total_hours) {

        // Get Employee for Restaurant ID 
        $employee = $this->employee_model->get_employee($post['id_employee']);
        // Get Restaurant 
        $restaurant = $this->restaurants_model->get_restaurant($employee['id_restourant']);
        // Calculate Per Shift time & Max time
        $open = strtotime($restaurant['open_hour']);
        $close = strtotime($restaurant['close_hour']);
        $shift = strtotime($restaurant['shift_hour']);
                     
        // Everything in second           
        $max = $close - $open;
        $shift1 = $shift - $open;
        $shift2 = $close - $shift;
        $max_week = $max * 7;
        
        $id_r = $employee['id_restaurant'];
        $id_e = $post['id_employee'];

        print_r($max);
        $insert_data = [];
        $i=0;
        $total = 0;
        
        foreach($post as $k=>$v) {
            // Get shift data
            if($k !== 'id_period' && $k !== 'id_employee' && $k !== 'submit') {
                if($v == 'full_day') {$total += $max; $d = $max;}
                elseif($v == '1st_shift') {$total += $shift1; $d = $shift1;}
                elseif($v == '2nd_shift') {$total += $shift2; $d = $shift2;}
                else{$total += 0; $d = 0;}

                $insert_data[$i]['id_employee'] = $id_e;
                $insert_data[$i]['id_period'] = $id;
                $insert_data[$i]['day'] = $k;
                $insert_data[$i]['type'] = $v;
                $insert_data[$i]['duration'] = $d;
                $i++;
            }
        
        }

        if($total > ($total_hours * 3600)){
            $this->session->set_flashdata('error', 'Time Exeeded '.$total.' from '.$total_hours*3600);
            return false;
        } else {
            $this->session->set_flashdata('message', 'Ok, the schedule had been made. It\'s time to make a report.');

            $this->scheduling_model->add_day_off($insert_data);

            redirect(base_url('admin/scheduling/by_employee/'.$id_e));
            //return true;

        }
        // Check if exeeded or not
        // If yes throw error message
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
        $scheduling = $this->scheduling_model->get_scheduling($id);

        // if empty results, return to list
        if ( ! $scheduling)
        {
            redirect($this->_redirect_url);
        }

        // validators
        $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
        $this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[5]|max_length[30]|callback__check_name[]');
        $this->form_validation->set_rules('address', 'Address', 'trim|min_length[2]');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|min_length[2]|max_length[13]|numeric');
        $this->form_validation->set_rules('email', 'Email', 'trim|max_length[128]|valid_email|callback__check_email[]');
        if ($this->form_validation->run() == TRUE)
        {
            // save the changes
            $saved = $this->scheduling_model->edit_scheduling($this->input->post());

            if ($saved)
            {
                $this->session->set_flashdata('message', sprintf(lang('scheduling msg edit_scheduling_success'), $this->input->post('first_name') . " " . $this->input->post('last_name')));
            }
            else
            {
                $this->session->set_flashdata('error', sprintf(lang('scheduling error edit_scheduling_failed'), $this->input->post('first_name') . " " . $this->input->post('last_name')));
            }

            // return to list and display message
            redirect($this->_redirect_url);
        }

        // setup page header data
        $this->set_title(lang('scheduling title user_edit'));

        $data = $this->includes;

        // set content data
        $content_data = array(
            'cancel_url'        => $this->_redirect_url,
            'scheduling'              => $scheduling,
            'user_id'           => $id,
            'password_required' => FALSE
        );

        // load views
        $data['content'] = $this->load->view('admin/scheduling/form', $content_data, TRUE);
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
            $scheduling = $this->scheduling_model->get_period($id);

            if ($scheduling)
            {
                // soft-delete the user
                $delete = $this->scheduling_model->delete_period($id);

                if ($delete)
                {
                    $this->session->set_flashdata('message', sprintf(lang('scheduling msg delete_scheduling')));
                }
                else
                {
                    $this->session->set_flashdata('error', sprintf(lang('scheduling error delete_scheduling')));
                }
            }
            else
            {
                $this->session->set_flashdata('error', lang('scheduling error scheduling_not_exist'));
            }
        }
        else
        {
            $this->session->set_flashdata('error', lang('scheduling error user_id_required'));
        }

        // return to list and display message
        redirect(base_url('admin/scheduling/by_employee/'.$scheduling['id_employee']));
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

        // get all scheduling
        $scheduling = $this->scheduling_model->get_all(0, 0, $filters, $sort, $dir);

        if ($scheduling['total'] > 0)
        {
            // manipulate the output array
            foreach ($scheduling['results'] as $key=>$scheduling)
            {
                unset($scheduling['results'][$key]['password']);
                unset($scheduling['results'][$key]['deleted']);

                if ($scheduling['status'] == 0)
                {
                    $scheduling['results'][$key]['status'] = lang('admin input inactive');
                }
                else
                {
                    $scheduling['results'][$key]['status'] = lang('admin input active');
                }
            }

            // export the file
            array_to_csv($scheduling['results'], "scheduling");
        }
        else
        {
            // nothing to export
            $this->session->set_flashdata('error', lang('core error no_results'));
            redirect($this->_redirect_url);
        }

        exit;
    }

    function print($id=NULL) {

        $period = $this->scheduling_model->get_period($id);
        $dayoffs = $this->scheduling_model->get_day_off_print($id);
        $employee = $this->employee_model->get_employee($period['id_employee']);
        $restaurant = $this->restaurants_model->get_restaurant($employee['id_restourant']);

        $open = strtotime($restaurant['open_hour']);
        $close = strtotime($restaurant['close_hour']);
        $shift = strtotime($restaurant['shift_hour']);
        $total_seconds = $period['total_hours']*3600;
                     
        // Everything in second           
        $max = $close - $open;
        $shift1 = $shift - $open;
        $shift2 = $close - $shift;
        $max_week = $max * 7;

        $mod = NULL;
        $numb_days = count($dayoffs);
        $total = 0;

        // Total counting
        foreach($dayoffs as $key => $val) {
            foreach($val as $k=>$v){
                if($v == 'full_day') {$total += $max;}
                elseif($v == '1st_shift') {$total += $shift1;}
                elseif($v == '2nd_shift') {$total += $shift2;}
                else{$total += 0;}
            }
        }

        // Find diff
        $diff = $total_seconds - $total; 
        $diff_rate = floor($diff/($numb_days*2));

        $i = 0;
        foreach($dayoffs as $dof) {
            $mod[$i] = $dof;

            // Clock in
            if($dof['type'] == 'full_day' || $dof['type'] == '1st_shift') {
                $clockin = strtotime($restaurant['open_hour']) - $diff_rate;
            } elseif ($dof['type'] == '2nd_shift') {
                $clockin = strtotime($restaurant['shift_hour']) - $diff_rate;
            } else {
                $clockin = 0;
            }
            
            // Clock out
            if($dof['type'] == 'full_day' || $dof['type'] == '2nd_shift') {
                $clockout = strtotime($restaurant['close_hour']) + $diff_rate;
            } elseif ($dof['type'] == '1st_shift') {
                $clockout = strtotime($restaurant['shift_hour']) + $diff_rate;
            } else {
                $clockout = 0;
            }

            $duration = $clockout - $clockin;
            $h = floor($duration/3600);
            $min = floor(($duration%3600)/60);

            $mod[$i]['clockin'] = date('H:i:s',$clockin);
            $mod[$i]['clockout'] = date('H:i:s',$clockout);
            $mod[$i]['duration'] = $h.' hrs : '.$min.' min';
            $i++;
        }

        $content_data = array(
            'resto_name' => $restaurant['name'],
            'resto_address' => $restaurant['address'],
            'resto_phone' => $restaurant['phone'],
            'resto_email' => $restaurant['email'],
            'open_hour' => $restaurant['open_hour'],
            'close_hour' => $restaurant['close_hour'],
            'shift_hour' => $restaurant['shift_hour'],
            'name' => $employee['name'],
            'dayoffs' =>$mod
        );


        $this->load->view('admin/scheduling/printer_friendly',$content_data);
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
        if (trim($name) != trim($current) && $this->scheduling_model->name_exists($name))
        {
            $this->form_validation->set_message('_check_name', sprintf(lang('scheduling error name_exists'), $name));
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
        if (trim($email) != trim($current) && $this->scheduling_model->email_exists($email))
        {
            $this->form_validation->set_message('_check_email', sprintf(lang('scheduling error email_exists'), $email));
            return FALSE;
        }
        else
        {
            return $email;
        }
    }

}
