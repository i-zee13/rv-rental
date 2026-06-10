/**
 * MV Admin — Dropify initializer (single + multiple image inputs)
 */
(function ($) {
    'use strict';

    var defaultMessages = {
        default: 'Drag & drop an image here or click to browse',
        replace: 'Drag & drop or click to replace',
        remove: 'Remove',
        error: 'Something went wrong with this file.'
    };

    function initDropify($root) {
        $root.find('input.dropify').not('.dropify-initialized').each(function () {
            var $input = $(this);
            var messages = $.extend({}, defaultMessages, {
                default: $input.data('default-msg') || defaultMessages.default
            });

            $input.dropify({
                messages: messages,
                error: {
                    fileSize: 'File is too large (max {{ value }}).',
                    minWidth: 'Image is too narrow (min {{ value }}px).',
                    maxWidth: 'Image is too wide (max {{ value }}px).',
                    minHeight: 'Image is too short (min {{ value }}px).',
                    maxHeight: 'Image is too tall (max {{ value }}px).',
                    imageFormat: 'Only images are allowed ({{ value }}).'
                }
            });

            $input.addClass('dropify-initialized');

            if ($input.hasClass('dropify-multiple') && $input.prop('multiple')) {
                $input.on('change', function () {
                    var count = this.files ? this.files.length : 0;
                    var $wrapper = $input.closest('.dropify-wrapper');
                    var $render = $wrapper.find('.dropify-preview .dropify-render');

                    if (count > 1) {
                        $render.html(
                            '<div class="dropify-multiple-count">' +
                            '<span>' + count + ' images selected</span></div>'
                        );
                        $wrapper.addClass('has-multiple-preview');
                    }
                });
            }
        });
    }

    function addRepeaterItem($group) {
        var $template = $group.find('[data-dropify-template]').first();
        if (!$template.length) return;

        var $clone = $template.clone().removeAttr('data-dropify-template').show();
        $clone.find('.dropify-wrapper').remove();
        $clone.find('input.dropify')
            .val('')
            .removeClass('dropify-initialized')
            .attr('id', 'dropify-' + Date.now());

        $group.append($clone);
        initDropify($clone);
    }

    $(function () {
        initDropify($(document));

        $(document).on('click', '[data-dropify-add]', function (e) {
            e.preventDefault();
            var selector = $(this).data('dropifyAdd');
            var $group = selector ? $(selector) : $(this).closest('[data-dropify-group]');
            addRepeaterItem($group);
        });
    });
})(jQuery);
