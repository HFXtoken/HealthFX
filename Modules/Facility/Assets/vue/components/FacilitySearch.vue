<template>
    <div>
        <form @change="searchFacilities" @keyup.enter="searchFacilities">
            <div class="input-group rowspacebottom">
                <input name="keyword" type="text" class="form-control" v-model="selectedKeyword" :placeholder="trans('frontend.search.keyword_placeholder')">
                <span class="input-group-addon social-share" @click="searchFacilities">
                    <i class="fa fa-search fa-flip-horizontal" aria-hidden="true"></i>
                </span>
            </div>
            <div class="input-group rowspacebottom fullWidth">
                <select name="destination" class="form-control" v-model="selectedDestination">
                    <option disabled value="">{{ trans('frontend.search.select_destination') }}</option>
                    <option v-for="(destination, key) in searchDestinations" v-bind:value="key">{{ destination }}</option>
                </select>
            </div>
            <div class="input-group rowspacebottom fullWidth" v-if="selectedDestination.length > 0">
                <select name="location" class="form-control" v-model="selectedLocation">
                    <option disabled value="">{{ trans('frontend.search.select_location') }}</option>
                    <option v-for="(location, key) in searchLocations" v-bind:value="key">{{ location }}</option>
                </select>
            </div>
            <div class="input-group rowspacebottom fullWidth">
                <select name="specialty" class="form-control" v-model="selectedSpeciality">
                    <option disabled value="">{{ trans('frontend.search.select_speciality') }}</option>
                    <option v-for="(speciality, key) in searchSpecialities" v-bind:value="key">{{ speciality }}</option>
                </select>
            </div>
        </form>

        <div v-show="displayReset" class="resetLink">
            <span @click="resetFilters"><h6>{{ trans('frontend.search.reset_filters') }}</h6></span>
        </div>
    </div>
</template>


<script>

    import { EventBus } from '../bundle.js';

    export default {

        data() {
            return {
                searchDestinations: [],
                searchLocations: [],
                searchSpecialities: [],
                selectedDestination: '',
                selectedLocation: '',
                selectedSpeciality: '',
                selectedKeyword: '',
                displayReset: false,
                readDropdowns: true,
            }
        },

        created() {
            let loader = this.$loading.show();
            let self=this;
            return axios.all([self.loadDestinations(), self.loadLocations(), self.loadSpecialities()])
                .then(axios.spread(function (destinations, locations, specialities) {
                    if(self.readDropdowns) {
                        self.searchDestinations = destinations.data;
                        self.searchLocations = locations.data;
                        self.searchSpecialities = specialities.data;
                    }

                    loader.hide();
                }));
        },

        mounted() {
            EventBus.$on('SEARCH_FILTERS', searchFilters => {
                var params = this.getSearchParams(searchFilters.searchParams);
                this.selectedSpeciality = ('speciality' in params) ? params['speciality'] : '';
                this.selectedDestination = unescape(('destination' in params) ? params['destination'] : '');
                this.selectedLocation = ('location' in params) ? params['location'] : '';
                this.selectedKeyword = unescape(('keyword' in params) ? params['keyword'] : '');
                if(this.selectedSpeciality != '' || this.selectedDestination != '' || this.selectedLocation != '' || this.selectedKeyword != '') {
                    this.displayReset = true;
                    this.searchSpecialities = searchFilters.updatedSpecialities;
                    this.searchDestinations = searchFilters.updatedDestinations;
                    this.searchLocations = searchFilters.updatedLocations;
                    this.readDropdowns = false;
                }
            });
        },

        methods: {
            searchFacilities() {
                var searchUrl = Globalhealth.facilityListTemplateLinkUrl + '?speciality=' + this.selectedSpeciality + '&destination=' + this.selectedDestination + '&location=' + this.selectedLocation + '&keyword=' + this.selectedKeyword;
                var hash = location.hash;
                window.location.href = searchUrl + hash;

            },

            loadDestinations() {
                return axios.get(Globalhealth.facilitySearchDestinationsUrl);
            },

            loadLocations() {
                return axios.get(Globalhealth.facilitySearchLocationsUrl);
            },

            loadSpecialities() {
                return axios.get(Globalhealth.facilitySearchSpecialitiesUrl);
            },

            resetFilters() {
                window.location.href = Globalhealth.facilityListTemplateLinkUrl;
            }, 

            getSearchParams(queryStr) {
                var queryArr = queryStr.replace('?','').split('&'),
                    queryParams = [];

                for (var q = 0, qArrLength = queryArr.length; q < qArrLength; q++) {
                    var qArr = queryArr[q].split('=');
                    queryParams[qArr[0]] = qArr[1];
                }

                return queryParams;
            },
        }

    }
</script>
