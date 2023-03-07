import { Component, createRef } from '@wordpress/element';
const { __, setLocaleData } = wp.i18n;
const { apiFetch } = wp;
import ReactResizeDetector from 'react-resize-detector';
const { debounce } = lodash;

import React from 'react';
import AsyncSelect from 'react-select/async';
import Slider from '@material-ui/core/Slider';

import { buildTermsTree } from './terms';


export class SearchForm extends Component {
    constructor() {
        super( ...arguments );

        this.state = {
            keyword: '',
            category: null,
            location: null,
            categoryOptions: [],
            locationOptions: [],
            geoRadius: null,
            geoLat: '',
            geoLon: '',
            geoEnabled: false,
            geoRadiusOpened: false,
            filtersEnabled: false,
            filtersOpened: false,
        };

        this.searchTerms = this.searchTerms.bind(this);
        this.getPositionSuccess = this.getPositionSuccess.bind(this);
        this.getPositionError = this.getPositionError.bind(this);

        this.locationInputValue = '',
        this.locations = [];
        this.categories = [];
        this.hideInputLocation = false;
        this.locationsInTree = 0;


        // Refs
        this.formRef = createRef();
        this.keywordInputRef = createRef();
        this.categoryInputRef = createRef();
        this.locationInputRef = createRef();
        this.geolocationRef = createRef();
        this.filtersRef = createRef();
        this.submitButtonRef = createRef();


        if( this.props.attributes.withAdvancedFilters && ! CitadelaDirectorySettings.features.item_extension ){
            console.warn( __('Search Results block has enabled Advanced Filters, but Item Extension feature is disabled in Citadela Listing Settings > Item Extension. Enable this feature in order to use filters with block.') );
        }
       
    }

