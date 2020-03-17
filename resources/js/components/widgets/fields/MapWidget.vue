<template>
    <div style="height: 400px;">
        <l-map ref="mapInstance" :zoom="zoom" :center="center" @click="setMarkerPosition">
            <l-tile-layer :url="url" :attribution="attribution"></l-tile-layer>
            <l-marker v-show="mutableValue.lat != null || mutableValue.lng != null" ref="mapMarker" :lat-lng.sync="mutableValue" :draggable="true"></l-marker>
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
            if (this.value) {
                this.mutableValue = this.value;
            } else {
                this.$emit("input", this.mutableValue);
            }
        },
        props: {
            value: {
                type: Object,
                default: null
            },
        },
        data() {
            return {
                zoom:5,
                center: L.latLng(34.6, 51.6),
                url: 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
                attribution: '&copy; <a href="http://osm.org/copyright" target="_blank">OpenStreetMap</a> contributors',
                marker: L.latLng(34.6, 51.6),
                mutableValue: {lat: 1000, lng: 1000}
            };
        },
        methods: {
            setMarkerPosition(event) {
                this.$refs.mapMarker.setLatLng(event.latlng);
            }
        },
        watch: {
            value:{
                deep: true,
                handler(newValue, oldValue) {
                    if (typeof newValue.lat != "undefined" && typeof newValue.lng != "undefined") {
                        if (newValue.lat != this.mutableValue.lat || newValue.lng != this.mutableValue.lng) {
                            this.mutableValue = {lat: newValue.lat, lng: newValue.lng};
                            if (this.$refs.mapMarker) {
                                this.$refs.mapMarker.setLatLng(new LatLng(newValue.lat, newValue.lng));
                            }
                        }
                    }
                }
            },
            mutableValue (newValue, oldValue) {
                this.$emit("input", newValue);
            }
        },
    }
</script>

<style lang="scss">
    .leaflet-pane.leaflet-shadow-pane {
        display: none;
    }
    .leaflet-marker-shadow {
        display: none;
    }
</style>
