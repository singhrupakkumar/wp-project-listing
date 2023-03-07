/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component, useCallback } = wp.element;
const { BaseControl, RangeControl } = wp.components;

const CustomRangeControl = ( { rangeValue = '', onChange, min, max, step, initial, allowReset } ) => {
	const setRangeValue = useCallback(
		( value ) => {
			onChange( value === undefined ? initial : value );
		},
		[ onChange, initial ]
	);

	return (
		<RangeControl
			value={ rangeValue }
			min={ min }
			max={ max }
			step={ step }
			initialPosition={ initial }
			allowReset={ allowReset }
			onChange={ setRangeValue }
		/>
	);
}

export default class CitadelaRangeControl extends Component {

	render() {
		const { label, help = "", min = 0, max = 100, step = 1, initial = 50, onChange, rangeValue, allowReset = false, className = "", } = this.props;

		return(
			
			<BaseControl
				label={ label }
				help={ help }
				className={ className } 
			>
				<CustomRangeControl
					rangeValue={ rangeValue }
					onChange={ onChange }
					min={ min }
					max={ max }
					step={ step }
					initial={ initial }
					allowReset={ allowReset }
				/>			
			</BaseControl>

	    );
	}
}