    render() {
        const { keyword, category, location, categoryOptions, locationOptions, geoLat, geoLon, geoEnabled, geoRadiusOpened, geoRadius, filtersEnabled } = this.state;
        const { action, postType, attributes } = this.props;
        
        const {
            withAdvancedFilters,
            geoDistanceLabel,
			geoDistanceSubmitLabel,
			geoDisableLabel,
            useGeolocationInput,
            geoUnit,
            geoMax,
            geoStep,
            buttonBackgroundColor,
            buttonTextColor,
            borderColor,
            boxShadow,
            borderRadius,
            hideInputLocation,
        } = attributes;
        
        this.hideInputLocation = hideInputLocation;

        const styles = {
            ...( borderColor ? { borderColor: borderColor } : {} ),
            ...( borderRadius >= 0 ? { borderRadius: `${borderRadius}px` } : {} ),
            ...( boxShadow ? { boxShadow: boxShadow } : {} ),
        }
        const borderStyle = {
            ...( borderRadius >= 0 ? { borderRadius: `${borderRadius}px` } : {} ),
        }

        const buttonStyles = {
            ...borderStyle,
            ...( buttonBackgroundColor ? { backgroundColor: buttonBackgroundColor } : {} ),
            ...( buttonBackgroundColor ? { borderColor: buttonBackgroundColor } : {} ),
            ...( buttonTextColor ? { color: buttonTextColor } : {} ),
        }
        
        return (
            <form class="search-form" method="get" action={ action } style={ styles } ref={ this.formRef }>

                <input name="ctdl" type="hidden" value={ true } />
                <input name="post_type" type="hidden" value={ postType } />
                <ReactResizeDetector handleWidth handleHeight onResize={this.onResize} />

                <div  class="data-type-1">
                    <div class={ "input-container keyword" + (keyword ? " input-enabled" : " input-disabled") } ref={ this.keywordInputRef } style={ styles }>
                        <div class="input-data">
                            <label>{ __( "Search keyword", "citadela-directory" ) }</label>
                            <div class="directory-search-form-input">
                                <input
                                    type="text"
                                    name="s"
                                    value={ keyword }
                                    onChange={ (event) => { this.setState({ keyword: event.target.value })} }
                                    placeholder={ __( "Search keyword", "citadela-directory" ) }
                                    onFocus={ () => { this.keywordInputRef.current.classList.add('input-focused') } }
                                    onBlur={ () => { this.onBlur(this.keywordInputRef) } }
                                    style={ borderStyle }
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="data-type-2">
                    <div class={ "input-container category"  + (category ? " input-enabled" : " input-disabled") } ref={ this.categoryInputRef } style={ styles }>
                        <div class="input-data">

                            <label>{ __( "Category", "citadela-directory" ) }</label>
                            <AsyncSelect
                                name='category'
                                className='directory-search-form-select'
                                classNamePrefix='directory-search-form-select'
                                placeholder={ __( "Select Category", "citadela-directory") }
                                value={ category }
                                onChange={ (value, {action}) => this.handleChange(value, action, 'category', this.categoryInputRef) }

                                isClearable={true}
                                blurInputOnSelect={ true }
                                onMenuOpen={ () => { this.onMenuOpen(this.categoryInputRef) } }
                                onBlur={ () => { this.onBlur(this.categoryInputRef) } }

                                defaultOptions={categoryOptions}
                                loadOptions={ debounce((inputValue, callback) => (this.searchTerms(inputValue, callback, this.categories)), 500) }
                                onInputChange={ this.handleInputChange }
                            />

                        </div>
                    </div>

                    { ! hideInputLocation &&
                    <div class={ "input-container location"  + (location ? " input-enabled" : " input-disabled") } ref={ this.locationInputRef } style={ styles }>
                        <div class="input-data">

                            <label>{ __( "Location", "citadela-directory" ) }</label>
                            <AsyncSelect
                                name='location'
                                className='directory-search-form-select'
                                classNamePrefix='directory-search-form-select'
                                placeholder={ __( "Select Location", "citadela-directory") }
                                value={ location }
                                onChange={ (value, {action}) => this.handleChange(value, action, 'location', this.locationInputRef) }

                                isClearable={true}
                                blurInputOnSelect={ true }
                                onMenuOpen={ () => { this.onMenuOpen(this.locationInputRef) } }
                                onBlur={ () => { this.onBlur(this.locationInputRef) } }

                                defaultOptions={locationOptions}
                                loadOptions={ (inputValue, callback) => (this.searchTerms(inputValue, callback, this.locations)) }
                                onInputChange={ this.handleInputChange }
                            />

                        </div>
                    </div>
                    }

                    { useGeolocationInput &&
                        <div class={ "input-container geolocation"  + ( geoEnabled ? " input-enabled" : " input-disabled" ) } ref={ this.geolocationRef } style={ styles }>
                            
                            <input type="hidden" name="lat" value={geoLat} disabled={ ! geoEnabled ? "disabled" : null } />
                            <input type="hidden" name="lon" value={geoLon} disabled={ ! geoEnabled ? "disabled" : null } />
                            <input type="hidden" name="rad" value={geoRadius} disabled={ ! geoEnabled ? "disabled" : null } />
                            <input type="hidden" name="unit" value={geoUnit} disabled={ ! geoEnabled ? "disabled" : null } />

                            <div class="input-data">
                                
                                <div 
                                    class="geolocation-toggle"
                                    onClick={ () => { this.openRadiusPopup(this.geolocationRef) } }
                                >
                                    <label>{ __( "Search by geolocation", "citadela-directory" ) }</label>
                                    { geoEnabled && 
                                    <>
                                        <div class="radius-value"><span class="value">{geoRadius}</span><span class="unit">{geoUnit}</span></div>
                                    </>
                                    }
                                </div>
                                <div class="geolocation-radius">
                                    <div class="inner-wrapper">
                                        <div class="label">{ geoDistanceLabel }</div>                                    
                                        <Slider
                                            value={ geoRadius }
                                            valueLabelDisplay="off"
                                            onChange={ ( event, value ) => { this.updateRadiusValue( value ) } }
                                            step={ parseFloat( geoStep ) }
                                            min={ geoStep == 1 ? 1 : 0.1 }
                                            max={ geoMax }
                                        />
                                        <div class="buttons-wrapper">
                                        <a 
                                            class="submit-radius"
                                            onClick={ () => { this.closeRadiusPopup(this.geolocationRef) } }
                                        >{ geoDistanceSubmitLabel ? geoDistanceSubmitLabel : <i class="fas fa-check"></i> }</a>
                                        <a 
                                            class="cancel-geolocation"
                                            onClick={ () => { this.toggleGeolocationEnabled( this.geolocationRef ) } }
                                        >{ geoDisableLabel ? geoDisableLabel : <i class="fas fa-times"></i> }</a>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            
                            
                        </div>
                    }

                    { withAdvancedFilters && CitadelaDirectorySettings.features.item_extension &&
                        <div class={ "input-container advanced-filters"  + ( filtersEnabled ? " input-enabled" : " input-disabled" ) } ref={ this.filtersRef } style={ styles }>
                            <div class="input-data">
                                
                                <div 
                                    class="filters-toggle"
                                    onClick={ () => { this.openFiltersPopup( this.filtersRef ) } }
                                >
                                    <label>{ __( "Filter posts", "citadela-directory" ) }</label>
                                </div>                       
                            </div>                          
                           
                        </div>
                    }

                </div>

                <div class="data-submit">
                    <div class="input-container sf-button" ref={ this.submitButtonRef }>
                        <div class="directory-search-form-button">
                            <button onClick={ ( e ) => this.submitForm(e) } type="submit" style={ buttonStyles }>{ __( "Search", "citadela-directory" ) }</button>
                        </div>
                    </div>
                </div>
            </form>
        );
    }

