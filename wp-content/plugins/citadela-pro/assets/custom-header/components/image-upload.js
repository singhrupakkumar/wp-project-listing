/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { applyFilters } = wp.hooks;

const { MediaUpload, MediaUploadCheck } = wp.blockEditor;
const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost;
const { withSelect, withDispatch } = wp.data;
const { Fragment, Component } = wp.element;
const { Spinner, ResponsiveWrapper, withNotices, withFilters, DropZoneProvider, DropZone, CheckboxControl, TextControl, ColorIndicator, FocalPointPicker, BaseControl, PanelBody, RadioControl, RangeControl, ColorPalette, Button, Placeholder, ToggleControl, SelectControl } = wp.components;
const { compose, withState } = wp.compose;



const ALLOWED_MEDIA_TYPES = [ 'image' ];


class CitadelaImageUpload extends Component {
	
	constructor() {
		super( ...arguments );
	}
	
	render() {
		const {
			onUpdateImage,
			onDropImage,
			onRemoveImage,
			media,
			noticeUI,
			mediaPopupLabel,
			dropzoneLabel,
			removeImageLabel,
			replaceImageLabel,
		} = this.props;

		const DEFAULT_IMAGE_LABEL = mediaPopupLabel ? mediaPopupLabel : __( 'Custom image', 'citadela-pro' );
		const DEFAULT_SET_IMAGE_LABEL = dropzoneLabel ? dropzoneLabel : __( 'Set custom image', 'citadela-pro' );
		const DEFAULT_REMOVE_IMAGE_LABEL = removeImageLabel ? removeImageLabel : __( 'Remove image', 'citadela-pro' );
		const DEFAULT_REPLACE_IMAGE_LABEL = replaceImageLabel ? replaceImageLabel : __( 'Replace image', 'citadela-pro' );
		
		
		let isMedia = ( media && media.id ) ? true : false;
		let isLoading = false;
		
		return (
			<Fragment>
				{ noticeUI }
				<div className="editor-post-featured-image">
					<MediaUploadCheck fallback={ __( 'To edit the image, you need permission to upload media.', 'citadela-pro'	) }>
						<MediaUpload
							title={ DEFAULT_IMAGE_LABEL }
							onSelect={ onUpdateImage }
							//unstableFeaturedImageFlow
							allowedTypes={ ALLOWED_MEDIA_TYPES }
							modalClass={ 'editor-post-featured-image__media-modal' }
							render={ ( { open } ) => (
								<div className="editor-post-featured-image__container">
									<Button
										className={
											isMedia
												? 'editor-post-featured-image__preview'
												: 'editor-post-featured-image__toggle'
										}
										onClick={ open }
										aria-label={
											 isMedia
											 	? __( 'Edit or update the image', 'citadela-pro' )
												: null
										}
									>
										{ isMedia && (
											<ResponsiveWrapper
												naturalWidth={ media.size.width }
												naturalHeight={ media.size.height }
												isInline
											>
												<img
													src={ media.url }
													alt=""
												/>
											</ResponsiveWrapper>
										) }
										{ isLoading && (
											<Spinner />
										) }
										{ ! isMedia &&
												DEFAULT_SET_IMAGE_LABEL }
									</Button>
									<DropZone onFilesDrop={ onDropImage } />
								</div>
							) }
							value={ ( media && media.id ) ? media.id : null }
						/>
					</MediaUploadCheck>
					{ isMedia && (
						<MediaUploadCheck>
							<MediaUpload
								title={ DEFAULT_IMAGE_LABEL }
								onSelect={ onUpdateImage }
								//unstableFeaturedImageFlow
								allowedTypes={ ALLOWED_MEDIA_TYPES }
								modalClass="editor-post-featured-image__media-modal"
								render={ ( { open } ) => (
									<Button onClick={ open } isSecondary>
										{ DEFAULT_REPLACE_IMAGE_LABEL }
									</Button>
								) }
							/>
						</MediaUploadCheck>
					) }
					{ isMedia && (
						<MediaUploadCheck>
							<Button onClick={ onRemoveImage } isLink isDestructive>
								{ DEFAULT_REMOVE_IMAGE_LABEL }
							</Button>
						</MediaUploadCheck>
					) }
				</div>
			</Fragment>
		);
	}
}


const applyWithSelect = withSelect( ( select, props ) => {
	const { getEditedPostAttribute } = select(
		'core/editor'
	);
	const { meta } = props;	
	const media = getEditedPostAttribute( 'meta' )[meta];
	return {
		media: media ? media : null,
	};
} );

const applyWithDispatch = withDispatch(
	( dispatch, { noticeOperations, onChange, state }, { select } ) => {
		const { editPost } = dispatch( 'core/editor' );
		return {
			onUpdateImage( image ) {
				//image selected from media
				const data = {
					id: image.id,
					url: image.url,
					size: { width: image.width, height: image.height }
				};
				onChange(data);
			},
			onDropImage( filesList ) {
				select( 'core/block-editor' )
					.getSettings()
					.mediaUpload( {
						allowedTypes: [ 'image' ],
						filesList,
						onFileChange( [ image ] ) {
							//image dropped to dropzone
							if( image.id !== undefined ){
								const data = {
									id: image.id,
									url: image.url,
									size: { width: image.media_details.width, height: image.media_details.height }
								};
								onChange(data);
							}
						},
						onError( message ) {
							noticeOperations.removeAllNotices();
							noticeOperations.createErrorNotice( message );
						},
					} );
			},
			onRemoveImage() {
				onChange(null);
			},
		};
	}
);

export default compose(
	withNotices,
	applyWithSelect,
	applyWithDispatch,
)( CitadelaImageUpload );
