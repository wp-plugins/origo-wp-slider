<?php
/*
Plugin Name: Origo WP Slider
Plugin URI: http://origothemes.com/wordpress-plugins/origo-slider/
Description: A very flexible and easy to use wordpress slider, that you can insert on the pages, posts or to on theme files. It comes with three slide layout that you can choose from
Version: 1.0.0.1
Author: Origo Themes
Author URI: http://webdev-tuts.com
License: GPLv2 or later
*/

define('ORIGO_SLIDER_ADMIN_URL', plugin_dir_url(__FILE__));
define('ORIGO_SLIDER_ADMIN_PATH', plugin_dir_path(__FILE__));

include_once('includes/slider.class.php');
include_once('includes/AjaxCall.php');
include_once('includes/Record.php');

$slider_page = new OrigoSlider();
add_action('admin_menu', array($slider_page, 'addAdminPage'));

add_shortcode('origo-slider', 'origoSliderShortcode');

function origoSliderShortcode($args = array(), $content = '')
{
    $slider_id = $args['id'];
    
    $db_slider = new DatabaseTable_Slider();
    $db_slides = new DatabaseTable_Slide();
    $db_slides->setSortOrderField('sort_order');             
    $slider = $db_slider->getbyId($slider_id);
    $slides = $db_slides->getByField('parent_id', $slider_id);    
    $slider_width = ($slider->width) ? $slider->width . 'px' : '100%';
    $slider_height = ($slider->height) ? $slider->height . 'px' : '400px';
    $slider_options = unserialize($slider->options);
    $flex_options = $slider_options['flex'];

    $slider_html = '<div id="origo-slider-' . $slider->ID . '" class="flexslider origo-flexslider" style="height: ' . $slider_height . '; width: ' . $slider_width . '">
                        <ul class="slides">';

    foreach ($slides as $slide) {        
        $options = unserialize($slide->options);
        $css = $options['css'];           
        ?>
        <style type="text/css">
            #origo-slider-<?php echo $slider->ID?> .slide-<?php echo $slide->ID ?> {
                background-size: <?php echo $css['slide']['background-size'] ?>;
                background-repeat: <?php echo $css['slide']['background-repeat'] ?>;
            }

            #origo-slider-<?php echo $slider->ID?> .slide-<?php echo $slide->ID ?> .left .inner3 {
                border-color: <?php echo $css['content1']['border-color'] ?>;
                border-style: <?php echo $css['content1']['border-style'] ?>;
                border-width: <?php echo $css['content1']['border-width'] ?>px;
                border-radius: <?php echo $css['content1']['border-radius'] ?>px;
                width: <?php echo ($css['content1']['width']) ? $css['content1']['width'] : 400 ?>px;
                height: <?php echo $css['content1']['height'] ?>px;
                font-family: <?php echo $css['content1']['font-family'] ?>;
                font-size: <?php echo $css['content1']['font-size'] ?>px;
                color: <?php echo $css['content1']['color'] ?>;
                max-width: 100%;
            }
            
            #origo-slider-<?php echo $slider->ID?> .slide-<?php echo $slide->ID ?>.layout-2 .left .inner3 {
                width: <?php echo ($css['content1']['width']) ? $css['content1']['width'].'px' : '100%' ?>;
            }            
            
            #origo-slider-<?php echo $slider->ID?> .slide-<?php echo $slide->ID ?> .left .inner3 h1,
            #origo-slider-<?php echo $slider->ID?> .slide-<?php echo $slide->ID ?> .left .inner3 h2,
            #origo-slider-<?php echo $slider->ID?> .slide-<?php echo $slide->ID ?> .left .inner3 h3,
            #origo-slider-<?php echo $slider->ID?> .slide-<?php echo $slide->ID ?> .left .inner3 h4,
            #origo-slider-<?php echo $slider->ID?> .slide-<?php echo $slide->ID ?> .left .inner3 h5,
            #origo-slider-<?php echo $slider->ID?> .slide-<?php echo $slide->ID ?> .left .inner3 p {
                color: <?php echo $css['content1']['color'] ?>;
            }

            #origo-slider-<?php echo $slider->ID?> .slide-<?php echo $slide->ID ?> .left .inner3:before {
                background: <?php echo $css['content1']['box-color'] ?>;
                opacity: <?php echo $css['content1']['box-opacity'] ?>;
            }

            #origo-slider-<?php echo $slider->ID?> .slide-<?php echo $slide->ID ?> .right .inner3 {
                border-color: <?php echo $css['content2']['border-color'] ?>;
                border-style: <?php echo $css['content2']['border-style'] ?>;
                border-width: <?php echo $css['content2']['border-width'] ?>px;
                border-radius: <?php echo $css['content2']['border-radius'] ?>px;
                width: <?php echo ($css['content1']['width']) ? $css['content1']['width'] : 400 ?>px;
                height: <?php echo $css['content2']['height'] ?>px;
                font-family: <?php echo $css['content2']['font-family'] ?>;
                font-size: <?php echo $css['content2']['font-size'] ?>px;
                color: <?php echo $css['content2']['color'] ?>;
                max-width: 100%;
            }

            #origo-slider-<?php echo $slider->ID?> .slide-<?php echo $slide->ID ?> .right .inner3 h1,
            #origo-slider-<?php echo $slider->ID?> .slide-<?php echo $slide->ID ?> .right .inner3 h2,
            #origo-slider-<?php echo $slider->ID?> .slide-<?php echo $slide->ID ?> .right .inner3 h3,
            #origo-slider-<?php echo $slider->ID?> .slide-<?php echo $slide->ID ?> .right .inner3 h4,
            #origo-slider-<?php echo $slider->ID?> .slide-<?php echo $slide->ID ?> .right .inner3 h5,
            #origo-slider-<?php echo $slider->ID?> .slide-<?php echo $slide->ID ?> .right .inner3 p {
                color: <?php echo $css['content2']['color'] ?>;
            }

            #origo-slider-<?php echo $slider->ID?> .slide-<?php echo $slide->ID ?> .right .inner3:before {
                background: <?php echo $css['content2']['box-color'] ?>;
                opacity: <?php echo $css['content2']['box-opacity'] ?>;
            }
        </style>
        <?php

        $slider_html .=  '  <li class="slide slide-' . $slide->ID . ' layout-' . $slide->layout . '" style="background-color:' . 
                $slide->bg_color . '; background-image: url(' . $slide->bg_image . ')">';
        $slider_html .= '<div class="left"><div class="inner"><div class="inner2"><div class="inner3">' .do_shortcode(stripslashes($slide->content1)) . '</div></div></div></div>';
        
        if ($slide->layout == 1) {
            $slider_html .= '<div class="right"><div class="inner"><div class="inner2"><div class="inner3">' . do_shortcode(stripslashes($slide->content2)) . '</div></div</div></div>';
        }
        
        $slider_html .=  '</li>';
    }

    $slider_html .= '   </ul>
                    </div>';
    echo $slider_html;
       
    ?>
    <script type="text/javascript">
    $jx = jQuery.noConflict();

    $jx(window).load(function() {
      $jx('#origo-slider-<?php echo $slider->ID?>').flexslider({
        animation: "<?php echo $flex_options['animation']?>",
        prevText: "",
        nextText: "",     
        slideshowSpeed: "<?php echo $flex_options['slideshowSpeed']?>",
        animationSpeed: "<?php echo $flex_options['animationSpeed']?>",
        animationLoop: "<?php echo ($flex_options['animationLoop'] == 'on') ? true : false?>",
        slideshow: "<?php echo ($flex_options['slideshow'] == 'on') ? true : false?>",
        pauseOnHover: "<?php echo ($flex_options['pauseOnHover'] == 'on') ? true : false?>",
        controlNav: "<?php echo ($flex_options['controlNav'] == 'on') ? true : false?>",
        directionNav: "<?php echo ($flex_options['directionNav'] == 'on') ? true : false?>",
      });
    });
    </script>
    <?php
    
}

