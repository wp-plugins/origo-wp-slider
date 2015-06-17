<?php
class OrigoSlider {
    private $_form_name = 'origo-slider';

    public function addAdminPage()
    {        
        add_menu_page('Origo Sliders', 'Origo Slider', 'administrator', 
            'origo-wp-slider', array($this, 'showSlidersPage')
        );
    }
    
    public function __construct()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('jquery-ui-sortable');
        
        wp_enqueue_style( 'wp-color-picker' );         
        wp_enqueue_script(
            'wp-color-picker',
            admin_url( 'js/color-picker.min.js'),
            array( 'iris' ),
            false,
            1
        );          
        wp_enqueue_script('jquery-flexslider', ORIGO_SLIDER_ADMIN_URL . '/scripts/flexslider/jquery.flexslider.js');
        wp_enqueue_script('flexslider', ORIGO_SLIDER_ADMIN_URL . '/includes/js/flexslider/flexslider.js');
        wp_enqueue_style('flexslider-styes', ORIGO_SLIDER_ADMIN_URL . '/styles/flexslider/flexslider.css');
        wp_enqueue_style('origo-slider-jquery-ui', ORIGO_SLIDER_ADMIN_URL . '/includes/css/jquery-ui.css');
        wp_enqueue_script('origo-slider-ajax-call', ORIGO_SLIDER_ADMIN_URL . '/includes/js/AjaxScripts.js');        
        