    componentDidMount() {
        const { categoryTaxonomy, locationTaxonomy } = this.props;

        this.fetchTerms(categoryTaxonomy, (result, prepared) => {
            this.categories = result;
            this.setState( {categoryOptions:  prepared} );
            this.setDefaultSelectValue(prepared, 'category');
        });
        this.fetchTerms(locationTaxonomy, (result, prepared) => {
            this.locations = result;
            this.setState( {locationOptions:  prepared} );
            this.setDefaultSelectValue(prepared, 'location');
        });

        this.setGeolocationDefaultValue();
        this.setFiltersDefaultState();

        //check url parameters to fill in initial search form values
        var url = new URL(window.location.href);
        var qParameter = url.searchParams.get("s");

        if(qParameter !== null && qParameter != ''){
            //define initial input value from url parameter, we can set it right now
            this.setState({ keyword: qParameter })
        }

    }
    
    submitForm(e){
        e.preventDefault();
        if( this.state.filtersEnabled ){
            // do not submit search form directly, build url query with filters included, we pass data from search form
            citadela.advanced_filters.applyFilters( this.submitButtonRef.current, true, {
                keyword: this.state.keyword,
                category: this.state.category ? this.state.category.value : "",
                location: this.state.location ? this.state.location.value : "",
                rad: this.state.geoRadius,
                lat: this.state.geoLat,
                lon: this.state.geoLon,
                unit: this.props.attributes.geoUnit,
                geoEnabled: this.state.geoEnabled,
            } );

        }else{
            this.formRef.current.submit();
        }
    }

