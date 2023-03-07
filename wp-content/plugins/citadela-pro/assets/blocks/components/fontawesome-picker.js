const { __, setLocaleData } = wp.i18n;

const { Component, Fragment, createRef } = wp.element;

export default class FontAwesomePicker extends Component {
    constructor() {
        super( ...arguments );
        this.state = {selectedIcon: this.props.selectedIcon};
        this.setIcon = this.setIcon.bind(this);
        this.inputRef = createRef();
    }
    
    componentDidMount() {
        const {
            inlinePicker = false,
        } =  this.props;

        var $container = jQuery('.citadela-fontawesome-select-container');
        var $iconpicker = $container.find('.iconpicker');
        $iconpicker.ctdlIconpicker();
        $container.find('.iconpicker-search').attr('placeholder', $iconpicker.data('search-text') );
        $container.find('.no-results').text( $iconpicker.data('noresults-text') );

        
        if(!inlinePicker){
            //manage closing of icon selection only for not inline picker
            var $iconPicker = $container.find('.selected-icon');
            var $iconPickerHolder = $container.find('.iconpicker-holder');
            $iconPicker.on('click', function(e){
                e.preventDefault();
                if($iconPickerHolder.hasClass('opened')){
                    $iconPickerHolder.removeClass('opened').slideUp();
                }else{
                    $iconPickerHolder.addClass('opened').slideDown();
                    $iconPickerHolder.find('.iconpicker-search').focus();
                }

            });
            $container.find('.iconpicker-item').on('click', function(e){
                e.preventDefault();
                $iconPickerHolder.removeClass('opened').slideUp();
            });
        }
    }

    setIcon(){
        this.props.onChange(this.inputRef.current.value);
    }

    render() {
        const {
            inlinePicker = false,
        } =  this.props;
        return (
            <Fragment>
                <div className={ "citadela-fontawesome-select-container" }>
                    { !inlinePicker &&
                        <div className={ "selected-icon" }>
                            <i class={ this.props.selectedIcon }></i>
                        </div>
                    }
                    <div onClick={this.setIcon} className={"iconpicker-holder"} /*style={ { display: 'none', }*/ >
                        <input ref={ this.inputRef } type={"hidden"} className={"iconpicker"} value={ this.props.selectedIcon } data-search-text={__('Type for search...', 'citadela-pro')} data-noresults-text={__('No results found.', 'citadela-pro')} />
                    </div>
                </div>
            </Fragment>
        );
    }
        
}