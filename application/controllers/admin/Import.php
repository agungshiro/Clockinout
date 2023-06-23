<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Import  extends Admin_Controller {

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
        $this->lang->load('import');
        $this->lang->load('scheduling');

        // load the restaurants model
        $this->load->model('restaurants_model');
        $this->load->model('employee_model');
        $this->load->model('scheduling_model');
        $this->load->model('import_model');
        $this->load->helper('file');

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
        $restaurants = $this->restaurants_model->get_all();
        foreach($restaurants['results'] as $resto) {
            $options[$resto['id']] = $resto['name'];
        }

        // setup page header data
		$this
			->add_js_theme('import.js', TRUE )
			->set_title('Import CSV');

        $data = $this->includes;

        // set content data
        $content_data = array(
            'cancel_url' => $this->_redirect_url,
            'this_url'   => THIS_URL,
            'options' => $options
        );

        // load views
        $data['content'] = $this->load->view('admin/import/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }


    function upload () {
        $data = array();
        $memData = array();

        $id_resto = $this->input->post('id_restourant');

        $resto = $this->restaurants_model->get_restaurant($id_resto);

        // Set data for typeofjobs drop down
        $tob = $this->import_model->get_tob_list($id_resto);
        foreach($tob->result_array() as $t) {
            $tob_options[$t['id']] = $t['name'];
        } 
        
        // If import request is submitted
        
        // Form field validation rules
        $this->form_validation->set_rules('csv_file', 'CSV file', 'callback_file_check');
        
        // Validate submitted form data
        if($this->form_validation->run() == true){
            //$insertCount = $updateCount = $rowCount = $notAddCount = 0;
            
            // If file uploaded
            if(is_uploaded_file($_FILES['csv_file']['tmp_name'])){

                $extention  = explode('.', $_FILES['csv_file']['name']);
                $file = $_FILES['csv_file']['tmp_name'];
                
                if (strtolower(end($extention)) === 'csv' && $_FILES["csv_file"]["size"] > 0) {

                    $list = [];
					$i = 0;
					$handle = fopen($file, "r");
					while (($col = fgetcsv($handle, 2048))) {
						$i++;
						if ($i == 1) continue;

						// Data yang akan disimpan ke dalam databse
						$data = [
							'name' => $col[0],
							'type' => $col[1],
							'regular_hour' => $col[2],
							'overtime' => $col[3],
                            'id_tob'=> $this->import_model->get_tob_id($col[1])
						];
                        array_push($list,$data);
						// Simpan data ke database.
					    //	$this->pelanggan->save($data);
					}

                    

					fclose($handle);

				} else {
					echo 'Format file tidak valid!';
				}


            }else{
                print('not uploaded');
            }
        }else{
            print('not valid');
        }
        //redirect('members');

        $this
            ->add_css_theme('bootstrap-datepicker.css')
			->add_js_theme('bootstrap-datepicker.js')
			->set_title('Review imported data for restaurant '.$resto['name']);

        $data = $this->includes;

        // set content data
        $content_data = array(
            'cancel_url' => $this->_redirect_url,
            'this_url'   => THIS_URL,
            'list' => $list,
            'tob_options' => $tob_options,
            'id_restourant' => $id_resto
        );

        $data['content'] = $this->load->view('admin/import/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function save() {
        //$len = count($this->input->post('name'));
        //print_r($len);

        if($this->input->post('start_period') == '' || $this->input->post('end_period') == '' || 
        $this->input->post('start_period') > $this->input->post('end_period')) {
            $this->session->set_flashdata('error', 'Please set a valid start and end of periode date');
            
            //redirect($this->_redirect_url);
            redirect(base_url('admin/import/'));
        };

        $names = $this->input->post('name');
        $id_tob = $this->input->post('id_tob');
        $regular_hours = $this->input->post('regular_hour');
        $overtimes = $this->input->post('overtime');

        //$len = count($names);

        $len = 30;
       // foreach ($names as $name) {
        for($i = 0; $i < $len; $i++){
            $data = array (
                'id_restourant' => $this->input->post('id_restourant'),
                'start_period' => $this->input->post('start_period'),
                'end_period' => $this->input->post('end_period'),
                'name' => $names[$i],
                'id_tob' => $id_tob[$i],
                'regular_hour' => (float)$regular_hours[$i],
                'overtime' => (float)$overtimes[$i],
                'total_hours' => (float)($regular_hours[$i] + $overtimes[$i])
            );

            $ids = $this->import_model->insert($data);

            $data['id_employee'] = $ids['id_employee'];
            $data['id_period'] = $ids['id_period'];

            // Randomize schedule
            $this->randomize($data);
        }
        
        redirect(base_url('admin/employee/by_restaurant/'.$this->input->post('id_restourant')));
        
    }

    /**
     * $param[id_period,id_resto,start, end, id_tob]
     */
    function randomize($param) {

        // Get Restaurant 
        $restaurant = $this->restaurants_model->get_restaurant($param['id_restourant']);
        // Calculate Per Shift time & Max time
        $open = strtotime($restaurant['open_hour']);
        $close = strtotime($restaurant['close_hour']);
        $shift = strtotime($restaurant['shift_hour']);
                     
        // Everything in second           
        $max = $close - $open;
        $shift1 = $shift - $open;
        $shift2 = $close - $shift;
        $max_week = $max * 7;
        $shift_rate_duration = floor($max/2);

        // get limit of the job
        $tob = $this->restaurants_model->get_tob($param['id_tob']);
        $time_limit = $tob['time_limit'];
        $shift_limit = $tob['shift_limit'];
        $numb_shift_max = $shift_limit / 0.5;
        $numb_full_day = $numb_shift_max % 7;
        $numb_reg_day = 7 - $numb_full_day;

        $hours = $param['total_hours'];
        if($hours > $time_limit) {
            $hours = $time_limit;
        }

        $hours_in_second = $hours * 3600;

        $a1 = $a2 = $a3 = array();

        if(floor($hours_in_second/($shift_rate_duration*14)) < 1) {
            $in = floor($hours_in_second/$shift_rate_duration);
            $off = 7 - $in;
            $a1 = array_fill(0,$off,0);
            $a2 = array_fill(0,$in,1);
            $a3 = array_merge($a1,$a2);
            shuffle($a3);
        } else {
            $in = floor($hours_in_second/$shift_rate_duration);
            $full = $in%7;
            $half = 7 - $full;
            $a1 = array_fill(0,$full,2);
            $a2 = array_fill(0,$half,1);
            $a3 = array_merge($a1,$a2);
            shuffle($a3);
        }


        $begin = new DateTime($param['start_period'].' 00:00:00');
        $end = new DateTime($param['end_period'].' 00:00:00');
        $end = $end->modify('+1 day');
        $period = new DatePeriod (
            $begin,
            new DateInterval('P1D'),
            $end
        );

        // Brake Periode Range into several date
        foreach($period as $key=>$val){
            $p[] = $val->format('Y-m-d');
        }


        for ($x = 0; $x < count($a3); $x++) {
            $inser_data['id_employee'] = $param['id_employee'];
            $inser_data['id_period'] = $param['id_period'];
            $inser_data['day'] = $p[$x];
            
            $reg = rand(1,2);
            if($reg == 1) {
                $d = $shift1;
                $t = '1st_shift';
            } else {
                $d = $shift2;
                $t = '2nd_shift';
            }

            switch($a3[$x]) {
                case 0:
                    $inser_data['type'] = 'off';
                    $inser_data['duration'] = 0;
                    break;
                
                case 1:
                    $inser_data['type'] = $t;
                    $inser_data['duration'] = $d;
                    break;

                case 2:
                    $inser_data['type'] = 'full_day' ;
                    $inser_data['duration'] = $max;
                    break;
            }

            //print_r($in);
            //print_r($a3);
            //exit;

            //print_r($inser_data);

            $this->import_model->insert_dayoff($inser_data);
        }
        // Get restaurant to get start , shift and close hour
        // Use period ID to get employee ID
        
    }



    /**
     * not use
     */
    public function file_check($str){
        $allowed_mime_types = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
        if(isset($_FILES['csv_file']['name']) && $_FILES['csv_file']['name'] != ""){
            $mime = get_mime_by_extension($_FILES['csv_file']['name']);
            $fileAr = explode('.', $_FILES['csv_file']['name']);
            $ext = end($fileAr);
            if(($ext == 'csv') && in_array($mime, $allowed_mime_types)){
                return true;
            }else{
                $this->form_validation->set_message('file_check', 'Please select only CSV file to upload.');
                return false;
            }
        }else{
            $this->form_validation->set_message('file_check', 'Please select a CSV file to upload.');
            return false;
        }
    }

}
