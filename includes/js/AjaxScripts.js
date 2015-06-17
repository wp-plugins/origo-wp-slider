    $jx = jQuery.noConflict();  
    
    $jx(document).ready(function(){
        
        $jx('.delete-slide').live('click', function(){
            id = $jx(this).parents('li').data('slide_id');
            
            delete_slides = $jx('input[name="delete_slides"]').val();
            if (delete_slides != '') {
                new_delete_slides = delete_slides + ',' + id;
            }
            else {
                new_delete_slides = id;
            }
            $jx('input[name="delete_slides"]').val(new_delete_slides);
            $jx(this).parents('li').remove();
            return false;
        });
        
        $jx('form[name="add_slider"]').submit(function(){
            form = $jx(this);
            $jx.ajax({
                url:ajaxurl,
                dataType: "json",
                type:'POST',
                data:'action=add-origo-slider&' + form.serialize(),
                success:function(result){
                    $jx('#the_sliders').append(result.html);
                }
            });
            return false;
        });
        
        $jx('form[name="origo_slider"]').submit(function(){
            $form = $jx(this);
            $jx.ajax({
                url:ajaxurl,
                dataType: "json",
                type:'POST',
                data:'action=save-origo-slider&' + $form.serialize(),
                success:function(result){
                    $jx('input.is-new').val(0);
                    $jx('#notice span').html('Changes saved!');
                    $jx('#notice').css('display', 'block');
                    setTimeout(function() {
                        $jx('#notice').fadeOut('fast');
                    }, 2000); //                    
                }
            });
            return false;
        });   
        
        $jx('.new-slide').click(function(){
            $jx.ajax({
                url:ajaxurl,
                dataType: "json",
                type:'POST',
                data:'action=save-origo-get-slide',
                success:function(result){
                    $jx('.slider .rows').append(result.slide);
                    $jx('.wp-color-picker2').wpColorPicker();
                }
            });
            return false;
        }); 
        
        $jx('.delete_slider').click(function(){
            if (confirm('Are you sure you want to delete this slider?')) {
                id = $jx(this).data('id');
                $jx.ajax({
                    url:ajaxurl,
                    dataType: "json",
                    type:'POST',
                    data:'action=delete_origo_slider&id=' + id,
                    success:function(result){    
                        $jx('.slider_' + result.deleted_slider_id).slideUp('slow');
                    }
                });
            }
            return false;
        });
    });  