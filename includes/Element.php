<?php
class Element {
    protected $_type;
    protected $_value;
    protected $_element_name;
    protected $_parent_id;
    protected $_id;
    protected $_post_id;
    protected $_sub_element_ids;
    protected $_sub_element_objs;
    protected $_is_new;
    protected $_element_class;

    public static $all_elements;

    public function __construct($data = array(), $is_new = true) {
        $this->_element_name = $data['element_name'];
        $this->_post_id = $data['post_id'];                
        $this->_id = $data['id'];  
        $this->_parent_id = $data['parent_id'];
        $this->_value = $data['value'];
        $this->_sub_element_ids = $data['sub_element_ids'];
        
        $this->_is_new = $is_new;
        //echo "\nIs New: " . $is_new;
        
        if ($this->_is_new) {
            $this->_addToElementsList();
        }
    }

    public static function getBeginHtml($tag, $data = array(), $additional_classes = array()) {
        return '<' . $tag . ' class="' . implode(' ', $additional_classes) . ' '. $data['id'] . 
                ' parent-' . $data['parent_id'] . ' '  
                . '" id="' . $data['id'] . '">';
    }

    public function getType() {
        return $this->_type;
    }

    public function save()
    {
        if ($this->_value) {
            Element::$all_elements[$this->_id]['value'] = $this->_value;
        }
        else {
            unset(Element::$all_elements[$this->_id]['value']);
        }
        Element::$all_elements[$this->_id]['element_name'] = $this->_element_name;
        Element::$all_elements[$this->_id]['parent_id'] = $this->_parent_id;
        Element::$all_elements[$this->_id]['type'] = $this->_type;
        
        if (is_array($this->_sub_element_ids)) {
            Element::$all_elements[$this->_id]['sub_element_ids'] = $this->_sub_element_ids;
        }
        else {
            unset(Element::$all_elements[$this->_id]['sub_element_ids']);
        }
        
        if($this->_is_new) {
            Element::$all_elements[$this->_parent_id]['sub_element_ids'][] = $this->_id;
        }
        
        update_post_meta($this->_post_id, 'pd-contents', Element::$all_elements);
    }
    
    public function updateContent($content) {
        unset($content['html']);
        //echo "<br />Update Content - " . $this->_id;
        //print_r($content);

        $post_contents = get_post_meta($this->_post_id, 'pd-contents', true);    
        $post_contents[$this->_id] = $content;
        
        // add to parent
        $parentId = $this->_parent_id;
        $post_contents[$parentId]['sub'][] = $this->_id;
        update_post_meta($this->_post_id, 'pd-contents', $post_contents);
    }     
    
    public function __toArray() {        
        $arr['type'] = $this->_type;
        $arr['value'] = $this->_value;
        $arr['element_name'] = $this->_element_name;
        $arr['parent_id'] = $this->_parent_id;
        $arr['id'] = $this->_id;
        $arr['post_id'] = $this->_post_id;
        $arr['sub_element_ids'] = $this->_sub_element_ids;        
        
        return $arr;
    }
    public function __toString() {
        $element = "{\nType: " . $this->_type;
        $element .= "\nName: " . $this->_element_name;
        $element .= "\nCSS ID/ID: " . $this->_id;
        $element .= "\nPost ID: " . $this->_post_id;
        if (is_array($this->_sub_element_ids)) {
            $element .= "\nItems: " . implode(', ', $this->_sub_element_ids);
        }
        $element .= "\n}\n";
        return $element;
    }

    public function getId()
    {
        return $this->_id;
    }  
    
    private function _addToElementsList() {
        //echo "\n***> " . $this->_element_name;
        $temp_element_id = strtolower($this->_element_name);    
        $temp_element_id = str_replace(' ', '-', $temp_element_id); // Replaces all spaces with hyphens.
        $temp_element_id = preg_replace('/[^A-Za-z0-9\-]/', '', $temp_element_id); // Removes special chars.

        $elements = get_option('pd-elements');

        if (!is_array($elements)) {
            $elements = array();
        }

        $element_id = $temp_element_id;
        $ctr = 1;

        while (is_array($elements[$element_id])) {
            $element_id = $temp_element_id . '-' . $ctr;
            $ctr++;
        }

        $elements[$element_id] = array('post_id' => $this->_post_id);

        //echo "\n==> " . $element_id;
        update_option('pd-elements', $elements);
        $this->_id = $element_id;
    }
    
    /*function prepareContent() {
        switch ($type) {
            case 'paragraph':            
                $html['html'] = '<p id="' . $elementId  . '" class="el-par parent-' . $parentId . ' ' 
                    . $elementId . '"><span class="inner">' . cleanContent($content) . 
                    '</span></p>';
                $html['value'] = cleanContent($content);
                break;
            case 'header': //$post['header-type']
                $html = prepareContentHeader($args['header-type'], $elementId, $parentId, cleanContent($content));
                break;
            case 'list':
                $html = prepareContentList($elementId, $parentId, $content, $args);
        }

        $html['parent'] = $parentId;
        $html['type'] = $type;

        return $html;
    }    */

}