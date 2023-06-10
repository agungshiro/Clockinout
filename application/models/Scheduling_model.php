<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Scheduling_model extends CI_Model {

    /**
     * @vars
     */
    private $_db;
    private $_resto_db;
    private $_employee_db;
    private $_day_off_db;


    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        // define primary table
        $this->_db = 'period';
        $this->_resto_db = 'restaurants';
        $this->_employee_db = 'employee';
        $this->_day_off_db = 'day_off';
    }


    /**
     * Get list of non-deleted users
     *
     * @param  int $limit
     * @param  int $offset
     * @param  array $filters
     * @param  string $sort
     * @param  string $dir
     * @return array|boolean
     */
    function get_all($id, $limit=0, $offset=0, $filters=array(), $sort='start_period', $dir='ASC')
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS {$this->_db}.*, {$this->_employee_db}.name,
            IF ((SELECT {$this->_day_off_db}.day FROM {$this->_day_off_db} WHERE {$this->_day_off_db}.id_period = {$this->_db}.id LIMIT 1) , true, false) AS day
            FROM {$this->_db}, {$this->_employee_db}
            WHERE {$this->_db}.deleted = '0' AND {$this->_db}.id_employee = {$id}
            AND {$this->_db}.id_employee = {$this->_employee_db}.id
        ";

        if ( ! empty($filters))
        {
            foreach ($filters as $key=>$value)
            {
                $value = $this->db->escape('%' . $value . '%');
                $sql .= " AND {$key} LIKE {$value}";
            }
        }

        $sql .= " ORDER BY {$sort} {$dir}";

        if ($limit)
        {
            $sql .= " LIMIT {$offset}, {$limit}";
        }

        $query = $this->db->query($sql);

        if ($query->num_rows() > 0)
        {
            $results['results'] = $query->result_array();
        }
        else
        {
            $results['results'] = NULL;
        }

        $sql = "SELECT FOUND_ROWS() AS total";
        $query = $this->db->query($sql);
        $results['total'] = $query->row()->total;

        return $results;
    }


    /**
     * Get specific user
     *
     * @param  int $id
     * @return array|boolean
     */
    function get_period($id=NULL)
    {
        if ($id)
        {
            $sql = "
                SELECT {$this->_db}.*
                FROM {$this->_db}
                WHERE {$this->_db}.id = " . $this->db->escape($id) . "
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }

    function get_day_off($id=NULL) {
        if ($id)
        {
            $sql = "
                SELECT {$this->_day_off_db}.*
                FROM {$this->_day_off_db}
                WHERE {$this->_day_off_db}.id_period = " . $this->db->escape($id) . "
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return true;
            }
        }

        return FALSE;
    }

    function get_day_off_print($id=NULL) {
        if ($id)
        {
            $sql = "
                SELECT {$this->_day_off_db}.*
                FROM {$this->_day_off_db}
                WHERE {$this->_day_off_db}.id_period = " . $this->db->escape($id) . "
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->result_array();
            }
        }

        return FALSE;
    }

    function get_day_off_by_date($date) {
        
    }
    


    /**
     * Add a new user
     *
     * @param  array $data
     * @return mixed|boolean
     */
    function add_period($data=array())
    {
        if ($data)
        {
            $sql = "
                INSERT INTO {$this->_db} (
                    id_employee,
                    start_period,
                    end_period,
                    total_hours
                ) VALUES (
                    " . $this->db->escape($data['id_employee']) . ",
                    " . $this->db->escape($data['start_period']) . ",
                    " . $this->db->escape($data['end_period']) . ",
                    " . $this->db->escape($data['total_hours']) . "
                )
            ";

            $this->db->query($sql);

            if ($id = $this->db->insert_id())
            {
                return $id;
            }
        }

        return FALSE;
    }

    function add_day_off($datax) {
        foreach($datax as $data) {
            $sql = "
                INSERT INTO {$this->_day_off_db} (
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

    /**
     * Edit an existing user
     *
     * @param  array $data
     * @return boolean
     */
    /*
    function edit_period($data=array())
    {
        if ($data)
        {
            $sql = "
                UPDATE {$this->_db}
                SET
                    name = " . $this->db->escape($data['name']) . ",
            ";

            $sql .= "
                    address = " . $this->db->escape($data['address']) . ",
                    phone = " . $this->db->escape($data['phone']) . ",
                    email = " . $this->db->escape($data['email']) . ",
                    open_hour = " . $this->db->escape($data['open_hour']) . ",
                    close_hour = " . $this->db->escape($data['close_hour']) . "'
                WHERE id = " . $this->db->escape($data['id']) . "
                    AND deleted = '0'
            ";

            $this->db->query($sql);

            if ($this->db->affected_rows())
            {
                return TRUE;
            }
        }

        return FALSE;
    }
    */


    /**
     * Soft delete an existing restaurant
     *
     * @param  int $id
     * @return boolean
     */
    function delete_period($id=NULL)
    {
        if ($id)
        {
            $sql = "
                DELETE FROM {$this->_db} 
                WHERE id = " . $this->db->escape($id) . "
            ";

            $this->db->query($sql);

            if ($this->db->affected_rows())
            {
                return TRUE;
            }
        }

        return FALSE;
    }


    /**
     * deacticate an existing restaurant
     *
     * @param  int $id
     * @return boolean
     */
    function deactivate_period($id=NULL)
    {
        if ($id)
        {
            $sql = "
                UPDATE {$this->_db}
                SET
                    is_active = '0'

                WHERE id = " . $this->db->escape($id) . "
            ";

            $this->db->query($sql);

            if ($this->db->affected_rows())
            {
                return TRUE;
            }
        }

        return FALSE;
    }


    /**
     * Check to see if a username already exists
     *
     * @param  string $username
     * @return boolean
     */
    function name_exists($name)
    {
        $sql = "
            SELECT id
            FROM {$this->_db}
            WHERE name = " . $this->db->escape($name) . "
            LIMIT 1
        ";

        $query = $this->db->query($sql);

        if ($query->num_rows())
        {
            return TRUE;
        }

        return FALSE;
    }

    function id_exists($id)
    {
        $sql = "
            SELECT id
            FROM {$this->_employee_db}
            WHERE id = " . $this->db->escape($id) . "
            LIMIT 1
        ";

        $query = $this->db->query($sql);

        if ($query->num_rows())
        {
            return TRUE;
        }

        return FALSE;
    }


    /**
     * Check to see if an email already exists
     *
     * @param  string $email
     * @return boolean
     */
    function email_exists($email)
    {
        $sql = "
            SELECT id
            FROM {$this->_db}
            WHERE email = " . $this->db->escape($email) . "
            LIMIT 1
        ";

        $query = $this->db->query($sql);

        if ($query->num_rows())
        {
            return TRUE;
        }

        return FALSE;
    }

    function period_exists($id)
    {
        $sql = "
            SELECT id
            FROM {$this->_db}
            WHERE id = " . $this->db->escape($id) . "
            LIMIT 1
        ";

        $query = $this->db->query($sql);

        if ($query->num_rows())
        {
            return TRUE;
        }

        return FALSE;
    }

}
