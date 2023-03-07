import FormInspectorControls from './inspector-controls';
import ToolbarAlignment from '../../components/toolbar-alignment';
import metadata from './block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { ToolbarGroup, ToolbarItem, Icon } = wp.components;
const { InspectorControls, BlockControls } = wp.blockEditor;
const { name } = metadata;

registerBlockType( name, { ...metadata, 
	title: __( 'Posts Search Form', 'citadela-directory' ),
	description: __( 'Search form that allows visitors to search posts by keyword, category or location.', 'citadela-directory' ),
	edit: (props) => {
        const activeProPlugin = typeof CitadelaProSettings === "undefined" ? false : true;

        const {
            attributes,
            setAttributes,
            name,
        } = props;
        
        const {
            buttonBackgroundColor,
			buttonTextColor,
            backgroundBlur,
			blurRadius,
			backgroundType,
			backgroundColorType,
			backgroundColor,
			backgroundGradient,
            align,
            borderWidth,
            borderColor,
            borderRadius,
            boxShadow,
            boxShadowType,
            hideInputLocation,
        } = attributes;

        const block = wp.blocks.getBlockType(name);

        let mainFormStyles = {};
        let styles = {};
        let borderStyle = {};
        let buttonBackgroundStyle = {};
        let buttonBorderColorStyle = {};
        let buttonTextStyle = {};
        let hasBackground = false;
        if( activeProPlugin ){
            
            hasBackground = backgroundType == 'background' || backgroundType == 'background-collapsed';
            
            if( backgroundType != 'none' ){
                if( backgroundColorType == 'solid' ){
                    mainFormStyles = {
                        ...mainFormStyles,
                        ...( backgroundColor ? { backgroundColor: backgroundColor } : {} ),
                    }    
                }
                if( backgroundColorType == 'gradient' ){
                    mainFormStyles = {
                        ...mainFormStyles,
                        ...( { backgroundImage: (backgroundGradient.type == "linear" ) ? `linear-gradient(${backgroundGradient.degree}deg, ${backgroundGradient.first} 0%, ${backgroundGradient.second} 100%)` : `radial-gradient(${backgroundGradient.first} 0%, ${backgroundGradient.second} 100%)` } ),
                    }    

                }
                if( backgroundBlur ) {
                    mainFormStyles = {
                        ...mainFormStyles,
                        ...( { WebkitBackdropFilter: `blur(${blurRadius}px)` }),
                        ...( { backdropFilter: `blur(${blurRadius}px)` }),
                    } 
                }
               
            }

            buttonBackgroundStyle = {
                ...( buttonBackgroundColor ? { backgroundColor: buttonBackgroundColor } : {} ),
            }
            
            buttonBorderColorStyle = {
                ...( buttonBackgroundColor ? { borderColor: buttonBackgroundColor } : {} ),
            }

            buttonTextStyle = {
                ...( buttonTextColor ? { color: buttonTextColor } : {} ),
            }

            const shadow = boxShadowType === 'custom' && boxShadow
                ? `${boxShadow.horizontal}px ${boxShadow.vertical}px ${boxShadow.blur}px ${boxShadow.spread}px rgba(${boxShadow.color.r}, ${boxShadow.color.g}, ${boxShadow.color.b}, ${boxShadow.color.a})`
                : '';

            styles = {
                ...( borderWidth != 'none' && borderColor ? { borderColor: borderColor } : {} ),
                ...( borderRadius >= 0 ? { borderRadius: `${borderRadius}px` } : {} ),
                ...( shadow ? { boxShadow: shadow } : {} ),
            } 
            
            borderStyle = {
                ...( borderRadius >= 0 ? { borderRadius: `${borderRadius}px` } : {} ),
            }

        }

		return (
            <>
                { activeProPlugin &&
                    <BlockControls>
                        <ToolbarGroup>
                            <ToolbarItem>
                                { ( toggleProps ) => (
                                    <ToolbarAlignment 
                                        value={ align } 
                                        onChange={ ( value ) => ( setAttributes( { align: value } ) ) }
                                        leftLabel={ __( 'Align Left', 'citadela-directory' ) }    
                                        centerLabel={ __( 'Align Center', 'citadela-directory' ) }    
                                        rightLabel={ __( 'Align Right', 'citadela-directory' ) }    
                                        toggleProps={ toggleProps }
                                        allowJustify
                                        justifyLabel={ __( 'Align Justify', 'citadela-directory' ) }    

                                    />
                                )}
                            </ToolbarItem>
                        </ToolbarGroup>
                    </BlockControls>
                }
                { activeProPlugin &&
                    <InspectorControls>
                        <FormInspectorControls attributes={ attributes } setAttributes={ setAttributes } />
                    </InspectorControls>
                }

                <div className={ classNames(
                    "wp-block-citadela-blocks",
                    "ctdl-posts-search-form",
                    attributes.className,
                    activeProPlugin ? `border-${borderWidth}` : null,
                    activeProPlugin ? `shadow-${boxShadowType}` : null,
                    activeProPlugin ? `align-${align}` : null,
                    hideInputLocation ? 'hidden-input-location' : null,
                    activeProPlugin && borderRadius ? 'custom-border-radius' : null,
                    activeProPlugin && backgroundType == 'background' ? 'has-background' : null,
                    activeProPlugin && backgroundType == 'background-collapsed' ? 'has-background-collapsed' : null,
                    activeProPlugin && hasBackground && backgroundBlur ? 'blur-background' : null,
                    activeProPlugin && hasBackground && backgroundColorType == 'solid' ? 'solid-background' : null,
                    activeProPlugin && hasBackground && backgroundColorType == 'solid' && backgroundColor ? 'custom-solid-background-color' : null,
                    activeProPlugin && hasBackground && backgroundColorType == 'gradient' ? 'gradient-background' : null,
                    activeProPlugin && buttonBackgroundColor ? 'custom-button-background-color' : null,
                    activeProPlugin && buttonTextColor ? 'custom-button-text-color' : null,
                )}>
                    <div class="ctdl-blockcard-title">
                        <div class="ctdl-blockcard-icon">
                            <Icon icon={block.icon.src} />
                        </div>
                        <div class="ctdl-blockcard-text">
                            <div class="ctdl-blockcard-name">{ block.title }</div>
                            <div class="ctdl-blockcard-desc">{ block.description }</div>
                        </div>
                    </div>

                    <div class="citadela-block-form search-form-component-container" style={ mainFormStyles }>
                        <div class="search-form" style={ styles }>
                            <div class="data-type-1">
                                <div class="input-container keyword" style={ styles }>
                                    <div class="input-data">
                                        <div class="label"></div>
                                        <div class="input" style={ borderStyle }></div>
                                    </div>
                                </div>
                            </div>

                            <div class="data-type-2">
                                <div class="input-container category" style={ styles }>
                                    <div class="input-data">
                                        <div class="label"></div>
                                        <div class="input" style={ borderStyle }></div>
                                    </div>
                                </div>

                                { ! hideInputLocation  &&
                                    <div class="input-container location" style={ styles }>
                                        <div class="input-data">
                                            <div class="label"></div>
                                            <div class="input" style={ borderStyle }></div>
                                        </div>
                                    </div>
                                }
                            </div>

                            <div class="data-submit">
                                <div class="input-container sf-button" style={ { ...borderStyle, ...buttonBackgroundStyle, ...buttonBorderColorStyle } }>
                                    <div class="input-data">
                                        <span class="submit" style={ buttonTextStyle }>{ __( 'Search', 'citadela-directory' ) }</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </>
        );
	},
	save: () => {
		return null;
	}
} );