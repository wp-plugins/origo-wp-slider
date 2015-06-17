<?php
add_action('wp_ajax_save-origo-slider', array('AjaxCall', 'saveSlider'));
add_action('wp_ajax_nopriv_save-origo-slider', array('AjaxCall', 'saveSlider'));//for users that are not logged in         

add_action('wp_ajax_add-origo-slider', array('AjaxCall', 'addSlider'));
add_action('wp_ajax_nopriv_add-origo-slider', array('AjaxCall', 'addSlider'));//for users that are not logged in   

add_action('wp_ajax_save-origo-get-slide', array('AjaxCall', 'addSlide'));
add_action('wp_ajax_nopriv_save-origo-get-slide', array('AjaxCall', 'addSlide'));//for users that are not logged in
        
add_action('wp_ajax_delete_origo_slider', array('AjaxCall', 'deleteSlider'));
add_action('wp_ajax_nopriv_delete_origo_slider', array('AjaxCall', 'deleteSlider'));//for users that are not logged in

class AjaxCall
{
    public function deleteSlider()
    {
        global $wpdb;
        $db_slider = new DatabaseTable_Slider();        
        $id = intval($_POST['id']);
        
        if ($id != 0) {
            $db_slider->deleteById($id);   
            $data['deleted_slider_id'] = $id;
        }
        else {
            $data['deleted_slider_id'] = 0;
        }
        print_r(json_encode($data));
        exit;
    } 
    
    public function addSlider()
    {
        global $wpdb;
        $db_slider = new DatabaseTable_Slider();
        
        $user_input = $_POST['slider'];
        $flex['flex']['controlNav'] = 'on';
        $flex['flex']['directionNav'] = 'on';
        $flex['flex']['slideshowSpeed'] = '2000';
        $flex['flex']['animationSpeed'] = '1500';
        $flex['flex']['animationLoop'] = 'on';
        $flex['flex']['slideshow'] = 'on';
        
        
        $user_input['options'] = serialize($flex);
                
        if ($db_slider->save($user_input)) {
            $last_id = $wpdb->insert_id;
            $data['html'] = '<tr class="<?php echo $is_alternate_class ?>">
                    <td class="">
                        <a style="text-decoration: none;" href="?page=origo-slider&slider_id=' . $last_id . '">' . $user_input['name'] . 
                    '</a></td>
                    <td class="">[origo-slider id=' . $last_id . ']</td>
                    <td class="">
                        <a style="text-decoration: none;" href="?page=origo-wp-slider&slider_id=' . $last_id . '">Edit</a> &bull;
                        <a style="text-decoration: none;" class="delete_slider" data-id="' . $last_id . '" href="#delete">Delete</a>
                    </td>
                </tr>  ';
            print_r(json_encode($data));
        }
        exit;
    }    

