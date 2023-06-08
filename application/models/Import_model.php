<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Import_model extends CI_Model {

    /**
     * @vars
     */
    private $_e_db;
    private $_r_db;
    private $_p_db;
    private $_d_db;
    private $_t_db;


    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        $this->_e_db = 'employee';
        $this->_r_db = 'restaurants';
        $this->_p_db = 'period';
        $this->_d_db = 'day_off';
        $this->_t_db = 'typeofjobs';
    }

    /**
     * Get type of job Id for dropdown
     */
    function get_tob_id($keyword){
        
        $this->db->select('id');
        $this->db->like('name', $keyword);
        $this->db->from($this->_t_db);
        $this->db->limit(1);
        if ($result = $this->db->get()) {
            return $result->result_array();
        }

        return false;
    }

    /**
     * Check name if exist return ID
     */
    function check_name() {
        $this->db->select('id');
        $this->db->like('name', $keyword);
        $this->db->from($this->_e_db);
        $this->db->limit(1);
        if ($id = $this->db->get()) {
            $res = $id->result_array();
            return $res[0]['id'];
        }

        return false;
    }

    function get_tob_list($id_resto) {
        if ($result = $this->db->get_where($this->_t_db, array('id_restaurant'=> $id_resto))){
            return $result;
        }

        return false;
    }

    function create_employee($data){
        
        $this->db->insert($this->_e_db,$data);
        return $this->db->insert_id();
    }

    function insert_($data) {
        $e_id = $this->check_name($data['name']);

        if($e_id == false) {

            $insert_data = array(
                'name' => $data['name'],
                'id_restourant' => $data['id_restourant'],
                'jobtype' => $data['id_tob']
            );

            $new_id = $this->create_employee($insert_data);
        }
    }

    function insert($data) {
        $ids = array();
        if ($data)
        {
            $sql = "
                INSERT INTO {$this->_e_db} (
                    name,
                    id_restourant,
                    jobtype
                ) VALUES (
                    " . $this->db->escape($data['name']) . ",
                    " . $this->db->escape($data['id_restourant']) . ",
                    " . $this->db->escape($data['id_tob']) . "
                )
            ";

            $this->db->query($sql);

            $ids['id_employee'] = $this->db->insert_id();

            $sql2 = "
                INSERT INTO {$this->_p_db} (
                    id_employee,
                    start_period,
                    end_period,
                    regular_hour,
                    overtime,
                    total_hours
                ) VALUES (
                    " . $this->db->escape($ids['id_employee']) . ",
                    " . $this->db->escape($data['start_period']) . ",
                    " . $this->db->escape($data['end_period']) . ",
                    " . $this->db->escape($data['regular_hour']) . ",
                    " . $this->db->escape($data['overtime']) . ",
                    " . $this->db->escape($data['total_hours']) . "
                )
            ";

            $this->db->query($sql2);

            if ($period_id = $this->db->insert_id())
            {
                $ids['id_period'] = $period_id;
                return $ids;
            }
        }
    }

    function insert_dayoff($data) {
        $sql = "
                INSERT INTO {$this->_d_db} (
                    id_employee,
                    id_period,
                    day,
                    type,
                    duration
                ) VALUES (
                    " . $this->db->escape($data['id_employee']) . ",
                    " . $this->db->escape($data['id_period']) . ",
                    " . $this->db->escape($data['day']) . ",
                    " . $this->db->escape($data['type']) . ",
                    " . $this->db->escape($data['duration']) . "
                )
            ";

            $this->db->query($sql);
    }

}