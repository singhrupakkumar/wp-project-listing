"use strict";
(function ($) {
	if ($('#citadela-layouts-root').length) {
		$('.citadela-import-layout').on('click', function () {
			fadeFromTo('#citadela-layouts-root', '#citadela-layouts-progress')
			$.post($(this).attr('href'), {}, (response) => {
				if (response.success) {
					Importer.setUrls(response.data.urls).run(response.data.requirements ? response.data.requirements : null)
				} else {
					fadeFromTo('#uploader-progress', '#uploader-error')
					$('#uploader-error-title').text(response.data.title)
					$('#uploader-error-msg').html(response.data.message)
					$('#uploader-error button').on('click', () => {
						location.reload()
					})
				}
			})
			return false
		});
	} else {
		var Uploader = {
			init() {
				let self = this
				this.wpUploaderOptions = _.extend({
					init() {
						if (!this.supports.dragdrop) {
							$('#uploader').removeClass('drag-drop')
						}
						$('#uploader-error button').on('click', () => {
							fadeFromTo('#uploader-error', '#uploader-selector', () => self.reset())
						})
					},
					added(attachment) {
						fadeFromTo('#uploader-selector', '#uploader-progress', () => self.onProgress(attachment))
					},
					progress(attachment) {
						self.onProgress(attachment)
					},
					success(attachment) {
						self.poltergeistButton('hide')
						Importer.setUrls(attachment.get('urls')).run()
					},
					error(message, data, file) {
						self.onError(data)
					},
					browser: $('#uploader-browse-button'),
					dropzone: $('#uploader'),
				}, _citadelaProPluploadOptions)
				new wp.Uploader(this.wpUploaderOptions)
			},
			onProgress(attachment) {
				$('#uploader-progress code').text(attachment.attributes.filename)
				$('#uploader-progress progress').val(attachment.attributes.percent)
			},
			onError(data) {
				$('#uploader-error-title').text(data.title)
				$('#uploader-error-msg').html(data.message)
				fadeFromTo('#uploader-progress', '#uploader-error', () => this.poltergeistButton('hide'))
			},
			reset() {
				this.poltergeistButton('show')
				$('#uploader-selector').show()
				$('#uploader-progress code').empty()
				$('#uploader-progress progress').val(0)
				$('#uploader-progress').hide()
				$('#uploader-error-title').empty()
				$('#uploader-error-msg').empty()
			},
			poltergeistButton(op) {
				// Plupload's shim to convert input[type=button] into input[type=file] and position it absolute over the button
				$('.moxie-shim.moxie-shim-html5')[op]()
			},
			startOverFrom(from) {
				this.reset()
				fadeFromTo(from, '#uploader')
			}
		}
		$(function () {
			Uploader.init()
		})
	}
	var Importer = {
		init() {
			this.$icons = {
				'idle': $('#indicator-icon-idle'),
				'wip': $('#indicator-icon-wip'),
				'done': $('#indicator-icon-done'),
				'dieded': $('#indicator-icon-dieded')
			}
			$('#import-confirm-requirements').on('click', this.onConfirmRequirements.bind(this))
			$('#import-cancel-requirements').on('click', this.onCancel.bind(this))
			$('#import-confirm').on('click', this.onConfirm.bind(this))
			$('#import-cancel').on('click', this.onCancel.bind(this))
		},
		onConfirmRequirements() {
			fadeFromTo('#import-confirmation-requirements', '#import-requirements-install', () => {
				$.post(this.urls.requirements, {}, (response) => {
					if (response.success) {
						fadeFromTo('#import-requirements-install', '#import-confirmation')
					} else {
						fadeFromTo('#import-requirements-install-progress', '#import-requirements-install-error')
						$('#import-requirements-install-error-msg').html(response.data.message)
						$('#import-requirements-install-error button').on('click', () => {
							location.reload()
						})
					}
				})
			})
		},
		onConfirm() {
			fadeFromTo('#import-confirmation', '#import-progress', () => this.start())
		},
		onCancel() {
			location.reload()
		},
		setUrls(urls) {
			this.urls = urls
			return this
		},
		run(requirements) {
			this.resetProgress()
			if (requirements && (requirements.install.length || requirements.activate.length)) {
				var plugins = {
					'aitthemes': [],
					'wporg': []
				}
				requirements.install.forEach((plugin) => {
					plugins[plugin.source].push(plugin.name)
				})
				requirements.activate.forEach((plugin) => {
					plugins[plugin.source].push(plugin.name)
				})
				if (plugins.aitthemes.length) {
					$('#import-requirements-aitthemes').removeClass('hidden')
					$('#import-requirements-aitthemes strong').text(plugins.aitthemes.join(', '));
				}
				if (plugins.wporg.length) {
					$('#import-requirements-wporg').removeClass('hidden')
					$('#import-requirements-wporg strong').text(plugins.wporg.join(', '));
				}
				fadeFromTo('#uploader', '#import-confirmation-requirements')
			} else {
				fadeFromTo('#uploader', '#import-confirmation')
			}
		},
		start() {
			this.import('content', () => {
				this.import('options', () => {
					this.import('images', () => {
						this.complete()
					})
				})
			})
		},
		import(watwat, next) {
			this.state(watwat, 'wip')
			$.post(this.urls[watwat], {}, (response) => {
				if (response.success) {
					this.state(watwat, 'done')
					next()
				} else {
					this.state(watwat, 'dieded', response.data.message)
				}
			})
		},
		complete() {
			$.post(this.urls.complete, {}, (response) => {
				fadeFromTo('#import-progress', '#import-complete')
			})
		},
		state(watwat, state, msg) {
			$('#import-indicator-' + watwat).removeClass('idle wip done dieded').addClass(state).find('.indicator-icon').empty().append(this.$icons[state].clone())
			if (state === 'dieded') {
				$('#import-error').text(msg).fadeIn(200)
			}
		},
		resetProgress() {
			['content', 'options', 'images'].forEach((watwat) => {
				$('#import-indicator-' + watwat).removeClass('wip done dieded').addClass('idle').find('.indicator-icon').empty().append(this.$icons['idle'].clone())
			})
			$('#import-error').empty().hide()
			$('#import-complete').hide()
			$('#import-progress').hide()
		}
	}
	Importer.init()
	function fadeFromTo(from, to, cb) {
		$(from).fadeOut(100, function () {
			if (cb) cb()
			$(to).fadeIn(200)
		})
	}
})(jQuery);