<template>
    <div style="margin-top: 10px;">
        <form @change="searchDoctors" @keyup.enter="searchDoctors">
            <div class="input-group rowspacebottom">
                <input name="keyword" type="text" class="form-control txt-search" v-model="selectedKeyword" :placeholder="trans('frontend.search.keyword_placeholder')">
                <span class="input-group-addon social-share" @click="searchDoctors">
                    <i class="fa fa-search fa-flip-horizontal" aria-hidden="true"></i>
                </span>
            </div>
            <div class="input-group rowspacebottom fullWidth">
                <select name="destination" class="form-control" v-model="selectedDestination">
                    <option disabled value="">{{ trans('frontend.search.select_destination') }}</option>
                    <option v-for="(destination, key) in searchDestinations" v-bind:value="key">{{ destination }}</option>
                </select>
            </div>
            <div class="input-group rowspacebottom fullWidth">
                <select name="specialty" class="form-control" v-model="selectedSpeciality">
                    <option disabled value="">{{ trans('frontend.search.select_speciality') }}</option>
                    <option v-for="(speciality, key) in searchSpecialities" v-bind:value="key">{{ speciality }}</option>
                </select>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-default btn-content" :class="{ 'btn-primary active': isMale === true }" @click.prevent="selectType('male')">
                    <span><i class="fa fa-male fa-2x" aria-hidden="true"></i></span>
                </button>
                <button type="button" class="btn btn-default btn-content" :class="{ 'btn-primary active': isMale === false }" @click.prevent="selectType('female')">
                    <span><i class="fa fa-female fa-2x" aria-hidden="true"></i></span>
                </button>
            </div>
        </form>

        <div v-show="displayReset" class="resetLink">
            <span v-on:click="resetFilters"><h6>{{ trans('frontend.search.reset_filters') }}</h6></span>
        </div>
    </div>
</template>


<script>

    import { EventBus } from '../bundle.js';

    export default {

        data() {
            return {
                searchDestinations: [],
                searchSpecialities: [],
                selectedDestination: '',
                selectedSpeciality: '',
                selectedKeyword: '',
                selectedGender: '',
                displayReset: false,
                readDropdowns: true,
                isMale: ''
            }
        },

        created() {
            let loader = this.$loading.show();
            let self=this;
            return axios.all([self.loadDestinations(), self.loadSpecialities()])
                .then(axios.spread(function (destinations, specialities) {
                    if(self.readDropdowns) {
                        self.searchDestinations = destinations.data;
                        self.searchSpecialities = specialities.data;
                    }

                    loader.hide();
                }));
        },

        mounted() {
            EventBus.$on('SEARCH_FILTERS', searchFilters => {
                var params = this.getSearchParams(searchFilters.searchParams);
                this.selectedSpeciality = ('speciality' in params) ? params['speciality'] : '';
                this.selectedDestination = ('destination' in params) ? params['destination'] : '';
                this.selectedGender = ('gender' in params) ? params['gender'] : '';
                this.selectedKeyword = unescape(('keyword' in params) ? params['keyword'] : '');
                this.isMale = params['gender'] === 'male' ? true : (params['gender'] === 'female' ? false : '');
                if(this.selectedSpeciality != '' || this.selectedDestination != '' || this.selectedGender != '' || this.selectedKeyword != '') {
                    this.displayReset = true;
                    this.searchSpecialities = searchFilters.updatedSpecialities;
                    this.searchDestinations = searchFilters.updatedLocations;
                    this.readDropdowns = false;
                }
            });
        },

        methods: {
            searchDoctors() {
                var searchUrl = Globalhealth.doctorListTemplateLinkUrl + '?speciality=' + this.selectedSpeciality + '&destination=' + this.selectedDestination + '&gender=' + this.selectedGender + '&keyword=' + this.selectedKeyword;
                var hash = location.hash;
                window.location.href = searchUrl + hash;

            },

            loadDestinations() {
                return axios.get(Globalhealth.doctorSearchDestinationsUrl);
            },

            loadSpecialities() {
                return axios.get(Globalhealth.doctorSearchSpecialitiesUrl);
            },

            resetFilters() {
                window.location.href = Globalhealth.doctorListTemplateLinkUrl;
            },  

            selectType(type) {
                this.selectedGender = type;
                this.searchDoctors();
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
