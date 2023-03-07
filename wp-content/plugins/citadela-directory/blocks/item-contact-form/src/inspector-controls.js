/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component, Fragment } = wp.element;
const { TextControl, TextareaControl, SelectControl, BaseControl, PanelBody, ToggleControl } = wp.components;
const { URLInput, MediaUpload } = wp.blockEditor;


export default class ItemContactDetailsInspectorControls extends Component {

	render() {

		const { setAttributes } = this.props;
		const { 
			emailFromName,
			emailFromAddress,
			emailMessage,
			labelName,
			labelEmail,
			labelSubject,
			labelMessage,
			labelSendButton,
			helpName,
			helpEmail,
			helpSubject,
			helpMessage,
			notificationSuccess,
			notificationValidationError,
			notificationServerError,
		} = this.props.attributes; 	
		return (
			<Fragment>
				<PanelBody 
					title={__('Email settings', 'citadela-directory')}
					initialOpen={true}
					className="citadela-panel"
				>
					<BaseControl
						id={ 'emailFromName' }
						label={ __('Sender Name', 'citadela-directory') }
						help={ __('Name of sender displayed in received email.', 'citadela-directory') }
					>
						<TextControl
							id={ 'emailFromName' }	
							type={ 'text' }
							value={ emailFromName }
							onChange={ ( value ) => { setAttributes( { emailFromName: value } ) } }
						/>
					</BaseControl>
					<BaseControl
						id={ 'emailFromAddress' }
						label={ __('Sender Email', 'citadela-directory') }
						help={ __('Email used as sender in email, make sure the email address corresponds with your website domain.', 'citadela-directory') }
					>
						<TextControl
							id={ 'emailFromAddress' }	
							type={ 'text' }
							value={ emailFromAddress }
							onChange={ ( value ) => { setAttributes( { emailFromAddress: value } ) } }
						/>
					</BaseControl>
					<BaseControl
						id={ 'emailMessage' }
						label={ __('Email Message', 'citadela-directory') }
						help={ __('Text of email message. Available are variables: {user-name} {user-email} {user-message}', 'citadela-directory') }
					>
						<TextareaControl
							id={ 'emailMessage' }
							value={ emailMessage }
							onChange={ ( value ) => { setAttributes( { emailMessage: value } ) } }
						/>
					</BaseControl>
				</PanelBody>

				<PanelBody 
					title={__('Input labels', 'citadela-directory')}
					initialOpen={false}
					className="citadela-panel"
				>
					<BaseControl
						id={ 'labelName' }
						label={ __('Name Label', 'citadela-directory') }
						help={ __('Label used for Name input.', 'citadela-directory') }
					>
						<TextControl
							id={ 'labelName' }	
							type={ 'text' }
							value={ labelName }
							onChange={ ( value ) => { setAttributes( { labelName: value } ) } }
						/>
					</BaseControl>
					<BaseControl
						id={ 'labelEmail' }
						label={ __('Email Label', 'citadela-directory') }
						help={ __('Label used for Email input.', 'citadela-directory') }
					>
						<TextControl
							id={ 'labelEmail' }	
							type={ 'text' }
							value={ labelEmail }
							onChange={ ( value ) => { setAttributes( { labelEmail: value } ) } }
						/>
					</BaseControl>
					<BaseControl
						id={ 'labelSubject' }
						label={ __('Subject Label', 'citadela-directory') }
						help={ __('Label used for Subject input.', 'citadela-directory') }
					>
						<TextControl
							id={ 'labelSubject' }	
							type={ 'text' }
							value={ labelSubject }
							onChange={ ( value ) => { setAttributes( { labelSubject: value } ) } }
						/>
					</BaseControl>
					<BaseControl
						id={ 'labelMessage' }
						label={ __('Message Label', 'citadela-directory') }
						help={ __('Label used for Message textarea.', 'citadela-directory') }
					>
						<TextControl
							id={ 'labelMessage' }	
							type={ 'text' }
							value={ labelMessage }
							onChange={ ( value ) => { setAttributes( { labelMessage: value } ) } }
						/>
					</BaseControl>
					<BaseControl
						id={ 'labelSendButton' }
						label={ __('Send Button Text', 'citadela-directory') }
						help={ __('Text displayed on Send Button.', 'citadela-directory') }
					>
						<TextControl
							id={ 'labelSendButton' }	
							type={ 'text' }
							value={ labelSendButton }
							onChange={ ( value ) => { setAttributes( { labelSendButton: value } ) } }
						/>
					</BaseControl>
				</PanelBody>

				<PanelBody 
					title={__('Help texts', 'citadela-directory')}
					initialOpen={false}
					className="citadela-panel"
				>
					<p className={ "citadela-help-text" }>{ __('Useful to show additional help text or GDPR text with each input in the form.', 'citadela-directory') }</p>
					
					<BaseControl
						id={ 'helpName' }
						label={ __('Name Help', 'citadela-directory') }
						help={ __('Help text displayed with Name input.', 'citadela-directory') }
					>
						<TextControl
							id={ 'helpName' }	
							type={ 'text' }
							value={ helpName }
							onChange={ ( value ) => { setAttributes( { helpName: value } ) } }
						/>
					</BaseControl>
					<BaseControl
						id={ 'helpEmail' }
						label={ __('Email Help', 'citadela-directory') }
						help={ __('Help text displayed with Email input.', 'citadela-directory') }
					>
						<TextControl
							id={ 'helpEmail' }	
							type={ 'text' }
							value={ helpEmail }
							onChange={ ( value ) => { setAttributes( { helpEmail: value } ) } }
						/>
					</BaseControl>
					<BaseControl
						id={ 'helpSubject' }
						label={ __('Subject Help', 'citadela-directory') }
						help={ __('Help text displayed with Subject input.', 'citadela-directory') }
					>
						<TextControl
							id={ 'helpSubject' }	
							type={ 'text' }
							value={ helpSubject }
							onChange={ ( value ) => { setAttributes( { helpSubject: value } ) } }
						/>
					</BaseControl>
					<BaseControl
						id={ 'helpMessage' }
						label={ __('Message Help', 'citadela-directory') }
						help={ __('Help text displayed with Message input.', 'citadela-directory') }
					>
						<TextControl
							id={ 'helpMessage' }	
							type={ 'text' }
							value={ helpMessage }
							onChange={ ( value ) => { setAttributes( { helpMessage: value } ) } }
						/>
					</BaseControl>
				</PanelBody>

				<PanelBody 
					title={__('Notification messages', 'citadela-directory')}
					initialOpen={false}
					className="citadela-panel"
				>
					<BaseControl
						id={ 'notificationSuccess' }
						label={ __('Success Message', 'citadela-directory') }
						help={ __('Message displayed when the form was sent succesfully.', 'citadela-directory') }
					>
						<TextControl
							id={ 'notificationSuccess' }	
							type={ 'text' }
							value={ notificationSuccess }
							onChange={ ( value ) => { setAttributes( { notificationSuccess: value } ) } }
						/>
					</BaseControl>
					<BaseControl
						id={ 'notificationValidationError' }
						label={ __('Validation Error Message', 'citadela-directory') }
						help={ __('Message displayed when the form validation failed.', 'citadela-directory') }
					>
						<TextControl
							id={ 'notificationValidationError' }	
							type={ 'text' }
							value={ notificationValidationError }
							onChange={ ( value ) => { setAttributes( { notificationValidationError: value } ) } }
						/>
					</BaseControl>
					<BaseControl
						id={ 'notificationServerError' }
						label={ __('Server Error Message', 'citadela-directory') }
						help={ __('Message displayed when the form sending failed because of problem on the server side.', 'citadela-directory') }
					>
						<TextControl
							id={ 'notificationServerError' }	
							type={ 'text' }
							value={ notificationServerError }
							onChange={ ( value ) => { setAttributes( { notificationServerError: value } ) } }
						/>
					</BaseControl>
				</PanelBody>
			</Fragment>
		);
	}
}