    setFiltersDefaultState(){
        
        var url = new URL(window.location.href);
        const {
            withAdvancedFilters,
        } = this.props.attributes;

        if( ! withAdvancedFilters ) return;

        // check if filters are enabled when they are collapsible
        const urlArgument = url.searchParams.get('a_filters');
        if( urlArgument !== null && urlArgument == 'true' ){
            this.setState( { filtersEnabled: true } );
        }
        const mainElement = this.formRef.current.closest('.wp-block-citadela-blocks');
        const filtersBlock = mainElement.querySelector('.ctdl-directory-advanced-filters');
        if( filtersBlock ){
            const submitButton = filtersBlock.querySelector('.buttons-wrapper .submit-filters');
            const disableButton = filtersBlock.querySelector('.buttons-wrapper .cancel-filters');
            submitButton.onclick = () => { this.closeFiltersPopup(this.filtersRef) };
            disableButton.onclick = () => { 
                citadela.advanced_filters.clearAllFilters();
                this.toggleFilters(this.filtersRef) 
            };
        }
    }

    toggleFilters(ref){
        const mainElement = ref.current.closest('.wp-block-citadela-blocks');
        const filtersBlock = mainElement.querySelector('.ctdl-directory-advanced-filters');
        
        // we use jquery to keep the same show/hide animation        
        jQuery(filtersBlock).trigger('advancedFiltersBlockToggle');

        this.setState( { filtersEnabled: ! this.state.filtersEnabled, filtersOpened: false } );

    }

    openFiltersPopup(ref) {
        this.closeAllPopups();
        const mainElement = ref.current.closest('.wp-block-citadela-blocks');
        const filtersBlock = mainElement.querySelector('.ctdl-directory-advanced-filters');
        if( ! this.state.filtersOpened ){
            // we use jquery to keep the same show/hide animation        
            jQuery(filtersBlock).trigger('advancedFiltersBlockToggle');
            this.setState( {
                filtersEnabled: true,
                filtersOpened: true,
            } );
        }
    }

    closeFiltersPopup(ref) {
        const mainElement = ref.current.closest('.wp-block-citadela-blocks');
        const filtersBlock = mainElement.querySelector('.ctdl-directory-advanced-filters');
        if( this.state.filtersOpened ){
            // we use jquery to keep the same show/hide animation        
            jQuery(filtersBlock).trigger('advancedFiltersBlockToggle');
            this.setState( { 
                filtersEnabled: citadela.advanced_filters.areSelectedFilters(), 
                filtersOpened: false } );
        }
    }

    updateRadiusValue( value ){
        this.setState( { geoRadius: value } );
    }

    toggleGeolocationEnabled(ref){
        const mainElement = ref.current.closest('.wp-block-citadela-blocks');
        const inputElement = ref.current;
        const newStateEnabled = ! this.state.geoEnabled;
        if( newStateEnabled ){
            mainElement.classList.add('radius-opened');
            this.setState( { 
                geoEnabled: newStateEnabled,
            } );
        }else{
            mainElement.classList.remove('radius-opened');
            this.setState( { 
                geoEnabled: newStateEnabled,
                geoRadiusOpened: false,
            } );
        }

    }
    openRadiusPopup(ref) {
        this.closeAllPopups();
        const mainElement = ref.current.closest('.wp-block-citadela-blocks');
        if( ! this.state.geoRadiusOpened ){
            mainElement.classList.add('radius-opened');
            this.setState( {
                geoEnabled: true,
                geoRadiusOpened: true,
            } );
            
            this.setGeolocationData()
        }
    }

    closeRadiusPopup(ref) {
        const mainElement = ref.current.closest('.wp-block-citadela-blocks');
        if( this.state.geoRadiusOpened ){
            mainElement.classList.remove('radius-opened');
            this.setState( { geoRadiusOpened: false } );
        }
    }

    toggleRadiusPopup(ref) {
        const mainElement = ref.current.closest('.wp-block-citadela-blocks');
        const inputElement = ref.current;
        const newStateOpened = ! this.state.geoRadiusOpened;
        if( this.state.geoEnabled ){
            //was enabled input, simply switch open state for radius options
            if( newStateOpened ){
                mainElement.classList.add('radius-opened');
            }else{
                mainElement.classList.remove('radius-opened');
            }
            this.setState( { 
                geoRadiusOpened: newStateOpened,
            } );
        }else{
            //was disabled input, enable geolocation and open radius options
            mainElement.classList.add('radius-opened');
           
            this.setState( { 
                geoEnabled: true,
                geoRadiusOpened: true,
            } );
        }
        
    }

