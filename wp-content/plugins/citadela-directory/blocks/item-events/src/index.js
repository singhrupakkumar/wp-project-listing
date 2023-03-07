import edit from './edit';
import metadata from './block.json';

const { __, setLocaleData } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { name } = metadata;

registerBlockType(name, {
    title: __('Item Events', 'citadela-directory'),
    description: __('Displays upcoming events of specific listing item.', 'citadela-directory'),
    icon: 'calendar',
    category: 'citadela-directory-blocks',
    edit: edit,
    save: () => {
        return null;
    }
});