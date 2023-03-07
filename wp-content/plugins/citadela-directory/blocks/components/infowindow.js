const { Component, Fragment, createPortal } = wp.element;
const { __, setLocaleData } = wp.i18n;

export class Infowindow extends Component {
    componentDidMount() {
        this.el = document.createElement( 'DIV' );
        this.infowindow = new google.maps.InfoWindow({
            content: this.el,
        });
    }

    componentDidUpdate( prevProps ) {
        if ( this.props.activeMarker ) {
            this.props.activeMarker ? this.openInfowindow() : this.closeInfowindow();
        }
    }

    render() {
        const { activeMarker } = this.props;

        if (this.el && activeMarker) {
            const { title, image, permalink, address, postType } = activeMarker.point;
            return createPortal(
                <Fragment>
                    <div className={ "infoBox " + postType }>
                        <div class="infobox-content">
                            <div class="item-data">
                                <div class='infobox-title'>{ title }</div>
                                {address &&(<p>{ address }</p>)}
                                <a class='item-more-button' href={ permalink }>
                                    <span class='item-button'>{ ( postType == 'post' ? __( 'Read more', 'citadela-directory' ) : __( 'Show more', 'citadela-directory' ) ) }</span>
                                </a>
                            </div>
                            { image && (
                                <div class="item-picture">
                                    <img src={ image }/>
                                </div>
                            )}
                        </div>
                    </div>
                </Fragment>,
            this.el )
        } else {
            return null;
        }
    }

    openInfowindow() {
        const { map, activeMarker } = this.props;
        this.infowindow.open(map, activeMarker.marker);
    }

    closeInfowindow() {
		this.infowindow.remove();
	}
}

export default Infowindow;