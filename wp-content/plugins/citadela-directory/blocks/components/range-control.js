/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { Component, useCallback } = wp.element;
const { BaseControl, RangeControl } = wp.components;

const CustomRangeControl = ( { rangeValue = '', onChange, min, max, step, initial, allowReset, allowNoValue } ) => {
	const setRangeValue = useCallback(
		( value ) => {
			if( allowNoValue ){
				onChange( value );
			}else{
				onChange( value === undefined ? initial : value );
			}
		},
		[ onChange, initial, allowNoValue ]
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
		const { label, help = "", min = 0, max = 100, step = 1, initial = 50, onChange, rangeValue, allowReset = false, allowNoValue = false} = this.props;

		const initialValue = allowNoValue ? undefined : initial;
		return(

			<BaseControl
				label={ label }
				help={ help }
			>
				<CustomRangeControl
					rangeValue={ rangeValue }
					onChange={ onChange }
					min={ min }
					max={ max }
					step={ step }
					initial={ initialValue }
					allowReset={ allowReset }
				/>
			</BaseControl>

	    );
	}
}