const { Component, createPortal } = wp.element;
const { __ } = wp.i18n;

export default class LeafletPopup extends Component {
    constructor() {
        super(...arguments);

        this.el = document.createElement('div');
        this.popup = L.popup({
            offset: [0, -70],
        }).setContent(this.el);
    }

    componentDidUpdate(prevProps) {
        if (this.props.on == prevProps.on) return;

        if (this.props.on) {
            this.popup.setLatLng(this.props.marker.getLatLng())
            this.props.map.openPopup(this.popup);
        }
    }

    render() {
        const { markerData } = this.props;
        
        return markerData ? createPortal(
            <div className={ "infoBox " + markerData.postType }>
                <div class="infobox-content">
                    <div class="item-data">
                        <div class='infobox-title'>{markerData.title}</div>
                        {markerData.address && (<p>{ markerData.address }</p>)}
                        <a class='item-more-button' href={ markerData.permalink }>
                            <span class='item-button'>{ ( markerData.postType == 'post' ? __( 'Read more', 'citadela-directory' ) : __( 'Show more', 'citadela-directory' ) ) }</span>
                        </a>
                    </div>
                    { markerData.image && (
                        <div class="item-picture">
                            <img src={ markerData.image }/>
                        </div>
                    )}
                </div>
            </div>,
        this.el) : null;
    }
   
}