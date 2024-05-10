define([
    'jquery',
    'jquery/ui',
], function ($) {
    'use strict';

    $.widget('mwz.previewToolbar', {

        options: {
            toolbar_selector: '#preview_toolbar',
            iframeresize_selector: '#iframeResizer',
            device_selector: 'select[name=responsive]',
            preview_current: {
                width: 1440,
                height: 800,
                orientation: 'portrait'
            },
            preview_adjustment: {
                height: 17,
                width: 17,
                resizebar_width: 25
            }
        },

        _create: function (config, element) {
            this._initEvents();
            this._initResizeablePreviewWindow();
            this._updateFrameResizer(this.options.preview_current.width, this.options.preview_current.height);
            this._manageResponsiveSelect(this.options.preview_current.width, this.options.preview_current.height);
        },

        /**
         * Init common events
         *
         * @private
         */
        _initEvents(){
            let _self = this;

            /**
             * Responsive device select event
             */
            $(_self.options.device_selector, _self.options.toolbar_selector).on('change', function () {
                let size = $(this).val();
                if(size) {
                    let sizeParts = size.split('x');
                    sizeParts = (_self.options.preview_current.orientation === 'landscape') ? sizeParts.reverse() : sizeParts;
                    _self._manageSizeUpdate(sizeParts[0], sizeParts[1]);
                }
            });

            /**
             * Height/Width device size input edit event
             */
            $('.sizes input', _self.options.toolbar_selector).on('blur', function () {
                let name = $(this).attr('name');
                if($(this).val()){
                    _self._manageSizeUpdate($('.sizes input[name=width]', _self.options.toolbar_selector).val(),
                        $('.sizes input[name=height]', _self.options.toolbar_selector).val());
                }else{
                    $(this).val(_self.options.preview_current[name]);
                }
            });

            // Allow digits only, using a RegExp
            $('.sizes input', _self.options.toolbar_selector).inputFilter(function(value) {
                return /^\d*$/.test(value) && (!value || parseInt(value) < 3001);
            },"Only digits. Max 3000");

            /**
             * Device orientation button event
             */
            $('.orientation', _self.options.toolbar_selector).on('click', function (evt){
                evt.preventDefault();

                if(_self.options.preview_current.orientation === 'landscape'){
                    _self.options.preview_current.orientation = 'portrait';
                    $('img.landscape', this).hide();
                    $('img.portrait', this).show();
                }else{
                    _self.options.preview_current.orientation = 'landscape';
                    $('img.landscape', this).show();
                    $('img.portrait', this).hide();
                }

                let width = $('.sizes input[name=width]', _self.options.toolbar_selector).val();
                let height = $('.sizes input[name=height]', _self.options.toolbar_selector).val();
                _self._manageSizeUpdate(height, width);
            });

        },

        /**
         * Resizeable preview window
         * @private
         */
        _initResizeablePreviewWindow: function () {
            let _self = this;
            let resizeTimeout = null;
            $(_self.options.iframeresize_selector).resizable(
                {
                    handles: "e",
                    start: function(event, ui) {
                        $('iframe').css('pointer-events','none');
                    },
                    stop: function(event, ui) {
                        $('iframe').css('pointer-events','auto');
                    },
                    resize: function( event, ui ) {
                        _self._manageSizeUpdate($(_self.options.iframeresize_selector).width(), _self.options.preview_current.height);
                    }
                });
        },

        /**
         * If user edits height/width device size inputs, and size corresponds
         * to a device in the select list, select it.
         *
         * @param sizeVal
         * @private
         */
        _manageResponsiveSelect: function (width, height) {
            let optionValue = (this.options.preview_current.orientation === 'portrait') ? width + 'x' + height : height + 'x' + width;
            if($("option[value='" + optionValue + "']", this.options.device_selector).length > 0){
                $(this.options.device_selector).val(optionValue);
            }else{
                $(this.options.device_selector).val('');
            }
        },

        /**
         * Manage the updating of the preview window based on device selection/size input editing
         *
         * @param width
         * @param height
         * @private
         */
        _manageSizeUpdate: function (width, height) {
            let _self = this;

            width = width ? width : _self.options.preview_current.width;
            height = height ? height : _self.options.preview_current.height;

            _self.options.preview_current.width = width;
            _self.options.preview_current.height = height;
            $('.sizes input[name=width]', _self.options.toolbar_selector).val(width);
            $('.sizes input[name=height]', _self.options.toolbar_selector).val(height);

            _self._updateFrameResizer(width, height);
            _self._manageResponsiveSelect(width, height);
        },

        /**
         * Update the actual iframe resizer window
         *
         * @param width
         * @param height
         * @private
         */
        _updateFrameResizer: function (width, height) {
            width = parseInt(width) + this.options.preview_adjustment.width + this.options.preview_adjustment.resizebar_width;
            height = parseInt(height) + this.options.preview_adjustment.height;
            $(this.options.iframeresize_selector).css('width', width + 'px');
            $(this.options.iframeresize_selector).css('height', height + 'px');
        },

});

    return $.mwz.previewToolbar;
});

/**
 * Height/width size input validation/management plugin
 */
(function($) {
    $.fn.inputFilter = function(callback, errMsg) {
        return this.on("input keydown keyup mousedown mouseup select contextmenu drop focusout", function(e) {
            if (callback(this.value)) {
                // Accepted value
                if (["keydown","mousedown","focusout"].indexOf(e.type) >= 0){
                    $(this).removeClass("input-error");
                    this.setCustomValidity("");
                }
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                // Rejected value - restore the previous one
                $(this).addClass("input-error");
                this.setCustomValidity(errMsg);
                this.reportValidity();
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            } else {
                // Rejected value - nothing to restore
                this.value = "";
            }
        });
    };
}(jQuery));