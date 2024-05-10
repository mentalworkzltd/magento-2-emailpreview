define([
    'jquery',
    'mage/url',
    "mage/translate",
    "Magento_Ui/js/modal/modal",
    'jquery/ui',
], function ($, url, $t, modal) {
    'use strict';

    $.widget('mwz.emailPreview', {

        options: {
            template_id: null,
            ajax_url: '',
            message_selector: '.message-wrapper',
            template_details_selector: '.template-details',
            load_template_selector: '#templateSelector',
            form_selector: 'form#previewsend_form',
            preview_iframe_selector: 'iframe#preview_iframe',
            preview_toolbar_selector: '#preview_toolbar'
        },

        _create: function (config, element) {
            let _self = this;

            _self._initEvents();
            _self._manageIframeContent();

            // If template id set, init preview
            if(_self.options.template_id) {
                _self._ajaxCall();
            }
        },

        /**
         * Preview form events
         *
         * @private
         */
        _initEvents(){
            let _self = this;

            $('#sendtest', _self.options.form_selector).on('click', function (evt) {
                evt.preventDefault();
                if(!_self._validateForm()){
                    return;
                }
                _self._manageTemplateDetails('-','-');
                _self._manageIframeContent('');
                _self._ajaxCall();
            });

            $('.templateFields select', _self.options.form_selector).on('change', function (){
                let templateId = $(this).val();
                let templateType = '';
                switch($(this).attr('name')){
                    case 'configTemplate':
                        templateType = 'config';
                        $('select[name=customTemplate]').val('');
                        break;
                    case 'customTemplate':
                        templateType = 'custom';
                        $('select[name=configTemplate]').val('');
                        break;
                    default:
                        templateId = '';
                }
                $('input[name=template_id]', _self.options.form_selector).val(templateId);
                $('input[name=template_type]', _self.options.form_selector).val((templateId) ? templateType : '');
            });

            $('select[name="entity_type"]', _self.options.form_selector).on('change', function () {
                let placeholder = $(this).val() == 'customer' ? $t('Enter Customer ID') : $t('Enter Increment ID');
                $('input[name="entity_id"]', _self.options.form_selector).attr('placeholder', placeholder);
            });
        },

        /**
         * Manage the preview iframe content
         *
         * @param emailHtml
         * @private
         */
        _manageIframeContent: function (emailHtml) {
            let _self = this;
            if(emailHtml){
                $(_self.options.preview_iframe_selector).contents().find('html').html(emailHtml);
                let contentsHeight = $(_self.options.preview_iframe_selector).contents().find("html").height();
                $(_self.options.preview_iframe_selector).height(contentsHeight);
            }else{
                let iframeContentMessage = $t('No email template loaded.');
                if(parseInt(this.options.template_id)){
                    iframeContentMessage = $t('Loading template, please wait...');
                }
                $(this.options.preview_iframe_selector).contents().find('html').html("<i style='display: block;text-align: center;margin: 20px auto;width: 100%;'>" + iframeContentMessage + "</i>");
            }
        },

        /**
         * Manage loaded template details top of the preview pane
         *
         * @param templateId
         * @param templateLabel
         * @private
         */
        _manageTemplateDetails: function (templateId, templateLabel) {
            $('.template_id', this.options.template_details_selector ).html(templateId);
            $('.template_code', this.options.template_details_selector ).html(templateLabel);
        },

        _ajaxCall: function () {
            let _self = this;

            let url = _self.options.ajax_url;
            let formData = $(_self.options.form_selector).serialize();

            $.ajax({
                type: 'POST',
                showLoader: true,
                url: url,
                data: formData,
                success: function (response) {
                    try {
                        _self._manageMessages(response.messages);

                        if (response.template_id) {
                            _self.options.template_id = response.template_id;
                            _self._manageTemplateDetails(response.template_id, response.template_code);
                            _self._manageIframeContent(response.html);

                            return;
                        }
                        _self._reset();

                    }catch(error){
                        _self._reset('An error ocurred with the request.');
                        console.log("Mentalworkz Email Preview Error");
                        console.log(error);
                    }
                },
                error: function (request, error) {
                    _self._reset('An error ocurred with the request.');
                    console.log("Mentalworkz Email Preview Error");
                    console.log(error);
                }
            });
        },

        /**
         * Manage user message output
         *
         * @param messages
         * @private
         */
        _manageMessages: function (messages) {
            let _self = this;

            $.each(messages, (index, _messages) => {
                $.each(_messages, (type, arr_messages) => {
                    $.each(arr_messages, (_index, message) => {
                        $(_self.options.message_selector).append('<p class="' + type + '"><span>' + $t(message) + '</span></p>');
                    });
                });
            });

            $("html, body").animate({scrollTop: $(_self.options.message_selector).offset().top}, 600);
            $(_self.options.message_selector).slideDown('fast', function (){
                setTimeout(function () {
                    $(_self.options.message_selector).slideUp();
                    $(_self.options.message_selector + ' p').remove();
                }, 5000);
            });
        },

        /**
         * Reset the form
         *
         * @param message
         * @private
         */
        _reset: function (message) {
            let _self = this;

            if(typeof message !== "undefined"){
                _self._manageMessages([{'error' : [message]}]);
            }
            _self.options.template_id = '';
            $('input[name=template_id]', _self.options.form_selector).val('');
            $('select[name=configTemplate]', _self.options.form_selector).val('');
            $('select[name=customTemplate]', _self.options.form_selector).val('');
            _self._manageIframeContent('');
        },

        /**
         * Validate the form before send ajax request
         *
         * @returns {boolean}
         * @private
         */
        _validateForm: function () {
            let _self = this;

            // Validate email template ID
            if(!$('input[name=template_id]', _self.options.form_selector).val()){
                _self._manageMessages([{'error' : ['Please select an email template']}]);
                return false;
            }

            // Validate entity/ID
            if($('input[name=entity_id]', _self.options.form_selector).val() && !$('select[name=entity_type]', _self.options.form_selector).val()){
                _self._manageMessages([{'error' : ['Please select an entity type for the specified entity ID']}]);
                return false;
            }

            // Validate email addresses
            let emailError = false;
            if($('input[name=email_address]', _self.options.form_selector).val()){
                let emailAddresses = $('input[name=email_address]', _self.options.form_selector).val().split(',');
                $.each(emailAddresses, function (index, emailAddress) {
                    if(!emailError) {
                        if (!_self._validateEmail(emailAddress.trim())) {
                            emailError = true;
                            _self._manageMessages([{'error' : [$t('Invalid email address found:') + ' ' + emailAddress]}]);
                        }
                    }
                });
            }

            return (emailError) ? false : true;
        },

        _validateEmail: function (emailAddress) {
            var regex = /\S+@\S+\.\S+/;
            return regex.test(emailAddress);
        },

    });

    return $.mwz.emailPreview;

});