/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component } = wp.element;
const { PanelBody, ToggleControl, RangeControl } = wp.components;



export default class CitadelaDirectoryPanelCarousel extends Component {
    
    constructor(){
		super( ...arguments );
        this.onChange = this.onChange.bind( this );
    }

	render() {

        const { 
            attributes,
        } = this.props;

        const {
            useCarousel,
            carouselAutoplay,
            carouselAutoHeight,
            carouselAutoplayDelay,
            carouselNavigation,
            carouselLoop,
            carouselPagination, 
        } = attributes;
        
		return(
			<PanelBody
                title={__('Carousel Options', 'citadela-directory')}
                initialOpen={false}
                className="citadela-panel"
            >
                <ToggleControl
                    label={__('Use carousel', 'citadela-directory')}
                    checked={ useCarousel }
                    onChange={ ( checked ) => this.onChange( 'useCarousel', checked ) }
                />

                { useCarousel &&
                    <>
                        <ToggleControl
                            label={__('Show navigation arrows', 'citadela-directory')}
                            checked={ carouselNavigation }
                            onChange={ ( value ) => this.onChange( 'carouselNavigation', value ) }
                        />

                        <ToggleControl
                            label={__('Show pagination bullets', 'citadela-directory')}
                            checked={ carouselPagination }
                            onChange={ ( value ) => this.onChange( 'carouselPagination', value ) }
                        />

                        <ToggleControl
                            label={__('Infinite loop', 'citadela-directory')}
                            checked={ carouselLoop }
                            onChange={ ( value ) => this.onChange( 'carouselLoop', value ) }
                        />
                        <ToggleControl
                            label={__('Automatic height', 'citadela-directory')}
                            checked={ carouselAutoHeight }
                            onChange={ ( value ) => this.onChange( 'carouselAutoHeight', value ) }
                        />
                        <ToggleControl
                            label={__('Autoplay', 'citadela-directory')}
                            checked={ carouselAutoplay }
                            onChange={ ( value ) => this.onChange( 'carouselAutoplay', value ) }
                        />
                        { carouselAutoplay &&
                            <RangeControl
                                label={ __( 'Delay between slides', 'citadela-directory' ) }
                                help={ __( 'Time in seconds', 'citadela-directory' ) }
                                value={ carouselAutoplayDelay }
                                onChange={ ( value ) => this.onChange( 'carouselAutoplayDelay', value ) }
                                min={ 1 }
                                max={ 10 }
                            />
                        }
                    </>
                }

            </PanelBody>
	    );
    }
    
    onChange( option, value ) {
        const {
            useCarousel,
            carouselAutoplay,
            carouselAutoplayDelay,
            carouselNavigation,
            carouselPagination, 
            carouselLoop,
            carouselAutoHeight,
        } = this.props.attributes;

        let values = [];
        values['useCarousel'] = useCarousel;
        values['carouselNavigation'] = carouselNavigation;
        values['carouselPagination'] = carouselPagination;
        values['carouselAutoplay'] = carouselAutoplay;
        values['carouselAutoHeight'] = carouselAutoHeight;
        values['carouselAutoplayDelay'] = carouselAutoplayDelay;
        values['carouselLoop'] = carouselLoop;
        values[option] = value;
        
        this.props.onChange( values );
    }

}