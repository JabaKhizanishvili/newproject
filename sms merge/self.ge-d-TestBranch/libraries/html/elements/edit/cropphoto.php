<?php

/**
 * @version		$Id: list.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework
/**
 * Renders a list element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementCropPhoto extends JElement
{
    /**
     * Element type
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'CropPhoto';

    public function fetchElement( $name, $value, $node, $control_name )
    {

        $cropType = $node->attributes('crop-type') ? $node->attributes('crop-type') : 'square';
        $width = $node->attributes('width') ? $node->attributes('width') : '500';
        $height = $node->attributes('height') ? $node->attributes('height') : '500';
        $quality = $node->attributes('high-quality') !== null ? $node->attributes('high-quality') : 1;
        $viewPortWidth = $node->attributes('viewport-width') ? $node->attributes('viewport-width') : '350';
        $viewPortHeight = $node->attributes('viewport-height') ? $node->attributes('viewport-height') : '350';
        $boundaryWidth = $node->attributes('boundary-width') ? $node->attributes('boundary-width') : '400';
        $boundaryHeight = $node->attributes('boundary-height') ? $node->attributes('boundary-height') : '400';
        $responsiveViewportWidth = $node->attributes('responsive-viewport-width') ? $node->attributes('responsive-viewport-width') : '250';
        $responsiveViewportHeight = $node->attributes('responsive-viewport-height') ? $node->attributes('responsive-viewport-height') : '250';
        $responsiveBoundaryWidth = $node->attributes('responsive-boundary-width') ? $node->attributes('responsive-boundary-width') : '300';
        $responsiveBoundaryHeight = $node->attributes('responsive-boundary-height') ? $node->attributes('responsive-boundary-height') : '300';

        $description = XTranslate::_( 'დასაშვებია მხოლოდ სურათის (.jpg, .jpeg, .png) ფორმატის ფაილის ატვირთვა' );
        if ( preg_match( '/^data\:/i', $value ) )
        {
            $Img = $value;
        }
        else
        {
            $Img = Helper::ImgToBase64( PATH_UPLOAD . DS . $value );
        }

        $style = '';

        if (!$Img) {
            $style = 'display:none';
        }

        $src = '<img id="profile-pic" alt="" src="' . $Img . '" style="' . $style . '" />';
        $id = $control_name . $name;
        $html = ''
            . '<div class="col-md-12 nopadding">'
            . '<div class="col-md-6 container-fluid">'
            . '<div class="col-12 nopadding">'
            . '<span class="btn btn-success btn-file">' . Text::_( 'Browse Image...' )
            . '<i class="bi bi-camera btn-ico"></i>'
            . '<input type="file" id="' . $id . '" class="form-control-file file-to-crop" />'
            . '</span>'
            . '</div>'
            . '<div class="row col-md-12 nopadding">'
            . '<div class="col-md-8 nopadding">'
            . '<div class="text-center imgdiv1">'
            . '<div id="' . $id . 'Preview" class="uploadblock">'
            . '<div class="rightDiagLine"></div>'
            . $src
            . '<div class="leftDiagLine"></div>'
            . '</div>'
            . '</div>'
            . '<div class="cls"></div>'
            . '<div class="text-center">'
            . '<input type="hidden" name="' . $control_name . '[' . $name . ']' . '" id="' . $id . '-data" value="' . $Img . '" />'
            . '</div>'
            . '</div>'
            . '<div class="col-md-4 nopadding">'
            . '<button class="btn btn-danger imgdiv1_clear" type="button" onclick="resetCropPhoto(\'' . $id . '\');">' . Text::_( 'Clear' ) . '</button>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '<div class="col-md-6">'
            . '<div class="form_desc red_desc_parent">'
            . '<i class="bi bi-exclamation-lg exclamation-ico"></i>'
            . '<span class="form_param_desc red_desc">'
            . $description
            . '</span>'
            . '</div>'
            . '</div>'
            . '</div>'
        ;

        $js = "
                function readURL(input) {
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $('#crop-preview').attr('src', e.target.result);
                           
                            var sizes = {
                                viewport: {
                                    width: " . $viewPortWidth . ",
                                    height: " . $viewPortHeight . "
                                },
                                boundary: {
                                    width: " . $boundaryWidth . ",
                                    height: " . $boundaryHeight . "
                                }
                            }
                            
                            if (screen.width <= 470) {
                                sizes.viewport.width = " . $responsiveViewportWidth . ";
                                sizes.viewport.height = " . $responsiveViewportHeight . ";
                                sizes.boundary.width = " . $responsiveBoundaryWidth . ";
                                sizes.boundary.height = " . $responsiveBoundaryHeight . ";
                            }
                            
                            sizes.viewport.type = '" . $cropType . "';
                            
                            var resize = new Croppie($('#crop-preview')[0], {
                                 viewport: sizes.viewport,
                                 boundary: sizes.boundary,
                                 showZoomer:false,
                                 enforceBoundary: false,
                                 quality: " . $quality . ",
                            });
                            $('#cropImageBtn').on('click', function() {
                                var type = 'base64';
                                var size = {
                                    width: " . $width . ",
                                    height: " . $height . ",
                                }
                                
                                resize.result({type, size}).then(function(dataImg) {
                                    //var data = [{ image: dataImg }, { name: 'myimgage.jpg' }];
                                    const dialog = document.querySelector('#imageCropModal');
                                    dialog.close();
                                    $('#profile-pic').attr('src', dataImg).css({'display': 'block'});
                                    $('.rightDiagLine').css({'display': 'none'});
                                    $('.leftDiagLine').css({'display': 'none'});
                                    $('#crop-preview').attr('src', '').removeAttr('class');
                                    var myImageHtml = $('#crop-preview').clone();
                                    $('#imageCropModal .modal-body').html(myImageHtml);
                                    $('#" . $id . "-data').attr('value', dataImg);
                                })
                            })
                        }
                        reader.readAsDataURL(input.files[0]);
                    }
                }

                $('.file-to-crop').change(function() {
                    const dialog = document.querySelector('#imageCropModal');
                    dialog.showModal();
                    readURL(this);
                });";

        Helper::SetJS($js);

        $js = '
            var modal = `<dialog id="imageCropModal" class="">
                    <header>
                        <h2>' . Text::_('Crop Image') . '</h2>
                    </header>
                    <div class="modal-body">
                        <img id="crop-preview" src="" alt="" />
                    </div>
                    <footer style="padding: 9px;text-align: right;">
                        <button id="cropImageBtn" type="submit" class="btn btn-primary">' . Text::_('Apply') . '</button>
                        <button id="cropImageCancelBtn" type="submit" class="btn btn-primary">' . Text::_('Cancel') . '</button>
                    </footer>
                </dialog>`;
						
		$("body").append(modal);				
        
        $("#cropImageCancelBtn").click(function() {
            const dialog = document.querySelector("#imageCropModal");
            dialog.close();
            var myImageHtml = $("#crop-preview").clone();
            $("#imageCropModal .modal-body").html(myImageHtml);
        });
        
        ';

        Helper::SetJS($js);

        return $html;

    }

}
