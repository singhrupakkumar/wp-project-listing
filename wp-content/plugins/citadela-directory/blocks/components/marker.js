const { Component } = wp.element;

export class Marker extends Component {

    render() {
       return null
    }

    componentDidMount() {
        this.renderMarker();
    }

    componentDidUpdate() {
        this.renderMarker();
    }

    renderMarker() {
        const { map, point } = this.props;

        if (!this.marker) {
            this.marker = new MarkerWithLabel({
                position: {lat: point.coordinates.latitude, lng: point.coordinates.longitude},
                map: map,
                labelContent: '<i class="fa fa-'+point.faIcon+'"></i>',
                labelClass: "fa-map-label",
                icon: this.renderIcon(),
            });
        }
    }

    renderIcon() {
        const { point } = this.props;
        return {
            path: 'M.17,24.83A24.83,24.83,0,1,1,43.51,41.38C39,46.46,25,60,25,60S11,46.46,6.49,41.38A24.74,24.74,0,0,1,.17,24.83Z',
            anchor: {x: 25, y: 60},
            fillColor: point.color,
            fillOpacity: 1,
            scale: 1,
            labelInBackground: false,
            strokeColor: '',
            strokeWeight: 0,
        }
    }

    clickHandler = () => {
        const { onClick } = this.props;
        onClick( this );
    };
}

export default Marker;