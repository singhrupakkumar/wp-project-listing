"use strict"

;( function( $, undefined ) {

	var Fields = {

		bind: function() {
			this.$context = $('.citadela-pro-settings #wpbody')
			this.codeEditor()
			this.datetimePicker()
		},


		codeEditor: function() {
			var $fields = $('.field-type-code-editor', this.$context)

			var settings = $.extend( {}, wp.codeEditor.defaultSettings )
			settings.codemirror = $.extend( {}, settings.codemirror, {
				indentUnit: 2,
				tabSize: 2,
			} )

			$fields.each( function() {
				var $field = $(this)
				var config = $field.data('ctdl-pro-field-config')
				settings.codemirror.mode = config.mode
				var editorInstance = wp.codeEditor.initialize( $field, settings )
				// store codemirror instance in data prop, so it can be accessed later if needed
				$field.data( 'codemirror', editorInstance.codemirror )
			} )
		},


		datetimePicker: function() {
			var $fields = $('.field-type-datetime', this.$context)

			$fields.each( function() {
				var $field = $(this)
				var config = $field.data('ctdl-pro-field-config')
				flatpickr.l10ns[config.locale].firstDayOfWeek = config.startOfWeek
				$field.flatpickr( {
					noCalendar     : ! config.hasDate,
					enableTime     : config.hasTime,
					minuteIncrement: 1,
					dateFormat     : "Y-m-d H:i",
					time_24hr      : config.timeAs24hr,
					locale         : config.locale,
				} )
				$field.next( '.field-type-datetime-clear' ).on( 'click', function() {
					$field[0]._flatpickr.clear()
				} )
			} )
		}

	}

	$( function() {
		Fields.bind()
	} )

} )( window.jQuery );
