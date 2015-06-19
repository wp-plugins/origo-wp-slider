<?php
/*
 * This is a class to handle records, user forms, and database
 */
class Entity {
    private $_db_table;
    
    public function __construct($db_table) 
    {
        $this->_db_table = $db_table;
    }
}

class Entity_Slide extends Entity {
    public function __construct($db_table)
    {
        parent::__construct($db_table);
    }
}

class DatabaseTable {
    protected $_table_name;
    protected $_array_fields;
    protected $_debug;
    protected $_sort_order_field;

    public function setSortOrderField($field)
    {        
        $this->_sort_order_field = $field;
    }
    
    public function __construct($table_name, $args = array()) {
        $this->_table_name = $table_name;       
    }

    public function save($user_input)
    {
        global $wpdb;
        $final_input = array();

        foreach ($this->_array_fields as $db_field => $values) {
            $idx = str_replace('_', '-', $db_field);
            $final_input[$db_field] = $user_input[$idx];
        }

        unset($final_input['ID']);

        if (!$user_input['ID']) {
            $wpdb->insert(
                $this->_table_name, 
                $final_input
            );                        
        }
        else {
            foreach($final_input as $field => $value){
                $arr_set []= $field . ' = "' . addslashes($value) . '"';
            }          
            $query = $query = 'UPDATE ' . $this->_table_name . ' set ' . implode(', ', $arr_set) . 
                ' WHERE ID = "' . $user_input['ID'] . '"';
            if ($user_input['guid']) {
                $query .= ' OR guid = "' . $user_input['guid'] . '"';
            }

            $wpdb->query($query);
        }

        return true;
    }
    
    public function getAll()
    {
        global $wpdb;
        if ($this->_sort_order_field) {
            $query = 'SELECT * FROM ' . $this->_table_name . ' WHERE sort_order status = "1" ORDER BY ' . $this->_sort_order_field . ' ASC';
        }
        else {
            $query = 'SELECT * FROM ' . $this->_table_name . ' WHERE status = "1"';
        }

        $res = $wpdb->get_results($query);
        return $res;
    }
    
    public function deleteByIds($ids = array()) 
    {
        global $wpdb;
        $query = 'DELETE FROM ' . $this->_table_name . ' WHERE ID IN(' . implode(', ', $ids) . ')';
        $wpdb->query($query);        
        return true;
    }
    
    public function deleteById($id) 
    {
        global $wpdb;
        $query = 'DELETE FROM ' . $this->_table_name . ' WHERE ID = ' . $id;
        $res = $wpdb->query($query);        
        return true;
    }    
    
    public function getByField($field_name, $value) {
        global $wpdb;
        
        if ($this->_sort_order_field) {
            $order = ' ORDER BY ' . $this->_sort_order_field . ' ASC';
        }
        else {
            $order = '';
        }        
        
        $query = 'SELECT * FROM ' . $this->_table_name . ' WHERE status = "1" AND ' . $field_name . '=' . $value . $order;
        $res = $wpdb->get_results($query);        
        return $res;        
    }
    
    public function getById($value) {
        global $wpdb;
                
        $query = 'SELECT * FROM ' . $this->_table_name . ' WHERE status = "1" AND ID = ' . $value;
        $res = $wpdb->get_results($query);        
        return $res[0];        
    }    
}

class DatabaseTable_Slider extends DatabaseTable {
    
    public function __construct() {        
        $this->_array_fields['ID'] = array();
        $this->_array_fields['name'] = array();   
        $this->_array_fields['height'] = array();  
        $this->_array_fields['width'] = array();  
        $this->_array_fields['options'] = array();  
        
        parent::__construct('wp_origo_sliders');
    }
}

class DatabaseTable_Slide extends DatabaseTable {
    
    public function __construct() {        
        $this->_array_fields['ID'] = array();
        $this->_array_fields['name'] = array();   
        $this->_array_fields['bg_image'] = array();
        $this->_array_fields['bg_color'] = array();
        $this->_array_fields['layout'] = array();
        $this->_array_fields['content1'] = array();
        $this->_array_fields['content2'] = array();
        $this->_array_fields['parent_id'] = array();
        $this->_array_fields['options'] = array();
        $this->_array_fields['sort_order'] = array();
        
        parent::__construct('wp_origo_slides');
    }
}