/*!
* jQuery.sliderCaptcha - Version(1.0.1)
* Author: Ammar Ali Khan (http://www.itizze.com)
* Date: Fri Jun 24 11:04:38 2011 +10:00 GMT 
*
* Examples and documentation at: http://docs.itizze.com/jquery-plugin/jquery.slidercaptcha/ 
*
* TERMS OF USE
* 
* Copyright © 2011 ITizze Corporation (http://www.itizze.com)
* All rights reserved.
* Dual licensed under the MIT and GPL licenses:
*   http://www.opensource.org/licenses/mit-license.php
*   http://www.gnu.org/licenses/gpl.html
* 
* Redistribution and use in source and binary forms, with or without modification, 
* are permitted provided that the following conditions are met:
* 
* Redistributions of source code must retain the above copyright notice, this list of 
* conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright notice, this list 
* of conditions and the following disclaimer in the documentation and/or other materials 
* provided with the distribution.
* 
* Neither the name of the author nor the names of contributors may be used to endorse 
* or promote products derived from this software without specific prior written permission.
* 
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY 
* EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
* MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
* COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
* EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
* GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED 
* AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
* NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
* OF THE POSSIBILITY OF SUCH DAMAGE. 
*
* USAGE
*
* jQuery.sliderCaptcha plugin give you an ability to add easy to use captcha to your forms.
* This plugin is very usefull to keep spammers away, this plugin lock (disabled) submit button till real human slide it to unlock (enable) it.
* This plugin has SALT value and Server Side Validation too.
*
* REQUIRED SKILL(S)
*
* *** >= Intermediate                  ***
*
* REQUIREMENT(S)
*
* *** jQuery >= 1.4                    *** http://docs.jquery.com/Downloading_jQuery
* *** jQuery UI                        *** http://jqueryui.com/download
* *** isjQuery Plugin                  *** http://library.itizze.com/script/jquery-plugin/jquery.isjquery/1.0.1/jquery.isjquery.min.js
* *** isjQueryUI Plugin                *** http://library.itizze.com/script/jquery-plugin/jquery.isjqueryui/1.0.1/jquery.isjqueryui.min.js
*
* USAGE RISTRICTION(S)
*
* This plugin has some design and syntax restrictions to work
* - This plugin require html form element and a submit button to integrate
* - This plugin insert captcha just before submit button, if you want to insert captcha some where else you need to require a place holder
*
* EXAMPLE(S)
*
* 1-    $(document).ready(function() {
*           $("#Form1").sliderCaptcha();
*       });
* 2-    $(document).ready(function() {
*           $("#Form1").sliderCaptcha({
*               saltValue: 1,
*               checkValue: 2,
*               placeHolder: '.myPlaceHolder'
*           });
*       });
*
* DEFAULT OPTION(S)
* inputValue: Set initial value of slider (Recommend to use default)
* saltValue: Set SALT value for Server Side Validation
* checkValue: Set value for Server Side Validation Check (inputValue + saltValue)
* errIcon: Validation icon if locked
* sucIcon: Validation Icon if unlocked
* errBackGrdColor: Validation background color when locked
* sucBackGrdColor: Validation background color when unlocked
* serverValidationField: Server side validator field name (use this in server side code) 
* placeHolder: Place holder css class name where you want to add captcha 
*
*/
(function($) {
    var methods = {
        init: function(options) {
            var defaults = {
                inputValue: 1,
                saltValue: 9,
                checkValue: 10,
                errIcon: 'http://library.itizze.com/images/Callouts/callout_validation.png',
                sucIcon: 'http://library.itizze.com/images/Callouts/callout_success.png',
                errBackGrdColor: '#ffccba',
                sucBackGrdColor: '#dff2bf',
                serverValidationField: 'sliderCaptchaServerValidator',
                placeHolder: '.sliderCaptchaPlaceHolder'
            };
            var options = $.extend(defaults, options);

            return this.each(function() {
                var opt = options;
                var obj = $(this);

                /* check if jQuery UI loaded */
                if (!$.isjQueryUI()) {
                    alert('jQuery.sliderCaptcha needs jQuery UI to work.');
                    return false;
                }
                
                /* check if binds with form element */
                if ($.isjQuery('1.6', '<')) {
                    if (obj.attr('tagName').toLowerCase() != 'form') {
                        alert('jQuery.sliderCaptcha plugin binds with form elements only');
                        return false;
                    }
                }
                else {
                    if (obj.prop('tagName').toLowerCase() != 'form') {
                        alert('jQuery.sliderCaptcha plugin binds with form elements only');
                        return false;
                    }
                }

                var submitButton = $('input:[type="submit"]:visible:enabled', obj);
                var placeHolder = $(opt.placeHolder, obj);
                /* insert slider captcha */
                if ($.isjQuery('1.6', '<')) {
                    if (placeHolder.attr('tagName') != undefined) { placeHolder.before(createUI(opt)); }
                    else { submitButton.before(createUI(opt)); }
                }
                else {
                    if (placeHolder.prop('tagName') != undefined) { placeHolder.before(createUI(opt)); }
                    else { submitButton.before(createUI(opt)); }
                }

                /* disable (lock) submit button */
                $(submitButton).attr('disabled', 'disabled');
                $(submitButton).attr('title', 'slide captcha slider to right to enable button');

                /* captcha field */
                $(placeHolder).parents('.sliderCaptchaField').css({ 'background': opt.errBackGrdColor + ' url(' + opt.errIcon + ') no-repeat 9px center' });

                /* add slider functionality */
                $('.sliderCaptchaSlider', obj).slider({
                    animate: true,
                    value: 0,
                    min: 0,
                    max: opt.inputValue,
                    step: opt.inputValue,
                    stop: function(event, ui) {
                        /* set value for server side valiation */
                        $('#' + opt.serverValidationField, obj).val(ui.value + opt.saltValue);

                        /* Check validation */
                        if ($('#' + opt.serverValidationField, obj).val() == opt.checkValue) {
                            /* change images */
                            $('#lockImg', obj).removeClass('sliderCaptchaLockedImage').addClass('sliderCaptchaLockedImageDisable');
                            $('#unlockImg', obj).removeClass('sliderCaptchaUnlockedImageDisable').addClass('sliderCaptchaUnlockedImage');
                            /* change status */
                            $('.sliderCaptchaStatus', obj).html('Unlocked');
                            /* enable (unlock) submit button */
                            $(submitButton).removeAttr('disabled');
                            $(submitButton).removeAttr('title');
                            /* captcha field */
                            $(placeHolder).parents('.sliderCaptchaField').css({ 'background': opt.sucBackGrdColor + ' url(' + opt.sucIcon + ') no-repeat 9px center' });
                        }
                        else {
                            /* change images */
                            $('#lockImg', obj).removeClass('sliderCaptchaLockedImageDisable').addClass('sliderCaptchaLockedImage');
                            $('#unlockImg', obj).removeClass('sliderCaptchaUnlockedImage').addClass('sliderCaptchaUnlockedImageDisable');
                            /* change status */
                            $('.sliderCaptchaStatus', obj).html('Locked');
                            /* disable (lock) submit button */
                            $(submitButton).attr('disabled', 'disabled');
                            $(submitButton).attr('title', 'slide captcha slider to right to enable button');
                            /* captcha field */
                            $(placeHolder).parents('.sliderCaptchaField').css({ 'background': opt.errBackGrdColor + ' url(' + opt.errIcon + ') no-repeat 9px center' });
                        }
                    }
                });

                /* reset slider captcha on form submit */
                obj.submit(function() {
                    /* reset slider to 0 (lock) */
                    $('.sliderCaptchaSlider', obj).slider('option', 'value', 0);
                    /* change images */
                    $('#lockImg', obj).removeClass('sliderCaptchaLockedImageDisable').addClass('sliderCaptchaLockedImage');
                    $('#unlockImg', obj).removeClass('sliderCaptchaUnlockedImage').addClass('sliderCaptchaUnlockedImageDisable');
                    /* change status */
                    $('.sliderCaptchaStatus', obj).html('Locked');
                    /* disable (lock) submit button */
                    $(submitButton).attr('disabled', 'disabled');
                    $(submitButton).attr('title', 'slide captcha slider to right to enable button');
                    /* captcha field */
                    $(placeHolder).parents('.sliderCaptchaField').css({ 'background': opt.errBackGrdColor + ' url(' + opt.errIcon + ') no-repeat 9px center' });
                });

                /* reset slider captcha on reset button click */
                $('input:[type="reset"]:visible:enabled', obj).click(function() {
                    /* reset slider to 0 (lock) */
                    $('.sliderCaptchaSlider', obj).slider('option', 'value', 0);
                    /* change images */
                    $('#lockImg', obj).removeClass('sliderCaptchaLockedImageDisable').addClass('sliderCaptchaLockedImage');
                    $('#unlockImg', obj).removeClass('sliderCaptchaUnlockedImage').addClass('sliderCaptchaUnlockedImageDisable');
                    /* change status */
                    $('.sliderCaptchaStatus', obj).html('Locked');
                    /* disable (lock) submit button */
                    $(submitButton).attr('disabled', 'disabled');
                    $(submitButton).attr('title', 'slide captcha slider to right to enable button');
                    /* captcha field */
                    $(placeHolder).parents('.sliderCaptchaField').css({ 'background': opt.errBackGrdColor + ' url(' + opt.errIcon + ') no-repeat 9px center' });
                });
            });
        }
    };

    // create UI function
    function createUI(opt) {
        var uiHTML = '';
        uiHTML += '<div class="sliderCaptchaContainer roundedCorner shadow">';
        uiHTML += '<div class="sliderCaptchaHeader"></div>';
        uiHTML += '<div class="sliderCaptchaSlider" title="slide it"></div>';
        uiHTML += '<div class="sliderCaptchaImages">';
        uiHTML += '<div id="lockImg" class="sliderCaptchaLockedImage"></div>';
        uiHTML += '<div id="unlockImg" class="sliderCaptchaUnlockedImageDisable"></div>';
        uiHTML += '<div class="sliderCaptchaStatus">Locked</div>';
        uiHTML += '</div></div>';
        uiHTML += '<input type="hidden" name="' + opt.serverValidationField + '" value="" id="' + opt.serverValidationField + '" />';
        return uiHTML;
    }

    $.fn.sliderCaptcha = function(method) {
        // Method calling logic
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        }
        else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        }
        else {
            alert('Method ' + method + ' does not exist on jQuery.sliderCaptcha');
        }
    };

})(jQuery);