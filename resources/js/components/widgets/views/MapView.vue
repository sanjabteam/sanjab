<template>
    <div style="height: 400px;">
        <l-map ref="themap" :zoom="zoom" :zoomControl="false" :center="center">
            <l-tile-layer :url="url"></l-tile-layer>
            <l-marker v-if="markerLatLng" ref="marker" :lat-lng.sync="markerLatLng" :draggable="false"></l-marker>
        </l-map>
    </div>
</template>

<script>
    import { LatLng } from 'leaflet';
    import { LMap, LTileLayer, LMarker, LCircle } from 'vue2-leaflet';

    export default {
        components: {
            LMap,
            LTileLayer,
            LMarker,
            LCircle
        },
        props: {
            data: {
                type: Object,
                default: () => {return {};}
            },
            widget: {
                type: Object,
                default: () => {return {};}
            },
        },
        data () {
            return {
                url: 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
                zoom: 4,
                center: [35.723, 53.682]
            };
        },
        computed: {
            markerLatLng() {
                return this.data[this.widget.name] ? this.data[this.widget.name] : null;
            }
        },
    }
</script>
