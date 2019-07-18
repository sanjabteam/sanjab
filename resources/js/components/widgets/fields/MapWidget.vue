<template>
    <div style="height: 400px;">
        <l-map ref="themap" :zoom="zoom" :zoomControl="false" :center="center">
            <l-tile-layer :url="url"></l-tile-layer>
            <l-marker ref="marker" :lat-lng.sync="markerLatLng" :draggable="true"></l-marker>
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
        mounted () {
            var self = this;
            if (this.value) {
                this.markerLatLng = this.value;
            } else {
                this.$emit("input", this.markerLatLng);
            }
        },
        props: {
            value: {
                type: Object,
                default: () => {}
            },
        },
        data () {
            return {
                url: 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
                zoom: 4,
                center: [35.723, 53.682],
                markerLatLng: {lat: 35.723, lng:53.682},
                circle: {
                    center: [47.413220, -1.0482],
                    radius: 4500,
                    color: 'red'
                }
            };
        },
        watch: {
            value:{
                deep: true,
                handler(newValue, oldValue) {
                    if (typeof newValue.lat != "undefined" && typeof newValue.lng != "undefined") {
                        if (newValue.lat != this.markerLatLng.lat || newValue.lng != this.markerLatLng.lng) {
                            this.markerLatLng = {lat: newValue.lat, lng: newValue.lng};
                            if (this.$refs.marker) {
                                this.$refs.marker.setLatLng(new LatLng(newValue.lat, newValue.lng));
                            }
                        }
                    }
                }
            },
            markerLatLng(newValue, oldValue) {
                this.$emit("input", newValue);
            }
        },
    }
</script>

<style>
    .leaflet-control-container {
        display: none;
    }
    .leaflet-pane.leaflet-shadow-pane {
        display: none;
    }
</style>
