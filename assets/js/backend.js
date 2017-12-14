(function ($) {
	"use strict";

	$(document).ready(function () {
		flex_metabox.ready();
	});

	$(window).load(function () {
		flex_metabox.load();
	});

	var flex_metabox = window.$flex_metabox = {


		/**
		 * Call functions when document ready
		 */
		ready: function () {
			this.repeater_init();
			this.repeater_toggle();
		},

		/**
		 * Call functions when window load.
		 */
		load: function () {

		},


		repeater_toggle: function () {
			$('.type-repeater').on('click', '.btn-toggle', function (e) {
				e.preventDefault();
				var $repeater_item = $(this).parents('.repeater-item');
				$repeater_item.toggleClass('active');
			});
		},


		repeater_init: function () {
			var $sortable = $(".repeater-list");
			var repeater_option = $('.type-repeater').repeater({
				show: function () {
					flex_metabox.refresh_wpeditor(this);
					$sortable.sortable('refresh');
					$(this).slideDown().toggleClass('active');
				},
				hide: function (deleteElement) {
					if (confirm('Are you sure you want to delete this element?')) {
						$(this).slideUp(deleteElement);
					}
				},
			});


			$sortable.sortable({
				axis       : 'y',
				containment: "parent",
			});

			$sortable.disableSelection();

		},


		uniqueId: function () {
			// Math.random should be unique because of its seeding algorithm.
			// Convert it to base 36 (numbers + letters), and grab the first 9 characters
			// after the decimal.
			return 'flex_metabox' + Math.random().toString(36).substr(2, 9);
		},

		// CUSTOM FUNCTION IN BELOW

		refresh_wpeditor: function (element) {
			var $wrapper = $(element).find('.wp-editor-wrap');
			if ($($wrapper).length == 0) {
				return;
			}
			var originalId = str_replace('wp-', '', $wrapper.attr('id'));
			originalId = str_replace('-wrap', '', originalId);
			var id = flex_metabox.uniqueId();
			var settings = tinyMCEPreInit.mceInit[originalId];
			settings.selector = 'textarea#' + id;

			$wrapper.attr('id', 'wp-' + id + '-wrap')
				.removeClass('html-active').addClass('mce-active') // Active the visual mode by default
			//	.find('.mce-container').remove().end()               // Remove rendered tinyMCE editor
				.find('.wp-editor-tools').attr('id', 'wp-' + id + '-editor-tools')
				.find('.wp-media-buttons').attr('id', 'wp-' + id + '-media-buttons')
				.find('button').data('editor', id).attr('data-editor', id);

			// Editor tabs
			$wrapper.find('.switch-tmce')
				.attr('id', id + '-tmce')
				.data('wp-editor-id', id).attr('data-wp-editor-id', id).end()
				.find('.switch-html')
				.attr('id', id + '-html')
				.data('wp-editor-id', id).attr('data-wp-editor-id', id);

			// Quick tags
			$wrapper.find('.wp-editor-container').attr('id', 'wp-' + id + '-editor-container')
				.find('.quicktags-toolbar').attr('id', 'qt_' + id + '_toolbar').html('');

			$wrapper.find('.wp-editor-area').attr('id', id).show();
			tinymce.init(settings);

			// Quick tags
			if (typeof quicktags === 'function' && tinyMCEPreInit.qtInit.hasOwnProperty(originalId)) {
				var qtSettings = tinyMCEPreInit.qtInit[originalId];
				qtSettings.id = id;
				quicktags(qtSettings);
				QTags._buttonsInit();
			}

		},

	};

})(jQuery);