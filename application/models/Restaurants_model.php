<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Restaurants_model extends CI_Model {

    /**
     * @vars
     */
    private $_db;
    private $_tob_db;


    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        // define primary table
        $this->_db = 'restaurants';
        $this->_tob_db = 'typeofjobs';
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
    function get_all($limit=0, $offset=0, $filters=array(), $sort='name', $dir='ASC')
    {
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM {$this->_db}
            WHERE deleted = '0'
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
    function get_restaurant($id=NULL)
    {
        if ($id)
        {
            $sql = "
                SELECT *
                FROM {$this->_db}
                WHERE id = " . $this->db->escape($id) . "
                    AND deleted = '0'
            ";

            $query = $this->db->query($sql);

            if ($query->num_rows())
            {
                return $query->row_array();
            }
        }

        return FALSE;
    }

    function get_tob($id=NULL)
    {
        if ($id)
        {
            $sql = "
                SELECT *
                FROM {$this->_tob_db}
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
    function add_restaurant($data=array())
    {
        if ($data)
        {
            $sql = "
                INSERT INTO {$this->_db} (
                    name,
                    address,
                    phone,
                    email,
                    open_hour,
                    shift_hour,
                    close_hour
                ) VALUES (
                    " . $this->db->escape($data['name']) . ",
                    " . $this->db->escape($data['address']) . ",
                    " . $this->db->escape($data['phone']) . ",
                    " . $this->db->escape($data['email']) . ",
                    " . $this->db->escape($data['open_hour']) . ",
                    " . $this->db->escape($data['shift_hour']) . ",
                    " . $this->db->escape($data['close_hour']) . "
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
    function edit_restaurant($data=array())
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
                    shift_hour = " . $this->db->escape($data['shift_hour']) . ",
                    close_hour = " . $this->db->escape($data['close_hour']) . "
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
    function delete_restaurant($id=NULL)
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
    function deactivate_restaurant($id=NULL)
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