        wp_enqueue_style('cose-admin-css',  ORIGO_SLIDER_ADMIN_URL . '/includes/css/admin.css');        
        
    }


    private function _ajax()
    {
    ?>
    <script type="text/javascript">
    $jx = jQuery.noConflict();     
    $jx(document).ready(function(){
        /* Home Slider */
        $jx('.slide-edit').live('click', function() {            
            id = $jx(this).attr('id');
            current_id = $jx('input[name="current_slide"]').val();

            if (current_id) {
                current_form = $jx('.slide-form-editor .inner');
                current_form.appendTo('.slide-form-' + current_id);
            }
            $jx('.slide-form-editor .inner').html('');
            $jx('.slide-form-' + id + ' .inner').appendTo('.slide-form-editor');

            $jx(".toggle-btn:not('.noscript') input[type=radio]").addClass("visuallyhidden");
            $jx(".toggle-btn:not('.noscript') input[type=radio]").change(function() {
                if( $jx(this).attr("name") ) {
                    $jx(this).parent().addClass("success").siblings().removeClass("success")
                } else {
                    $jx(this).parent().toggleClass("success");
                }
            });   
            
            $jx('input[name="current_slide"]').val(id);            
            
            return false;
        });    
        
        $jx('.cose-tabs').tabs();

        $jx('.media-uploader').live('click', function(){
            var send_attachment_bkp = wp.media.editor.send.attachment;
            var button = $jx(this);
            var rel = $jx(this).attr('rel');
            _custom_media = true;

            wp.media.editor.send.attachment = function(props, attachment){
                if ( _custom_media ) {
                    $jx("#" + rel).val(attachment.url);
                }
                else {
                    return _orig_send_attachment.apply( this, [props, attachment] );
                }
            }
            wp.media.editor.open(button);
        });

        $jx('.slider .rows').sortable({handle: '.handle'});
        $jx('.wp-color-picker2').wpColorPicker();

        $jx(".toggle-btn:not('.noscript') input[type=radio]").addClass("visuallyhidden");
        $jx(".toggle-btn:not('.noscript') input[type=radio]").change(function() {
            if( $jx(this).attr("name") ) {
                $jx(this).parent().addClass("success").siblings().removeClass("success")
            }
            else {
                $jx(this).parent().toggleClass("success");
            }
        });
    });
    </script>
    <?php
    }
    
    public function showSlidersPage()
    {
        $this->_ajax();
        //$slides = get_option('cose-main-slider');
        //$slide_order = get_option('cose-main-slider-order');
        //$slides_new = $slides['new'];
        $slider_id = $_GET['slider_id'];
        $db_slider = new DatabaseTable_Slider();
        $db_slides = new DatabaseTable_Slide();
        $db_slides->setSortOrderField('sort_order');             
        $slider = $db_slider->getbyId($slider_id);
        $slides = $db_slides->getByField('parent_id', $slider_id);
        
        if ($slider_id) {
            $this->showSingleSliderPage($slider, $slides);
        }
        else {            
            $data = $db_slider->getAll();
            $this->displaySliders($data);  
        }
        
        
    }
    
    public function showSingleSliderPage($slider, $slides)
    {        
    ?>
    <div class="wrap">
        <h2>Origo Sliders</h2>
        <form name="origo_slider" class="origo-form" method="POST">
            <div id="notice"><span></span></div>
            <div class="origo_slider_top_buttons">                
                <a href="?page=origo-wp-slider" class="button">Back to Sliders</a>
                <input type="submit" value="Save Changes" class="button-primary" />     
            </div>
            <div class="cose-tabs">
                <ul>
                    <li><a href="#my-wordpress-tabs-1">Slides</a></li>
                    <li><a href="#my-wordpress-tabs-2">Settings</a></li>
                </ul>
                <div id="my-wordpress-tabs-1">                                              
                    <?php                        
                        $this->diplaySlides($slides);                         
                        echo '<input type="hidden" name="slider[ID]" value="' . $slider->ID . '">';
                    ?>
                </div>
                <div id="my-wordpress-tabs-2">
                    <div name="slider_setting" method="POST" class="slider_form">
                        <?php
                        $height = ($slider->height == 0) ? '' : $slider->height;
                        $width = ($slider->width == 0) ? '' : $slider->width;
                        $slider_options = unserialize($slider->options);
                        $flex_options = $slider_options['flex'];
                        ?>
                        <div class="row">
                            <label class="field-label">Name</label>
                            <input type="text" name="slider[name]" value="<?php echo $slider->name ?>" />
                        </div>
                        <div class="row">
                            <label class="field-label">Height</label>
                            <input type="text" name="slider[height]" value="<?php echo $height ?>" />px <em>default: 400px</em>
                        </div>     
                        <div class="row">
                            <label class="field-label">Width</label>
                            <input type="text" name="slider[width]" value="<?php echo $width ?>" />px <em>leave blank for full width</em>
                        </div>                        
                        <div class="row">
                            <label class="field-label">Shortcode</label>
                            <input type="text" name="slider_shortcode" value="[origo-slider id=<?php echo $slider->ID ?>]" readonly="readonly"/>
                        </div>
                        <div class="row">
                            <label class="field-label">Animation</label>
                            <?php echo showSliderAnimationOptions('slider_options[flex][animation]', $flex_options['animation']) ?>
                        </div>        
                        <div class="row">
                            <label class="field-label">Slideshow Speed</label>
                            <input type="text" name="slider_options[flex][slideshowSpeed]" value="<?php echo $flex_options['slideshowSpeed'] ?>" />px <em>Set the speed of the slideshow cycling, in milliseconds</em>
                        </div> 
                        <div class="row">
                            <label class="field-label">Animation Speed</label>
                            <input type="text" name="slider_options[flex][animationSpeed]" value="<?php echo $flex_options['animationSpeed'] ?>" />px <em>Set the speed of animations, in milliseconds</em>
                        </div>                         
                        <div class="row">
                            <label class="field-label">Loop</label>
                            <input type="checkbox" name="slider_options[flex][animationLoop]" <?php echo ($flex_options['animationLoop'] == 'on') ? 'checked="checked"' : '' ?> />
                        </div>    
                        <div class="row">
                            <label class="field-label">Slideshow</label>
                            <input type="checkbox" name="slider_options[flex][slideshow]" <?php echo ($flex_options['slideshow'] == 'on') ? 'checked="checked"' : '' ?> />
                            <em>Animate slider automatically</em>
                        </div>
                        <div class="row">
                            <label class="field-label">Pause On Hover</label>
                            <input type="checkbox" name="slider_options[flex][pauseOnHover]" <?php echo ($flex_options['pauseOnHover'] == 'on') ? 'checked="checked"' : '' ?> />
                            <em>Animate slider automatically</em>
                        </div> 
                        <div class="row">
                            <label class="field-label">Show Control Nav</label>
                            <input type="checkbox" name="slider_options[flex][controlNav]" <?php echo ($flex_options['controlNav'] == 'on') ? 'checked="checked"' : '' ?> />
                            <em>Create navigation for paging control of each slide</em>
                        </div>
                        <div class="row">
                            <label class="field-label">Show Direction Nav</label>
                            <input type="checkbox" name="slider_options[flex][directionNav]" <?php echo ($flex_options['directionNav'] == 'on') ? 'checked="checked"' : '' ?> />
                            <em>Create navigation for previous/next navigation</em>
                        </div>




<!--direction: "horizontal", //String: Select the sliding direction, "horizontal" or "vertical"
randomize: false, //Boolean: Randomize slide order
pauseOnAction: true, //Boolean: Pause the slideshow when interacting with control elements, highly recommended.
multipleKeyboard: false, //{NEW} Boolean: Allow keyboard navigation to affect multiple sliders. Default behavior cuts out keyboard navigation with more than one slider present.-->
 


                    </div>     
                </div>
            </div>
            <hr />
            <div style="display: none"><?php wp_editor('', 'editor-holder'); ?></div>            
        </form>
    </div>
<?php
    }

    public function displaySliders($data)
    {
        ?>
    <div class="wrap">
        <h2>Origo Slider</h2>
        
        <table class="wp-list-table widefat fixed pages">
            <thead>
                <tr>
                    <th style="" class="manage-column" id="title" scope="col">Name</th>
                    <th style="" class="manage-column" id="author" scope="col">Shortcode</th>
                    <th style="" class="manage-column" id="author" scope="col">Actions</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th style="" class="manage-column" scope="col">Name</th>
                    <th style="" class="manage-column" scope="col">Actions</th>
                    <th style="" class="manage-column" id="author" scope="col">Actions</th>
                </tr>
            </tfoot>
            <tbody id="the_sliders">
        <?php      
        $ctr = 1;
        foreach ($data as $idx => $values) {
            $is_alternate_class = ($ctr%2) ? 'alternate ' : '';
            ?>
                <tr class="slider_<?php echo $values->ID ;?>">
                    <td class="">
                        <a style="text-decoration: none;" href="?page=origo-wp-slider&slider_id=<?php echo $values->ID ?>"><?php echo $values->name ?></a>
                    </td>
                    <td>[origo-slider id=<?php echo $values->ID ?>]</td>
                    <td class="">
                        <a style="text-decoration: none;" href="?page=origo-wp-slider&slider_id=<?php echo $values->ID ?>">Edit</a> &bull;
                        <a style="text-decoration: none;" class="delete_slider" data-id="<?php echo $values->ID ?>" href="#delete">Delete</a>
                    </td>
                </tr>                
            <?php
            $ctr++;
        }        
        ?>                

            </tbody>
        </table>
        
        <ul id="tbl_origo_slider">

        </ul>
        <form name="add_slider">
            <input type="text" name="slider[name]" value="" placeholder="Slider Name" />
            <input type="submit" class="button-primary" value="Add Slider" />
        </form>
    </div>
    <?php
    }

    /**
     * 
     * @global type $wpdb
     * @param type $slides array of object
     */
    public function diplaySlides($slides = array())
    {
    ?>
        <div class="sliders-section">
            <div class="slider right-pane">
                <ul class="rows">
                    <?php
                    $slide_ctr = 0;
                    if (is_array($slides)) {
                    foreach ($slides as $idx => $slide) {
                        ?>
                        <li data-slide_id="<?php echo $slide->ID ?>">
                            <?php include(ORIGO_SLIDER_ADMIN_PATH . 'includes/templates/slide.php') ?>
                        </li>                                
                        <?php
                        $slide_ctr++;
                    }
                    }
                    ?>                  
                </ul>
                <a href="#new" class="new-slide button">Add Slide</a>
            </div>
            <div class="slide-form slide-form-editor">
                <div class="inner">
                    <a href="http://origothemes.com/wordpress-plugins/origo-wp-slider/" target="_blank" title="Plugin Updates"><img style="width: 300px; max-width: 90%; margin: 0 auto; display: block;" src="<?php echo ORIGO_SLIDER_ADMIN_URL ?>includes/images/origo-slider.png" />                    
                </div>
                <input type="hidden" name="current_slide" value="" />
                <input type="hidden" name="delete_slides" value="" />
                
            </div>            
            <div style="display: none"><?php wp_editor('', 'tinymce-editor-temp') ?></div>
        </div>
<?php
    }
}