    public function saveSlider()
    {
        $db_slides = new DatabaseTable_Slide();
        $db_slider = new DatabaseTable_Slider();
        
        $user_data = $_POST;
        $user_data['slider']['options'] = serialize($user_data['slider_options']);
        
        // start saving slider details        
        $db_slider->save($user_data['slider']); // sanitize_text_field is done here
        // done updating slider details

        // start saving slides        
        foreach ($user_data['slide-order'] as $idx => $value) {
            $user_input = $user_data['slide'][$value];
            if ($user_input['name']) {
                //$slides[] = $user_data['slide'][$value];
                $user_input['parent-id'] = $user_data['slider']['ID'];
                
                $content1 = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $user_input['content1']);
                $content2 = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $user_input['content2']);
                
                $user_input['content1'] = htmlspecialchars($content1);
                $user_input['content2'] = htmlspecialchars($content2);
                
                $user_input['sort-order'] = $idx + 1; //since idx start at 0, and I want the sort_order value to start at 1
                //print_r($user_data['css'][$value]);
                $user_input['options'] = serialize(array('css' => $user_data['css'][$value],
                                                         'additional-details' => $user_data['additional-details'][$value]
                                                        ));

                if ($user_input['new'] == 1) {
                    unset($user_input['new']);
                    unset($user_input['ID']);
                }

                $db_slides->save($user_input);  // sanitize_text_field is done here
            }
        }
        // done saving slides

        // delete slides
        $delete_slides_ids = explode(',', sanitize_text_field($_POST['delete_slides']));

        $db_slides->deleteByIds($delete_slides_ids);
        // done deleting slides

        print_r(json_encode($user_data));
        exit;        
    }    
    
    public function addSlide()
    {
        $id = time();
        ob_start();
        wp_editor('', 'editor-' . $id);
        $editor = ob_get_contents();
        ob_end_clean();         
        $slide =        '<li>
                            <span class="handle">Drag</span>
                            Slide ' . $id . '
                            <div class="slide-form slide-form-' . $id . '">                  
                                <div class="inner">
                                    <div class="row">
                                        <label class="field-label">Name</label>
                                        <input type="text" name="slide[' .  $id . '][name]" value="Slide ' . $id . '"/>
                                    </div>
                                   <div class="row">
                                        <label class="field-label">Background Image</label>
                                        <div class="uploader">
                                            <input id="slide-image-' .  $id . '" name="slide[' .  $id . '][bg-image]" type="text"  value="" />
                                            <input class="button media-uploader" rel="slide-image-' .  $id . '"type="button" value="Upload" />
                                        </div>
                                        <div class="color-picker2">
                                            <label style="display: none">Color</label>
                                            <input name="slide[' . $id . '][bg-color]" class="wp-color-picker2"  value="" />
                                        </div>                                        
                                    </div>
                                    <div class="row">
                                        <label class="field-label"><span style="color: #aaa; font-size: 12px;">-</span></label>
                                        ' . showBgSizeOptions('css[' . $id . '][slide][background-size]', '') . 
                                        showBgRepeatOptions('css[' . $id  . '][slide][background-repeat]', '') . 
                                    '</div>                                      
                                    <div class="row">
                                        <label class="field-label">Layout</label>
                                        <div class="slide-layout toggle-btn-grp">
                                            <label onclick="" class="toggle-btn success">
                                                <input value="1" type="radio" name="slide[' . $id . '][layout]" checked="checked"/>
                                                <img src="' . ORIGO_SLIDER_ADMIN_URL . '/includes/images/template1.jpg" /> 
                                            </label>
                                            <label onclick="" class="toggle-btn">
                                                <input value="2" type="radio" name="slide[' . $id . '][layout]"/>
                                                <img src="' . ORIGO_SLIDER_ADMIN_URL  . '/includes/images/template4.jpg" />
                                            </label>
                                            <label onclick="" class="toggle-btn">
                                                <input value="3" type="radio" name="slide[' . $id . '][layout]"/>
                                                <img src="' . ORIGO_SLIDER_ADMIN_URL . '/includes/images/template5.jpg" />
                                            </label>
                                        </div>
                                    </div>                                    
                                    <div class="row slide-content">
                                        <label class="field-label">Content 1</label>
                                        <textarea name="slide[' . $id . '][content1]"></textarea>
                                    </div>
                                    <div class="content1-css">
                                        <div class="row">
                                            <label class="field-label">Text</label>
                                            <div class="color-picker2">
                                                <label style="display: none" class="field-label">Color</label>
                                                <input name="css[' . $id . '][content1][color]" class="wp-color-picker2"  value="" />
                                            </div>               
                                            ' . showFontFamilyOptions('css[' . $id . '][content1][font-family]', '') . '
                                            <input title="font size" name="css[' . $id . '][content1][font-size]" type="text" value="" size="3"/> px                                        
                                        </div>
                                        <div class="row">
                                            <label class="field-label">Border</label>
                                            <div class="color-picker2">
                                                <label style="display: none" class="field-label">Color</label>
                                                <input name="css[' . $id . '][content1][border-color]" class="wp-color-picker2"  value="" />
                                            </div>                                         
                                            ' . showBorderStyleOptions('css[' . $id . '][content1][border-style]', '') . '
                                            <input title="Border Width" name="css[' . $id . '][content1][border-width]" type="text" value="" size="3"/> px                                          
                                            <input title="Border Radius" name="css[' . $id . '][content1][border-radius]" type="text" value="" size="3"/> px 
                                        </div>
                                        <div class="row">
                                            <label class="field-label">Background</label>                                        
                                            <div class="color-picker2">
                                                <label style="display: none" class="field-label">Color</label>
                                                <input name="css[' . $id . '][content1][box-color]" class="wp-color-picker2"  value="" />
                                            </div>                                        
                                            <input title="Opacity" name="css[' . $id . '][content1][box-opacity]" type="text" value="" size="3"/><em>Set to 0 if you want to hide this section</em>
                                        </div>
                                        <div class="row">
                                            <label class="field-label">Dimension</label>                                                                                                    
                                            <input title="Width" name="css[' . $id . '][content1][width]" type="text" value="" size="3"/> px
                                            <input title="Height" name="css[' . $id . '][content1][height]" type="text" value="" size="3"/> px
                                        </div>
                                    </div>                                    
                                    <div class="row slide-content">
                                        <label class="field-label">Content 2</label>
                                        <textarea name="slide[' . $id . '][content2]"></textarea>
                                    </div>    
                                    <div class="content1-css">
                                        <div class="row">
                                            <label class="field-label">Text</label>
                                            <div class="color-picker2">
                                                <label style="display: none" class="field-label">Color</label>
                                                <input name="css[' . $id . '][content2][color]" class="wp-color-picker2"  value="" />
                                            </div>               
                                            ' . showFontFamilyOptions('css[' . $id . '][content2][font-family]', '') . '
                                            <input title="font size" name="css[' . $id . '][content2][font-size]" type="text" value="" size="3"/> px                                        
                                        </div>
                                        <div class="row">
                                            <label class="field-label">Border</label>
                                            <div class="color-picker2">
                                                <label style="display: none" class="field-label">Color</label>
                                                <input name="css[' . $id . '][content2][border-color]" class="wp-color-picker2"  value="" />
                                            </div>                                         
                                            ' . showBorderStyleOptions('css[' . $id . '][content2][border-style]', '') . '
                                            <input title="Border Width" name="css[' . $id . '][content2][border-width]" type="text" value="" size="3"/> px                                          
                                            <input title="Border Radius" name="css[' . $id . '][content2][border-radius]" type="text" value="" size="3"/> px 
                                        </div>
                                        <div class="row">
                                            <label class="field-label">Background</label>                                        
                                            <div class="color-picker2">
                                                <label style="display: none" class="field-label">Color</label>
                                                <input name="css[' . $id . '][content2][box-color]" class="wp-color-picker2"  value="" />
                                            </div>                                        
                                            <input title="Opacity" name="css[' . $id . '][content2][box-opacity]" type="text" value="" size="3"/><em>Set to 0 if you want to hide this section</em>
                                        </div>
                                        <div class="row">
                                            <label class="field-label">Dimension</label>                                                                                                    
                                            <input title="Width" name="css[' . $id . '][content2][width]" type="text" value="" size="3"/> px
                                            <input title="Height" name="css[' . $id . '][content2][height]" type="text" value="" size="3"/> px
                                        </div>
                                    </div>                                                                           
                                    <input type="hidden" name="slide-order[]" value="' . $id . '">
                                    <input type="hidden" name="slide[' . $id . '][guid]" value="' . $id . '">
                                    <input type="hidden" name="slide[' . $id . '][ID]" value="' . $id . '">
                                    <input type="hidden" class="is-new" name="slide[' . $id . '][new]" value="1">
                                </div>                                
                            </div>
                            <div class="item-actions">
                                <input type="button" class="slide-edit button" name="slide-edit" id="' . $id . '" value="Edit" />
                                <a href="#" class="delete-slide button">x</a>
                            </div>
                        </li>';
        
        $data['slide'] = $slide;
        print_r(json_encode($data));
        exit;
    }    
}

/*

 *  
 */