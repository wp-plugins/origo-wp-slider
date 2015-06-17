                            <span class="handle">Drag</span>
                            <?php echo $slide->name ?>
                            <?php 
                                $options = unserialize($slide->options);
                                $additional_details = $options['additional-details'];
                                $css = $options['css'];
                            ?>
                            <div class="slide-form slide-form-<?php echo $slide_ctr ?>">
                                <div class="inner">
                                    <div class="row">
                                        <label class="field-label">Name</label>
                                        <input type="text" name="slide[<?php echo $slide->ID ?>][name]" value="<?php echo $slide->name ?>"/>
                                    </div>
                                    <div class="row">
                                        <label class="field-label">Slide Background</label>
                                        <div class="uploader">
                                            <input id="slide-image-<?php echo $slide->ID ?>" name="slide[<?php echo $slide->ID ?>][bg-image]" type="text"  value="<?php echo $slide->bg_image ?>" />
                                            <input class="button media-uploader" rel="slide-image-<?php echo $slide->ID ?>"type="button" value="Upload" />
                                        </div>
                                        <div class="color-picker2">
                                            <label style="display: none" class="field-label">Color</label>
                                            <input name="slide[<?php echo $slide->ID ?>][bg-color]" class="wp-color-picker2"  value="<?php echo $slide->bg_color ?>" />
                                        </div>                                          
                                    </div>
                                    <div class="row">
                                        <label class="field-label"><span style="color: #aaa; font-size: 12px;">-</span></label>
                                        <?php echo showBgSizeOptions('css[' . $slide->ID . '][slide][background-size]', $css['slide']['background-size']) ?>
                                        <?php echo showBgRepeatOptions('css[' . $slide->ID  . '][slide][background-repeat]', $css['slide']['background-repeat']) ?>
                                    </div>               
                                    <div class="row">
                                        <label class="field-label"><span style="color: #aaa; font-size: 12px;">-</span></label>
                                    </div>

                                    <div class="row">
                                        <label class="field-label">Layout</label>
                                        <div class="slide-layout toggle-btn-grp">
                                            <label onclick="" class="toggle-btn <?php echo ($slide->layout == 1) ? 'success' : '' ?>">
                                                <input value="1" type="radio" name="slide[<?php echo $slide->ID ?>][layout]" 
                                                    <?php echo ($slide->layout == 1) ? 'checked="checked"' : '' ?> />
                                                <img src="<?php echo ORIGO_SLIDER_ADMIN_URL ?>/includes/images/template1.jpg" /> 
                                            </label>
                                            <label onclick="" class="toggle-btn <?php echo ($slide->layout == 2) ? 'success' : '' ?>">
                                                <input value="2" type="radio" name="slide[<?php echo $slide->ID ?>][layout]" 
                                                    <?php echo ($slide->layout == 2) ? 'checked="checked"' : '' ?> />
                                                <img src="<?php echo ORIGO_SLIDER_ADMIN_URL ?>/includes/images/template4.jpg" />
                                            </label>
                                            <label onclick="" class="toggle-btn <?php echo ($slide->layout == 3) ? 'success' : '' ?>">
                                                <input value="3" type="radio" name="slide[<?php echo $slide->ID ?>][layout]" 
                                                    <?php echo ($slide->layout == 3) ? 'checked="checked"' : '' ?> />
                                                <img src="<?php echo ORIGO_SLIDER_ADMIN_URL ?>/includes/images/template5.jpg" />
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row slide-content">
                                        <label class="field-label">Content 1</label>
                                        <textarea name="slide[<?php echo $slide->ID ?>][content1]"><?php echo stripslashes($slide->content1) ?></textarea>
                                    </div>
                                    <div class="content1-css">
                                        <div class="row">
                                            <label class="field-label">Text</label>
                                            <div class="color-picker2">
                                                <label style="display: none" class="field-label">Color</label>
                                                <input name="css[<?php echo $slide->ID ?>][content1][color]" class="wp-color-picker2"  value="<?php echo $css['content1']['color'] ?>" />
                                            </div>               
                                            <?php echo showFontFamilyOptions('css[' . $slide->ID . '][content1][font-family]', $css['content1']['font-family']) ?>
                                            <input title="font size" name="css[<?php echo $slide->ID ?>][content1][font-size]" type="text" value="<?php echo $css['content1']['font-size'] ?>" size="3"/> px                                        
                                        </div>
                                        <div class="row">
                                            <label class="field-label">Border</label>
                                            <div class="color-picker2">
                                                <label style="display: none" class="field-label">Color</label>
                                                <input name="css[<?php echo $slide->ID ?>][content1][border-color]" class="wp-color-picker2"  value="<?php echo $css['content1']['border-color'] ?>" />
                                            </div>                                         
                                            <?php echo showBorderStyleOptions('css[' . $slide->ID . '][content1][border-style]', $css['content1']['border-style']) ?>                                            
                                            <input title="Border Width" name="css[<?php echo $slide->ID ?>][content1][border-width]" type="text" value="<?php echo $css['content1']['border-width'] ?>" size="3"/> px                                          
                                            <input title="Border Radius" name="css[<?php echo $slide->ID ?>][content1][border-radius]" type="text" value="<?php echo $css['content1']['border-radius'] ?>" size="3"/> px 
                                        </div>
                                        <div class="row">
                                            <label class="field-label">Background</label>                                        
                                            <div class="color-picker2">
                                                <label style="display: none" class="field-label">Color</label>
                                                <input name="css[<?php echo $slide->ID ?>][content1][box-color]" class="wp-color-picker2"  value="<?php echo $css['content1']['box-color'] ?>" />
                                            </div>                                        
                                            <input title="Opacity" name="css[<?php echo $slide->ID ?>][content1][box-opacity]" type="text" value="<?php echo $css['content1']['box-opacity'] ?>" size="3"/><em>Set to 0 if you want to hide this section</em>
                                        </div>
                                        <div class="row">
                                            <label class="field-label">Dimension</label>                                                                                                    
                                            <input title="Width" name="css[<?php echo $slide->ID ?>][content1][width]" type="text" value="<?php echo $css['content1']['width'] ?>" size="3"/> px
                                            <input title="Height" name="css[<?php echo $slide->ID ?>][content1][height]" type="text" value="<?php echo $css['content1']['height'] ?>" size="3"/> px
                                        </div>
                                    </div>
                                    <div class="row slide-content">
                                        <label class="field-label">Content 2</label>
                                        <textarea name="slide[<?php echo $slide->ID ?>][content2]"><?php echo stripslashes($slide->content2) ?></textarea>
                                    </div>
                                    <div class="content1-css">
                                        <div class="row">
                                            <label class="field-label">Text</label>
                                            <div class="color-picker2">
                                                <label style="display: none" class="field-label">Color</label>
                                                <input name="css[<?php echo $slide->ID ?>][content2][color]" class="wp-color-picker2"  value="<?php echo $css['content2']['color'] ?>" />
                                            </div>               
                                            <?php echo showFontFamilyOptions('css[' . $slide->ID . '][content2][font-family]', $css['content2']['font-family']) ?>
                                            <input title="font size" name="css[<?php echo $slide->ID ?>][content2][font-size]" type="text" value="<?php echo $css['content2']['font-size'] ?>" size="3"/> px                                        
                                        </div>
                                        <div class="row">
                                            <label class="field-label">Border</label>
                                            <div class="color-picker2">
                                                <label style="display: none" class="field-label">Color</label>
                                                <input name="css[<?php echo $slide->ID ?>][content2][border-color]" class="wp-color-picker2"  value="<?php echo $css['content2']['border-color'] ?>" />
                                            </div>                                         
                                            <?php echo showBorderStyleOptions('css[' . $slide->ID . '][content2][border-style]', $css['content2']['border-style']) ?>                                            
                                            <input title="Border Width" name="css[<?php echo $slide->ID ?>][content2][border-width]" type="text" value="<?php echo $css['content2']['border-width'] ?>" size="3"/> px                                          
                                            <input title="Border Radius" name="css[<?php echo $slide->ID ?>][content2][border-radius]" type="text" value="<?php echo $css['content2']['border-radius'] ?>" size="3"/> px 
                                        </div>
                                        <div class="row">
                                            <label class="field-label">Background</label>                                        
                                            <div class="color-picker2">
                                                <label style="display: none" class="field-label">Color</label>
                                                <input name="css[<?php echo $slide->ID ?>][content2][box-color]" class="wp-color-picker2"  value="<?php echo $css['content2']['box-color'] ?>" />
                                            </div>                                        
                                            <input title="Opacity" name="css[<?php echo $slide->ID ?>][content2][box-opacity]" type="text" value="<?php echo $css['content2']['box-opacity'] ?>" size="3"/><em>Set to 0 if you want to hide this section</em>
                                        </div>
                                        <div class="row">
                                            <label class="field-label">Dimension</label>                                                                                                    
                                            <input title="Width" name="css[<?php echo $slide->ID ?>][content2][width]" type="text" value="<?php echo $css['content2']['width'] ?>" size="3"/> px
                                            <input title="Height" name="css[<?php echo $slide->ID ?>][content2][height]" type="text" value="<?php echo $css['content2']['height'] ?>" size="3"/> px
                                        </div>
                                    </div>                                    
                                </div>
                            </div>

                            <input type="hidden" name="slide-order[]" value="<?php echo $slide->ID ?>" />
                            <input type="hidden" name="slide[<?php echo $slide->ID ?>][guid]" value="<?php echo $slide->guid ?>" />
                            <input type="hidden" name="slide[<?php echo $slide->ID ?>][parent-id]" value="<?php echo $slide->parent_id ?>" />
                            <input type="hidden" name="slide[<?php echo $slide->ID ?>][ID]" value="<?php echo $slide->ID ?>" />

                            <div class="item-actions">
                                <input type="button" class="slide-edit button" name="slide-edit" id="<?php echo $slide_ctr ?>" value="Edit" />
                                <a href="#" class="delete-slide button">x</a>
                            </div> 