<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Joblist_model extends CI_Model {

    /**
     * @vars
     */
    private $_db;


    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        // define primary table
        $this->_db = 'typeofjobs';
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
    function get_all($id,$limit=0, $offset=0, $filters=array(), $sort='name', $dir='ASC')
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM {$this->_db} 
            WHERE id_restaurant = ".$this->db->escape($id)."
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
    function get($id=NULL)
    {
        if ($id)
        {
            $sql = "
                SELECT *
                FROM {$this->_db}
                WHERE id = " . $this->db->escape($id) . "
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }

    /**
     * Add a new user
     *
     * @param  array $data
     * @return mixed|boolean
     */
    function add($data=array())
    {
        if ($data)
        {
            $sql = "
                INSERT INTO {$this->_db} (
                    name,
                    time_limit,
                    shift_limit,
                    id_restaurant
                ) VALUES (
                    " . $this->db->escape($data['name']) . ",
                    " . $this->db->escape($data['time_limit']) . ",
                    " . $this->db->escape($data['shift_limit']) . ",
                    " . $this->db->escape($data['id_restaurant']) . "
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

    /**
     * Edit an existing user
     *
     * @param  array $data
     * @return boolean
     */
    function edit($data=array())
    {
        if ($data)
        {
            $sql = "
                UPDATE {$this->_db}
                SET
                    name = " . $this->db->escape($data['name']) . ",
            ";

            $sql .= "
                    time_limit = " . $this->db->escape($data['time_limit']) . ",
                    shift_limit = " . $this->db->escape($data['shift_limit']) . "
                WHERE id = " . $this->db->escape($data['id']) . "
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
     * Soft delete an existing restaurant
     *
     * @param  int $id
     * @return boolean
     */
    function delete($id=NULL)
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

    function job_exists($name)
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
}