    setGeolocationDefaultValue(){
        var url = new URL(window.location.href);
        let geoState = {};
        const {
            useGeolocationInput,
            geoMax,
            geoStep,
        } = this.props.attributes;

        if( ! useGeolocationInput ) return;

        let enableGeolocation = false;

        const urlArgumentRad = url.searchParams.get('rad');
        if( urlArgumentRad !== null && urlArgumentRad != '' && parseFloat(urlArgumentRad) > 0 && parseFloat(urlArgumentRad) <= geoMax ){
            geoState['geoRadius'] = urlArgumentRad;           
        }else{
            if( parseFloat(urlArgumentRad) > geoMax ){
                geoState['geoRadius'] = geoMax;
            }
            else if( parseFloat(urlArgumentRad) <= 0 ){
                geoState['geoRadius'] = geoStep == 1 ? 1 : 0.1;
            }else{
                geoState['geoRadius'] = geoMax / 2;
            }
        }
        
        const urlArgumentLat = url.searchParams.get('lat');
        if( urlArgumentLat !== null && urlArgumentLat != ''){
            geoState['geoLat'] = urlArgumentLat;
            enableGeolocation = true;
        }else{
            enableGeolocation = false;
        }

        const urlArgumentLon = url.searchParams.get('lon');
        if( urlArgumentLon !== null && urlArgumentLon != ''){
            geoState['geoLon'] = urlArgumentLon;
            enableGeolocation = true;
        }else{
            enableGeolocation = false;
        }
        
        if( enableGeolocation ) { 
            geoState['geoEnabled'] = enableGeolocation; 
        }

        this.setState( geoState );
    }

    setGeolocationData(){
        if(navigator.geolocation){
            navigator.geolocation.getCurrentPosition( this.getPositionSuccess, this.getPositionError );
        }
    }
    
    getPositionSuccess( position ){
        this.setState({
            geoLat: position.coords.latitude,
            geoLon: position.coords.longitude,
        });
    }

    getPositionError( error ){
        console.log( error );
        var content = __( 'Geolocation failed', 'cidatela-directory' );
		if( error ){
			if( error['code'] == 1 ){
				content = __('User denied geolocation', 'citadela-directory');
			}
            if( error['code'] == 2 ){
				content = __('Position unavailable', 'citadela-directory');
			}
		}
		alert( content );

        //make sure to reset previously filled latitude/longitude if was used

        this.setState({
            geoLat: '',
            geoLon: '',
        });

    }

    setDefaultSelectValue(options, selectName) {
        var url = new URL(window.location.href);
        var urlArgument = url.searchParams.get(selectName);
        if(urlArgument !== null && urlArgument != '') {
            options.forEach(option => {
                if (urlArgument == option.value) {
                    let obj = {};
                    obj[selectName] = { value: option.value, label: option.label}
                    this.setState(obj);
                }
            });
        }
    }

    handleInputChange = (newValue) => {
        const inputValue = newValue.replace(/\t/g, '');
        return inputValue;
    };

    searchTerms(inputValue, callback, terms) {
        let filtered = terms.filter( term =>
            term.name.toLowerCase().includes(inputValue)
        );

        let withAncestors = [];
        filtered.forEach(term => {
            withAncestors = withAncestors.concat(this.getTermAncestors(term, terms));
        });

        filtered = terms.filter( term =>
            withAncestors.includes(term.id)
        );

        const termsTree = buildTermsTree( filtered );
        const selectOptions = this.prepareSelectOptions(termsTree, {maxDepth: 999, limit: 100});

        callback(selectOptions);
    }

