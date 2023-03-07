const { __ } = wp.i18n;
const { Component, Fragment } = wp.element;
const { Button } = wp.components;
const { RichText } = wp.blockEditor;

export default class TableRows extends Component {

	constructor(){
		super(...arguments);
		this.handleRowUpdate = this.handleRowUpdate.bind( this );
		this.insertRow = this.insertRow.bind( this );
		this.deleteRow = this.deleteRow.bind( this );
		this.moveRow = this.moveRow.bind( this );

		this.newRows = [];
	}

	handleRowUpdate( value, index) {
		const{ rows } = this.props;
		//make copy of rows prop, needed to not change directly value of property
		this.newRows = rows.slice( 0, rows.length + 1 );
		const newData = { text: value };
		this.newRows[index] = newData;
		this.props.setAttributes( { rows: this.newRows } );
	}

	
	insertRow( index ){
		const{ rows } = this.props;
		//make copy of rows prop, needed to not change directly value of property
		this.newRows = rows.slice( 0, rows.length + 1 );
		const newData = { text: "" };
		this.newRows.splice(index+1, 0, newData);
		this.props.setAttributes( { rows: this.newRows } );
	}


	deleteRow( index ){
		const{ rows } = this.props;
		//make copy of rows prop, needed to not change directly value of property
		this.newRows = rows.slice( 0, rows.length + 1 );
		this.newRows.splice(index, 1);
		this.props.setAttributes( { rows: this.newRows } );
	}
	
	moveRow( index, direction ){
		const{ rows } = this.props;
		//make copy of rows prop, needed to not change directly value of property
		this.newRows = rows.slice( 0, rows.length + 1 );
		const movedData = this.newRows[index];
		switch (direction) {
			case "up":
				this.newRows.splice(index - 1, 0, movedData);
				this.newRows.splice(index + 1, 1);

				break;
			
			case "down":
				this.newRows.splice(index + 2, 0, movedData);
				this.newRows.splice(index, 1);
				break;
		
			default:
				break;
		}
		this.props.setAttributes( { rows: this.newRows } );
	}

	
	render() {
		const { 
			rows,
			isSelected
		} = this.props;
		
		const rowsCount = rows.length - 1;
		const rowsOutput = rows.map((rowData, index) => 
						<div class="row">
							<RichText
								tagName='div'
								className={classNames(
									"row-text",
									{"empty-row" : rowData.text == "" }
								)}
								onChange= { ( value ) => { this.handleRowUpdate( value, index ) } }
								value= { rowData.text }
								placeholder={ isSelected ? __('row text...', 'citadela-pro' ) : '' }
								keepPlaceholderOnFocus={ true }
								multiline={ false }
							/>
							<div class="row-tools">
								{ index > 0 &&
								<Button
									icon="arrow-up"
									label={__("Move up", 'citadela-pro') }
									onClick={() => this.moveRow(index, "up") }
								/>
								}
								{ index < rowsCount &&
								<Button
									icon="arrow-down"
									label={ __("Move down", 'citadela-pro') }
									onClick={ () => this.moveRow(index, "down") }
								/>
								}
								{ rowsCount > 0 &&
								<Button
								icon="no"
								label={ __("Delete row", 'citadela-pro') }
								onClick={ () => this.deleteRow(index) }
								/>
								}
								<Button
									icon="plus"
									label={ __("Insert row after", 'citadela-pro') }
									onClick={ () => this.insertRow(index) }
								/>
							</div>
						</div>
					);

		return (
			<Fragment>
				{rowsOutput}
			</Fragment>
		);
	}

}