function showFontFamilyOptions($name, $selected) {
    $select = '<select class="fonts" name="' . $name . '">';
    
    $fonts = array('arial', 'tahoma', 'helvetica', 'Comic Sans MS', 'Trebuchet MS');
    
    foreach ($fonts as $f) {
        $is_selected = ($f == $selected) ? ' selected="selected"' : '';
        $select .= '<option value="' . $f . '" ' . $is_selected . '>' . $f . '</option>';
    }
    $select .= '</select>';
    return $select; 
}

function showSliderAnimationOptions($name, $selected) {    
    $options = array('slide', 'fade');
    return showDropDownOptions($name, $options, $selected); 
}

function showBgSizeOptions($name, $selected) {    
    $options = array('auto', 'cover');
    return showDropDownOptions($name, $options, $selected); 
}

function showBgRepeatOptions($name, $selected) {    
    $options = array('no-repeat', 'repeat-x', 'repeat-y', 'repeat');   
    return showDropDownOptions($name, $options, $selected); 
}

function showBorderStyleOptions($name, $selected) {    
    $options = array('none', 'solid', 'dotted', 'dashed');   
    return showDropDownOptions($name, $options, $selected); 
}

function showDropDownOptions($name, $options, $selected) {
    $select = '<select class="border-style" name="' . $name . '">';        
    foreach ($options as $o) {
        $is_selected = ($o == $selected) ? ' selected="selected"' : '';
        $select .= '<option value="' . $o . '" ' . $is_selected . '>' . $o . '</option>';
    }
    $select .= '</select>';
    return $select;     
}

register_activation_hook(__FILE__, 'jal_install');

global $jal_db_version;
$jal_db_version = '1.0';

function jal_install() {
    global $wpdb;
    global $jal_db_version;

    $table_name_slides = $wpdb->prefix . 'origo_slides';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS " . $table_name_slides . " (
        ID int(11) NOT NULL AUTO_INCREMENT,
        name varchar(50) NOT NULL,
        bg_image varchar(500) NOT NULL,
        bg_color varchar(50) NOT NULL,
        layout int(11) NOT NULL,
        content1 text NOT NULL,
        content2 text NOT NULL,
        parent_id int(11) NOT NULL,
        options text NOT NULL COMMENT 'array',
        sort_order int(11) NOT NULL,
        guid varchar(50) NOT NULL,
        status int(11) NOT NULL DEFAULT '1',
        PRIMARY KEY (`ID`)  
    ) $charset_collate;";        

    $table_name_sliders = $wpdb->prefix . 'origo_sliders';
    
    $sql2 = "CREATE TABLE IF NOT EXISTS " . $table_name_sliders . " (
        ID int(11) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        status int(11) NOT NULL DEFAULT '1' COMMENT '1 or 0',
        height decimal(10,0) NOT NULL,
        width decimal(10,0) NOT NULL,
        options text NOT NULL COMMENT 'serialized',
        PRIMARY KEY (`ID`)        
    ) $charset_collate;";    

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    dbDelta( $sql2 );
}
	