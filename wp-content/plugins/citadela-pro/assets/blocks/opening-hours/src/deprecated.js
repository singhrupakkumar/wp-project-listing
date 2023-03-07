const { RichText } = wp.blockEditor;
const deprecated = [
    {
        attributes: {
            hideEmptyDays: {
                type: "boolean",
                default: false
            },
            layout: {
                type: "string",
                default: "list"
            },
            textAlign: {
                type: "string",
                default: "left"
            },
            mondayTitle: {
                type: "string",
                default: ""
            },
            tuesdayTitle: {
                type: "string",
                default: ""
            },
            wednesdayTitle: {
                type: "string",
                default: ""
            },
            thursdayTitle: {
                type: "string",
                default: ""
            },
            fridayTitle: {
                type: "string",
                default: ""
            },
            saturdayTitle: {
                type: "string",
                default: ""
            },
            sundayTitle: {
                type: "string",
                default: ""
            },
            mondayValue: {
                type: "string",
                default: "8:00 - 17:00"
            },
            tuesdayValue: {
                type: "string",
                default: "8:00 - 17:00"
            },
            wednesdayValue: {
                type: "string",
                default: "8:00 - 17:00"
            },
            thursdayValue: {
                type: "string",
                default: "8:00 - 17:00"
            },
            fridayValue: {
                type: "string",
                default:"8:00 - 17:00"
            },
            saturdayValue: {
                type: "string",
                default: ""
            },
            sundayValue: {
                type: "string",
                default: ""
            },
            dayDataColor: {
                type: "string",
            }, 
            dayLabelColor: {
                type: "string",
            }, 
            linesColor: {
                type: "string",
            },
            boxWidth: {
                type: "number",
                default: 200
            }
        },
        save({ attributes, className }){
            
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
                color: dayLabelColor ? dayLabelColor : false,
            };
            
            const dayDataStyles = {
                color: dayDataColor ? dayDataColor : false,
            };

            const dayHolderStyles = {
                ...( boxWidth && layout == 'box' ? { flexBasis: `${boxWidth}px` } : false ),
                borderColor: linesColor ? linesColor : false,
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
    },
    {
        attributes: {
            hideEmptyDays: {
                type: "boolean",
                default: false
            },
            layout: {
                type: "string",
                default: "list"
            },
            textAlign: {
                type: "string",
                default: "left"
            },
            mondayTitle: {
                type: "string",
                default: "Monday"
            },
            tuesdayTitle: {
                type: "string",
                default: "Tuesday"
            },
            wednesdayTitle: {
                type: "string",
                default: "Wednesday"
            },
            thursdayTitle: {
                type: "string",
                default: "Thursday"
            },
            fridayTitle: {
                type: "string",
                default: "Friday"
            },
            saturdayTitle: {
                type: "string",
                default: "Saturday"
            },
            sundayTitle: {
                type: "string",
                default: "Sunday"
            },
            mondayValue: {
                type: "string",
                default: "8:00 - 17:00"
            },
            tuesdayValue: {
                type: "string",
                default: "8:00 - 17:00"
            },
            wednesdayValue: {
                type: "string",
                default: "8:00 - 17:00"
            },
            thursdayValue: {
                type: "string",
                default: "8:00 - 17:00"
            },
            fridayValue: {
                type: "string",
                default:"8:00 - 17:00"
            },
            saturdayValue: {
                type: "string",
                default: ""
            },
            sundayValue: {
                type: "string",
                default: ""
            },
            dayDataColor: {
                type: "string",
            }, 
            dayLabelColor: {
                type: "string",
            }, 
            linesColor: {
                type: "string",
            },
            boxWidth: {
                type: "number",
                default: 200
            }
        },
        save({ attributes, className }){
            
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
                color: dayLabelColor ? dayLabelColor : false,
            };
        
            const dayDataStyles = {
                color: dayDataColor ? dayDataColor : false,
            };
        
            const dayHolderStyles = {
                ...( boxWidth && layout == 'box' ? { flexBasis: `${boxWidth}px` } : false ),
                borderColor: linesColor ? linesColor : false,
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

            return(
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
    }
]
export default deprecated;