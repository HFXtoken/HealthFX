<template>
    <div>
        <form @change="searchPackages" @keyup.enter="searchPackages">
            <div class="input-group rowspacebottom">
                <input name="keyword" type="text" class="form-control" v-model="selectedKeyword" :placeholder="trans('frontend.search.keyword_placeholder')">
                <span class="input-group-addon social-share" @click="searchPackages">
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
                <select name="category" class="form-control" v-model="selectedCategory">
                    <option disabled value="">{{ trans('frontend.search.select_category') }}</option>
                    <option v-for="(category, key) in searchCategories" v-bind:value="key">{{ category }}</option>
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
                searchCategories: [],
                searchResults: [],
                selectedDestination: '',
                selectedLocation: '',
                selectedCategory: '',
                selectedKeyword: '',
                displayReset: false,
                listType: window.location.pathname.split("/").pop(),
                readDropdowns: true,
            }
        },

        created() {
            let self=this;
            return axios.all([self.loadDestinations(), self.loadLocations(), self.loadCategories()])
                .then(axios.spread(function (destinations, locations, categories) {
                    if(self.readDropdowns) {
                        self.searchDestinations = destinations.data;
                        self.searchLocations = locations.data;
                        self.searchCategories = categories.data;
                    }
                }));
        },

        mounted() {
            EventBus.$on('SEARCH_FILTERS', searchFilters => {
                var params = this.getSearchParams(searchFilters.searchParams);
                this.selectedCategory = ('category' in params) ? params['category'] : '';
                this.selectedDestination = unescape(('destination' in params) ? params['destination'] : '');
                this.selectedLocation = ('location' in params) ? params['location'] : '';
                this.selectedKeyword = unescape(('keyword' in params) ? params['keyword'] : '');
                if(this.selectedCategory != '' || this.selectedDestination != '' || this.selectedLocation != '' || this.selectedKeyword != '') {
                    this.displayReset = true;
                    this.searchCategories = searchFilters.updatedCategories;
                    this.searchDestinations = searchFilters.updatedDestinations;
                    this.searchLocations = searchFilters.updatedLocations;
                    this.readDropdowns = false;
                }
            });
        },

        methods: {
            searchPackages() {
                var searchUrl = Globalhealth.packageListTemplateLinkUrl + '/' + this.listType + '?category=' + this.selectedCategory + '&destination=' + this.selectedDestination + '&location=' + this.selectedLocation + '&keyword=' + this.selectedKeyword;
                var hash = location.hash;
                window.location.href = searchUrl + hash;

            },

            loadDestinations() {
                return axios.get(Globalhealth.packageSearchDestinationsUrl);
            },

            loadLocations() {
                return axios.get(Globalhealth.packageSearchLocationsUrl);
            },

            loadCategories() {
                return axios.get(Globalhealth.packageSearchCategoriesUrl);
            },

            resetFilters() {
                window.location.href = Globalhealth.packageListTemplateLinkUrl + '/' + this.listType;
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
