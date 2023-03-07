import SpacerInspectorControls from './inspector-controls';
import StateIcons from '../../components/state-icons';

const { __ } = wp.i18n;
const { Component, Fragment } = wp.element;
const { InspectorControls} = wp.blockEditor;
const { Icon, Tooltip } = wp.components;

export default class Edit extends Component{

	constructor() {
		super( ...arguments );
		this.setState = this.setState.bind(this);
		this.migrateAttributeHideInResponsive = this.migrateAttributeHideInResponsive.bind(this);
		
		this.state = {
			responsiveTab: "desktop",
		}

		this.migrateAttributeHideInResponsive();
	}
	
	migrateAttributeHideInResponsive(){
		const { attributes, setAttributes } = this.props;
		const {
			hideInResponsive,
		} = attributes;
		// needed check after update with available responsive options, 
		// hideInResponsive option no more available, 
		// if hideInResponsive was true, set height to 0 and turn on responsive options
		if( hideInResponsive ) {
			setAttributes( { 
				useResponsiveOptions: true, 
				hideInResponsive: false, 
				heightMobile: 0,
			} );
		}
	}

	render() {
		const { attributes, setAttributes, isSelected, className } = this.props;
		const {
			useResponsiveOptions,
			breakpointMobile,
			height,
			heightMobile,
			unit,
			unitMobile,
			hideInResponsive
		} = attributes;
		
		const mobileView = ( useResponsiveOptions && this.state.responsiveTab == 'mobile' );
		const desktopView = ( useResponsiveOptions && this.state.responsiveTab == 'desktop' );

		const actualValue = mobileView
		? {
			height: heightMobile !== undefined ? heightMobile : height,
			unit: unitMobile !== undefined ? unitMobile : unit,
		}
		: {
			height: height,
			unit: unit,
		}
		
		let style = {};
		let negativeHeight = false;
		if( actualValue.height < 0){
			style={
				...{marginTop: actualValue.height + actualValue.unit}
			}
			negativeHeight = true;
		}else{
			style={
				...{paddingTop: actualValue.height + actualValue.unit}
			}
		}
	
	
		return (
			<Fragment>
				<InspectorControls key='inspector'>
					<SpacerInspectorControls 
						attributes={ attributes } 
						setAttributes={ setAttributes }
						state={ this.state } 
						setState={ this.setState }
					/>
				</InspectorControls>
				<div 
					className={ 
						classNames(
							className,
							"citadela-block-spacer",
							{'is-selected': isSelected},
							{'negative-height': negativeHeight},
							{'hide-in-responsive': hideInResponsive}
							)
					}
				>
					<StateIcons 
                        useResponsiveOptions= { useResponsiveOptions } 
                        isSelected={ isSelected } 
                        currentView={ this.state.responsiveTab }
                    />

					<div className="inner-holder" style={style} />
				</div>
			</Fragment>
		);


	}
}