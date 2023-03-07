import edit from './edit';
import metadata from './block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InnerBlocks } = wp.blockEditor;
const { Fragment } = wp.element;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( 'Automatic Listing Map (for posts)', 'citadela-directory' ),
	description: __( 'Displays relevant posts automatically based on current page. For example search results or post detail location.', 'citadela-directory' ),
    edit: edit,
	save: (props) => {
        const {
            attributes: {
                withSearchForm
            },
        } = props;
		return (
            <Fragment>
                {withSearchForm && <InnerBlocks.Content
                    template={[
                        ['citadela-directory/posts-search-form', {}],
                    ]}
                    templateLock="all"
                />}
            </Fragment>
        );
	}
} );