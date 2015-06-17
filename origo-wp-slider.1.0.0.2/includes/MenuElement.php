<?php
/**
action          get-list
css_id          list-1428745895896
element_id	
element_name	gdfg
has-inner	1
new             1
page-designer	<p>fdsfsd</p><p>fsdfdsfsd</p>
parentId	el-editor
post_id         858
to              append
**/

class MenuElement extends Element {
    private $_menu_location;
    private $_level1_template;
    private $_level2_template;
    private $_level3_template;
    private $_level4_template;
    
    public function __construct($data = array(), $is_new = true )
    {        
        $this->_type = 'menu';
        $this->_element_class = 'el-menu';
        $this->_menu_location = $data['menu_location'];
        $this->_level1_template = $data['level1_template'];
        $this->_level2_template = $data['level2_template'];
        $this->_level3_template = $data['level3_template'];
        $this->_level4_template = $data['level4_template'];
        
        parent::__construct($data, $is_new);
    }

    public function getHtml()
    {
        $defaults = array(
                'theme_location'  => $this->_menu_location,
                'menu'            => '',
                'container'       => 'div',
                'container_class' => '',
                'container_id'    => '',
                'menu_class'      => 'menu',
                'menu_id'         => '',
                'echo'            => false,
                'fallback_cb'     => 'wp_page_menu',
                'before'          => '',
                'after'           => '',
                'link_before'     => '',
                'link_after'      => '',
                'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                'depth'           => 0,
                'walker'          => ''
        );

        $menu = wp_nav_menu($defaults);
        
        if ($this->_is_new) {
            $html = $this->getBeginHtml('div', $this->__toArray(), array($this->_element_class)) . 
                '<span class="inner">';
        }
        else {
            $html = $this->getBeginHtml('div', $this->__toArray(), array($this->_element_class, 'el-current')) . 
                    '<span id="wrap"><span id="el-options"><a href="#" id="edit">Edit</a> | <a href="#" id="delete">Delete</a> | 
                    <a id="sort" href="#">Sort</a></span><span id="_main"><span class="inner">';            
        }
        
        $html .= $menu;
        
        if ($this->_is_new) {
            $html .= '</span></div>';  
        }
        else {
            $html .= '</span></span></span></div>';  
        }

        return $html;
    }
    
    public function save() 
    {        
        $adv = array();
        $adv['navigation_location'] = $this->_menu_location;
        $adv['level1_template'] = $this->_level1_template;
        $adv['level2_template'] = $this->_level2_template;
        $adv['level3_template'] = $this->_level3_template;
        $adv['level4_template'] = $this->_level4_template;
        updateAdvStyles($this->_post_id, $this->_id,  $adv);
    
        Element::$all_elements[$this->_id]['menu_location'] = $this->_menu_location;
        Element::$all_elements[$this->_id]['level1_template'] = $this->_level1_template;
        Element::$all_elements[$this->_id]['level2_template'] = $this->_level2_template;
        Element::$all_elements[$this->_id]['level3_template'] = $this->_level3_template;
        Element::$all_elements[$this->_id]['level4_template'] = $this->_level4_template;
        parent::save();
    }
}