<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_model extends CI_Model {

    /**
     * @vars
     */
    private $_db;
    private $_resto_db;


    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        // define primary table
        $this->_db = 'employee';
        $this->_resto_db = 'restaurants';
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
    function get_all($limit=0, $offset=0, $filters=array(), $sort='last_name', $dir='ASC')
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS {$this->_db}.*, {$this->_resto_db}.name AS restaurant
            FROM {$this->_db}, {$this->_resto_db}
            WHERE {$this->_db}.deleted = '0' AND {$this->_db}.id_restourant = {$this->_resto_db}.id
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

    function get_all_by_restaurant($id, $limit=0, $offset=0, $filters=array(), $sort='name', $dir='ASC') 
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS {$this->_db}.*, {$this->_resto_db}.name AS restaurant
            FROM {$this->_db}, {$this->_resto_db}
            WHERE {$this->_db}.deleted = '0' AND {$this->_db}.id_restourant = {$this->_resto_db}.id
            AND {$this->_db}.id_restourant = {$id}
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
    function get_employee($id=NULL)
    {
        if ($id)
        {
            $sql = "
                SELECT {$this->_db}.*, {$this->_resto_db}.name AS restaurant
                FROM {$this->_db}, {$this->_resto_db}
                WHERE {$this->_db}.id = " . $this->db->escape($id) . " AND {$this->_resto_db}.id = {$this->_db}.id_restourant
                    AND {$this->_db}.deleted = '0'
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
    function add_employee($data=array())
    {
        if ($data)
        {
            $sql = "
                INSERT INTO {$this->_db} (
                    id_restourant,
                    name,
                    address,
                    phone,
                    email
                ) VALUES (
                    " . $this->db->escape($data['id_restourant']) . ",
                    " . $this->db->escape($data['name']) . ",
                    " . $this->db->escape($data['address']) . ",
                    " . $this->db->escape($data['phone']) . ",
                    " . $this->db->escape($data['email']) . "
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
    function edit_employee($data=array())
    {
        if ($data)
        {
            $sql = "
                UPDATE {$this->_db}
                SET
                    name = " . $this->db->escape($data['name']) . ",
            ";

            $sql .= "
                    id_restourant = " . $this->db->escape($data['id_restourant']) . ",
                    address = " . $this->db->escape($data['address']) . ",
                    phone = " . $this->db->escape($data['phone']) . ",
                    email = " . $this->db->escape($data['email']) . "
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


    /**
     * Soft delete an existing restaurant
     *
     * @param  int $id
     * @return boolean
     */
    function delete_employee($id=NULL)
    {
        if ($id)
        {
            $sql = "
                UPDATE {$this->_db}
                SET
                    deleted = '1'

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
    function deactivate_employee($id=NULL)
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

}
