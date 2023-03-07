import edit from './edit';
import metadata from './block.json';

const { __, setLocaleData } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { Fragment } = wp.element;
const { InnerBlocks } = wp.blockEditor;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( 'Listing Map (for posts)', 'citadela-directory' ),
	description: __( 'Displays posts on map based on filters. For example posts only from specific category or location.', 'citadela-directory' ),
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