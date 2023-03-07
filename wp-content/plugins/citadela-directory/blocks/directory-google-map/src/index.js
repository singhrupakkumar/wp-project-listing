import edit from './edit';
import metadata from './block.json';

const { __, setLocaleData } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { Fragment } = wp.element;
const { InnerBlocks } = wp.blockEditor;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( 'Listing Map', 'citadela-directory' ),
	description: __( 'Displays listing items on map based on filters. For example items only from specific category, location or featured items.', 'citadela-directory' ),
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
                        ['citadela-directory/directory-search-form', {}],
                    ]}
                    templateLock="all"
                />}
            </Fragment>
        );
	}
} );