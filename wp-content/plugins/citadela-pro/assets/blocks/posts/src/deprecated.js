const { Fragment } = wp.element;
const deprecated = [
	{
		attributes: {
			category: {
				type: 'string',
				default: '',
			},
			count: {
				type: 'number',
				default: 12,
			},
			title: {
				type: 'string',
				default: '',
			},
			layout: {
				type: 'string',
				default: 'simple',
			},
			size: {
				type: 'string',
				default: 'medium',
			},
			showDate: {
				type: 'boolean',
				default: true,
			},
			showFeaturedImage: {
				type: 'boolean',
				default: true,
			},
			showDescription: {
				type: 'boolean',
				default: true,
			},
			showCategories: {
				type: 'boolean',
				default: true,
			},
			postsOrderBy: {
				type: 'string',
				default: 'date',
			},
			postsOrder: {
				type: 'string',
				default: 'DESC',
			},
			borderWidth: {
				type: 'string',
				default: 'none',
			},
			borderColor: {
				type: 'string',
				default: '',
			},
			backgroundColor: {
				type: 'string',
				default: '',
			},
			textColor: {
				type: 'string',
				default: '',
			},
			decorColor: {
				type: 'string',
				default: '',
			},
			dateColor: {
				type: "string",
				default: ""
			},
			skipStartPosts: {
				type: 'number',
				default: 0,
			}
		},
		save({ attributes }){
			let gridType = "grid-type-1";
			if( attributes.layout == "list"){
				gridType = "grid-type-3";
			}
			if( attributes.layout == "simple"){
				gridType = "";
			}

			return (
				<Fragment>
	                <div className={ classNames(
	                    "wp-block-citadela-blocks ctdl-posts",
	                    gridType,
	                    [ `layout-${attributes.layout}` ],
	                    { [ `size-${attributes.size}` ]: attributes.layout == 'simple' ? false : true },
	                    { "show-item-featured-image": attributes.showFeaturedImage ? true : false },
	                    { "show-item-date": attributes.showDate ? true : false },
	                    { "show-item-description": attributes.showDescription ? true : false },
	                    { "show-item-categories": attributes.showCategories ? true : false },
	                ) } id={ attributes.blockId }>


	                    { attributes.title && <header class="citadela-block-header">
	                        <div class="citadela-block-title">
	                        <h2>{ attributes.title }</h2>
	                        </div>
	                    </header> }

	                    <ctdl-dynamic-content></ctdl-dynamic-content>
	                </div>
	            </Fragment>
			);
		}
	},
	{
		attributes: {
			category: {
				type: 'string',
				default: '',
			},
			count: {
				type: 'number',
				default: 12,
			},
			title: {
				type: 'string',
				default: '',
			},
			layout: {
				type: 'string',
				default: 'simple',
			},
			size: {
				type: 'string',
				default: 'medium',
			},
			showDate: {
				type: 'boolean',
				default: true,
			},
			showFeaturedImage: {
				type: 'boolean',
				default: true,
			},
			showDescription: {
				type: 'boolean',
				default: true,
			},
			showCategories: {
				type: 'boolean',
				default: true,
			},
			postsOrderBy: {
				type: 'string',
				default: 'date',
			},
			postsOrder: {
				type: 'string',
				default: 'DESC',
			},
		},
		save({ attributes }){
			let gridType = "grid-type-1";
			if( attributes.layout == "list"){
				gridType = "grid-type-3";
			}
			if( attributes.layout == "simple"){
				gridType = "";
			}

			return (
				<Fragment>
	                <div className={ classNames(
	                    "wp-block-citadela-blocks ctdl-posts",
	                    gridType,
	                    [ `layout-${attributes.layout}` ],
	                    { [ `size-${attributes.size}` ]: attributes.layout == 'simple' ? false : true },
	                    { "show-item-featured-image": attributes.showFeaturedImage ? true : false },
	                    { "show-item-date": attributes.showDate ? true : false },
	                    { "show-item-description": attributes.showDescription ? true : false },
	                    { "show-item-categories": attributes.showCategories ? true : false },
	                ) } id={ attributes.blockId }>


	                    { attributes.title && <header class="citadela-block-header">
	                        <div class="citadela-block-title">
	                        <h2>{ attributes.title }</h2>
	                        </div>
	                    </header> }

	                    <ctdl-dynamic-content></ctdl-dynamic-content>
	                </div>
	            </Fragment>
			);
		}
	},
	{
		attributes: {
			category: {
				type: 'string',
				default: '',
			},
			count: {
				type: 'number',
				default: 12,
			},
			title: {
				type: 'string',
				default: '',
			},
			layout: {
				type: 'string',
				default: 'simple',
			},
			size: {
				type: 'string',
				default: 'medium',
			},
			showDate: {
				type: 'boolean',
				default: true,
			},
			showFeaturedImage: {
				type: 'boolean',
				default: true,
			},
			showDescription: {
				type: 'boolean',
				default: true,
			},
			showCategories: {
				type: 'boolean',
				default: true,
			},
			postsOrderBy: {
				type: 'string',
				default: 'date',
			},
			postsOrder: {
				type: 'string',
				default: 'DESC',
			},
		},
		save({ attributes }){
			let gridType = "grid-type-1";
			if( attributes.layout == "list"){
				gridType = "grid-type-3";
			}
			if( attributes.layout == "simple"){
				gridType = "";
			}

			return (
				<Fragment>
					<div className={ classNames(
						"wp-block-citadela-blocks ctdl-posts",
						gridType,
						[ `layout-${attributes.layout}` ],
						{ [ `size-${attributes.size}` ]: attributes.layout == 'simple' ? false : true },
						{ "show-item-featured-image": attributes.showFeaturedImage ? true : false },
						{ "show-item-description": attributes.showDescription ? true : false },
						{ "show-item-categories": attributes.showCategories ? true : false },
					) } id={ attributes.blockId }>


						{ attributes.title && <header class="citadela-block-header">
							<div class="citadela-block-title">
							<h2>{ attributes.title }</h2>
							</div>
						</header> }

						<ctdl-dynamic-content></ctdl-dynamic-content>
					</div>
				</Fragment>
			);
		}
	},

];

export default deprecated;