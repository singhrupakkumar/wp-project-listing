const { __ } = wp.i18n;
const { Component, createRef } = wp.element;
const { TextControl, Spinner } = wp.components;
const { apiFetch } = wp;

export default class PostsSelection extends Component {
    
    constructor() {
        super( ...arguments );

        this.getSelectedPostsData = this.getSelectedPostsData.bind( this );
        this.searchPosts = this.searchPosts.bind( this );
        this.updateSelectedPosts = this.updateSelectedPosts.bind( this );
        this.getData = this.getData.bind( this );
    

        this.state = {
            loading: false,
            searchString: '',
            selectedPostsData: [],
            loadingSelectedPostsData: true,
            foundPosts: [],
            typing: false,
            typingTimeout: 0,
            editSelection: false,
        };

    }

    
    componentDidMount() {
        this.getSelectedPostsData();
        this.searchPosts();
        this.setState( { loading: true } );
    }


    render() {
        const { loading, searchString, foundPosts, loadingSelectedPostsData, selectedPostsData } = this.state;
        const { 
            dataType = 'posts', // type of data: posts, users, pages...
            postType = 'post', // post type in case of posts data type
            selectedPostsIds, 
            titleLabel = __('Selected posts', 'citadela-directory' ),
            searchLabel = __('Search for posts', 'citadela-directory' ),
            nothingFoundLabel = __( 'No posts found', 'citadela-directory' ),
            description = '',
            isSelected,
            singleValue,
        } = this.props;

        return (
            <>
            <div class="citadela-posts-selection-component">
                
                { selectedPostsIds.length > 0 &&
                    <div class="selected-posts">
                        <div class="header">
                            <div class="title">{titleLabel}</div>
                            { description && <div class="desc">{description}</div> }
                        </div>
                        { loadingSelectedPostsData 
                        ? 
                            <div class="loader"><Spinner/></div> 
                        : 
                            <div class="posts-list">
                            { selectedPostsData.length == 0
                            ?
                            <>
                                { singleValue
                                ?  <>{__('Selected post is no more published.', 'citadela-directory')}</>
                                :  <>{__('Selected posts are no more published.', 'citadela-directory')}</>
                                }
                            </>
                            :
                            <>
                                { 
                                selectedPostsIds.map( ( id ) => {
                                    const post_data = this.getData( id );
                                    if( post_data ){
                                        let label =  '';
                                        if( dataType == 'posts' ){
                                            label = post_data.post_title;
                                        }else if( dataType == 'users' ){
                                            label = post_data.data 
                                                ? `${post_data.data.display_name} - ${post_data.data.user_email}`
                                                : `${post_data.display_name} - ${post_data.user_email}`;
                                        }
                                        return(
                                            <div class="list-item">
                                                { isSelected && 
                                                    <div 
                                                        class="remove"
                                                        onClick={ ( event ) => { this.updateSelectedPosts( post_data, 'remove' ) } }
                                                    >
                                                        <i class="fa fa-times" aria-hidden="true"></i>
                                                    </div>
                                                }
                                                <div class="title">
                                                    {label}
                                                </div>
                                            </div>
                                        );
                                    }
                                    return '';
                                }) }
                            </>
                            }
                            </div>
                        }
                    </div>
                }
                { ( ( isSelected && selectedPostsIds.length > 0 ) || ( selectedPostsIds.length == 0 ) ) &&
                    <div class="search-posts">
                        <div class="search-bar">
                            <TextControl
                                label={ searchLabel }
                                value={ searchString }
                                onChange={ (value ) => {
                                    const self = this;
                                    if (this.state.typingTimeout) {
                                        clearTimeout(this.state.typingTimeout);
                                    }
                                
                                    this.setState({
                                        searchString: value,
                                        loading: true,
                                        typingTimeout: setTimeout(function () {
                                                self.searchPosts();
                                            }, 1000)
                                        });
                                    } }
                                />
                        </div>

                        <div class={classNames( 
                            "posts-list",
                            `data-type-${dataType}`
                        )}>
                            { loading 
                                ? <div class="loader"><Spinner/></div> 
                                : <>
                                    { this.state.foundPosts.length > 0 
                                    ?
                                        this.state.foundPosts.map( ( post_data, index ) => { 
                                            const selected = selectedPostsIds.includes( parseInt( post_data.ID ) );
                                            let label = '';
                                            if( dataType == 'posts' ){
                                                label = post_data.post_title;
                                            }else if( dataType == 'users' ){
                                                //results directly from sql query, we do not need get data same way as from wp user query!
                                                label = post_data.data 
                                                    ? `${post_data.data.display_name} - ${post_data.data.user_email}`
                                                    : `${post_data.display_name} - ${post_data.user_email}`;
                                            }
                                            return( 
                                            <div 
                                                className={classNames( 
                                                    "list-item",
                                                    selected ? 'selected' : null
                                                )}
                                                onClick={ ( event ) => { this.updateSelectedPosts( post_data, selected ? 'remove' : 'add' ) } }
                                            >
                                                <div class="title">
                                                    {label}
                                                </div>
                                            </div> 
                                        ) } )
                                        : <div class="no-posts-found">{ nothingFoundLabel }</div>
                                    }
                                </>
                            }

                        </div>
                    </div>
                }
            </div>
            </>
        );

    }


