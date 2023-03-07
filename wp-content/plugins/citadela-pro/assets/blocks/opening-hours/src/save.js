const { __ } = wp.i18n;
const { RichText } = wp.blockEditor;

export default function OpeningHoursSave( { 
	attributes,
	className
} ) {
	const {
		mondayTitle, tuesdayTitle, wednesdayTitle, thursdayTitle, fridayTitle, saturdayTitle, sundayTitle,
		mondayValue, tuesdayValue, wednesdayValue, thursdayValue, fridayValue, saturdayValue, sundayValue,
		dayLabelColor, dayDataColor, linesColor,
		hideEmptyDays,
		layout,
		textAlign,
		boxWidth,
	} = attributes;

	
	const dayLabelStyles = {
		...( dayLabelColor ? { color: dayLabelColor } : false ),
	};

	const dayDataStyles = {
		...( dayDataColor ? { color: dayDataColor } : false ),
	};


	const dayHolderStyles = {
		...( boxWidth && layout == 'box' ? { flexBasis: `${boxWidth}px;` } : false ),
		...( linesColor ? { borderColor: linesColor } : false ),
	};
		
	const days = [
		{ 
		day: mondayTitle ? <RichText.Content
				tagName='p'
				value= { mondayTitle }
				/> : "", 
		value: mondayValue ? <RichText.Content
				tagName='p'
				value= { mondayValue }
				/> : ""
		},
		{ 
		day: tuesdayTitle ? <RichText.Content
				tagName='p'
				value= { tuesdayTitle }
				/> : "", 
		value: tuesdayValue ? <RichText.Content
				tagName='p'
				value= { tuesdayValue }
				/> : ""
		},
		{ 
		day: wednesdayTitle ? <RichText.Content
				tagName='p'
				value= { wednesdayTitle }
				/> : "", 
		value: wednesdayValue ? <RichText.Content
				tagName='p'
				value= { wednesdayValue }
				/> : ""
		},
		{ 
		day: thursdayTitle ? <RichText.Content
				tagName='p'
				value= { thursdayTitle }
				/> : "", 
		value: thursdayValue ? <RichText.Content
				tagName='p'
				value= { thursdayValue }
				/> : ""
		},
		{ 
		day: fridayTitle ? <RichText.Content
				tagName='p'
				value= { fridayTitle }
				/> : "", 
		value: fridayValue ? <RichText.Content
				tagName='p'
				value= { fridayValue }
				/> : ""
		},
		{ 
		day: saturdayTitle ? <RichText.Content
				tagName='p'
				value= { saturdayTitle }
				/> : "", 
		value: saturdayValue ? <RichText.Content
				tagName='p'
				value= { saturdayValue }
				/> : ""
		},
		{ 
		day: sundayTitle ? <RichText.Content
				tagName='p'
				value= { sundayTitle }
				/> : "", 
		value: sundayValue ? <RichText.Content
				tagName='p'
				value= { sundayValue }
				/> : ""
		},
	].filter( (item) => {
		if( !hideEmptyDays ){
			return item;
		}else if( hideEmptyDays && item.value != "" ){
			return item;
		}
	});
	
	const data = days.map((item, key) =>
			<div class="oh-day" style={ dayHolderStyles ? dayHolderStyles : false } >
				<div class="oh-label" style={ dayLabelStyles ? dayLabelStyles : false } >
					{ item.day }
				</div>
				<div class="oh-data" style={ dayDataStyles ? dayDataStyles: false } >
					{ item.value }
				</div>
			</div>
	);

	return (
		<div 
			className={ 
				classNames(
					className,
					"citadela-block-opening-hours",
					`layout-${layout}`,
					`align-${textAlign}`,
					{ 'custom-border-color': linesColor },
					{ 'custom-label-color': dayLabelColor },
					{ 'custom-data-color': dayDataColor }
				)
			}
		>
			{data}
		</div>
	);

}