    getTermAncestors(term, terms) {
        let ids = [];
        ids.push(term.id);
        if (term.parent != 0) {
            const parent = terms.find(obj => {
                return obj.id == term.parent;
            });

            ids = ids.concat(this.getTermAncestors(parent, terms));
        }
        return ids;
    }

    fetchTerms(taxonomy, callback) {
        this.fetchRequest = apiFetch( {
			path: `/citadela-directory/terms?taxonomy=${taxonomy}`,
		} ).then(
			( data ) => {
                let result = data;
                if (typeof data === 'object') {
                    result = Object.keys(data).map(function(key) {
                        return data[key];
                    });
                }

                // hack because buildTermsTree library requires 'id' property and wordpress returns 'term_id'
                result.forEach(term => {
                    term['id'] = term.term_id
                });

                const termsTree = buildTermsTree( result );
                const prepared = this.prepareSelectOptions(termsTree, {maxDepth: 999, limit: null});

                callback(result, prepared);
			}
		).catch(
			(error) => {
                console.log(error);
			}
		);
    }

    prepareSelectOptions(terms, options = {maxDepth: 5, limit: 100}, result = [], spacer = '', currentDepth = 1, processedTerms = {count: 0}) {
        var helperTextarea = document.createElement('textarea');
        terms.forEach(term => {
            //dirty way to decode html in term name, api fetch return escaped value from db
            helperTextarea.innerHTML = term.name;
            term.name = helperTextarea.value;
            if ( options.limit != null && processedTerms.count > options.limit) {
                return result;
            }

            const option = { value: term.slug, label: `${spacer}${term.name}` };

            result.push(option);
            processedTerms.count += 1;
            if (term.children.length > 0 && currentDepth < options.maxDepth) {
                result = this.prepareSelectOptions(term.children, options, result, spacer +'\xa0' + '\xa0', ++currentDepth, processedTerms);
            }
        });
        return result;
    }

    onMenuOpen(ref) {
        this.closeAllPopups();
        ref.current.classList.add('input-focused');
        const mainElement = ref.current.closest('.wp-block-citadela-blocks');
        mainElement.classList.add('dropdown-opened');
        
        
    }
    
    closeAllPopups(){
        
        // close geolocation popup if opened
        if( this.state.geoRadiusOpened ) {
            this.closeRadiusPopup(this.geolocationRef);
        } 
        // close filters popup if opened
        if( this.state.filtersOpened ) {
            this.closeFiltersPopup(this.filtersRef);
        } 

    }
    onBlur(ref) {
        const mainElement = ref.current.closest('.wp-block-citadela-blocks');
        const isKeywordInput = ![this.categoryInputRef, this.locationInputRef].includes(ref);

        if (!isKeywordInput) {
            mainElement.classList.remove('dropdown-opened');
            mainElement.style.pointerEvents = "none";
        }

        ref.current.classList.remove('input-focused');

        if (isKeywordInput) return;

        setTimeout(() => {
            ref.current.querySelector('input').blur();
            mainElement.style.pointerEvents = "";

            // trigger resize of form so it can properly collapse if there is enough space after select is blured and collapsed
            this.onResize();
        }, 100);
    }

    handleChange(value, action, input, ref) {
        this.setState( {[input]: value} )
        if (action == 'clear') {
            this.onBlur(ref);
        }
    }

    onResize = () => {
        const mainElement = this.keywordInputRef.current.closest('.wp-block-citadela-blocks');
        mainElement.classList.remove('layout-collapsed');
        const offsetA = this.keywordInputRef.current.offsetTop;
        const offsetB = this.submitButtonRef.current.offsetTop;

        const threshold = this.hideInputLocation ? this.categoryInputRef.current.clientHeight / 2 : this.locationInputRef.current.clientHeight / 2;

        if ( Math.abs(offsetA - offsetB) < threshold) {
            mainElement.classList.remove('layout-collapsed');
        } else {
            mainElement.classList.add('layout-collapsed');
        }
    }
}

export default SearchForm;