    updateSelectedPosts( data, action = 'add' ){
        const { selectedPostsIds, onChange, singleValue = false } = this.props;
        const { selectedPostsData } = this.state;
        let updatedSelectedPostsData = [...selectedPostsData];
        let updatedSelection = singleValue ? [] : [...selectedPostsIds];

        const post_id = parseInt( data.ID );
        if( action == 'remove' ){
            for (let index = 0; index < updatedSelection.length; index++) {
                if( updatedSelection[index] == post_id ){
                    updatedSelection.splice(index, 1);
                }
            }
        }else{
            updatedSelection.push(post_id);
            
            // we store here post data to load them for section with selected posts
            updatedSelectedPostsData.push(data);
            this.setState( { selectedPostsData: updatedSelectedPostsData });
        }
        onChange( updatedSelection );
    }

    
    
    componentDidUpdate( prevProps, prevState ) {

    }
    

    searchPosts(){
        const { limit = 100, selectedPostsIds, postType = 'post', dataType = 'posts' } = this.props;
        const { searchString } = this.state;
        
        let params = {};
        
        // exclude selected posts from search, posts will be displayed always on the top of selection
        let exclude_posts = [];
        if( selectedPostsIds.length ){
            exclude_posts = selectedPostsIds;
            params['exclude_posts'] = exclude_posts;
        }

        if( searchString != '' ){
            params['search'] = searchString;
        }else{
            params['limit'] = limit;
        }
        let paramsQuery = Object.keys(params)
            .map(k => encodeURIComponent(k) + '=' + encodeURIComponent(params[k]))
            .join('&');

        let path = "";
        if( dataType == 'posts' ){
            path = `/citadela-directory/posts?post_type=${postType}&${paramsQuery}`
        
        }else if( dataType == 'users' ){
            path = `/citadela-directory/users?${paramsQuery}`
        }
        
        this.fetchRequest = apiFetch( {
            path: path,
        } ).then(
            ( result ) => {
                this.setState( { foundPosts: result, loading: false, typingTimeout: 0 } );
            }
        ).catch(
            () => {
                this.setState( { foundPosts: [], loading: false, typingTimeout: 0 } );
            }
        );
    }


    getSelectedPostsData(){
        const { 
            postType, 
            selectedPostsIds,
            dataType = 'posts', // type of data: posts, users, pages...
        } = this.props;

        let params = {};

        params['selected_posts'] = selectedPostsIds;

        let paramsQuery = Object.keys(params)
            .map(k => encodeURIComponent(k) + '=' + encodeURIComponent(params[k]))
            .join('&');

        let path = "";
        if( dataType == 'posts' ){
            path = `/citadela-directory/posts?post_type=${postType}&${paramsQuery}`
        
        }else if( dataType == 'users' ){
            path = `/citadela-directory/users?${paramsQuery}`
        }

        this.fetchRequest = apiFetch( {
            path: path,
        } ).then(
            ( result ) => {
                this.setState( { selectedPostsData: result, loadingSelectedPostsData: false } );
            }
        ).catch(
            () => {
                this.setState( { selectedPostsData: [], loadingSelectedPostsData: false } );
            }
        );
    }

    
    getData( id ){
        const { selectedPostsData } = this.state;
        const { 
            dataType = 'posts', // type of data: posts, users, pages...
        } = this.props;
        for (let index = 0; index < selectedPostsData.length; index++) {
            const post = selectedPostsData[index];
            if( id == post.ID ){
                return post;
            }
        }
        return false;
    }
}