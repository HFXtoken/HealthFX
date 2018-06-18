<template>
    <div>
        <div v-if="facilityList.length <= 0">
            <div v-if="keyword && (keyword !== null || keyword !== '')">
                <span style="font-size: 24px;">Sorry! No Results Found.</span>
                <p style="margin-top: 35px; font-size: 18px;">Suggestions:</p>
                <div style="margin-left: 40px;">
                    <ul style="list-style: disc;">
                        <li>Make sure all words are spelled correctly.</li>
                        <li>Reset Filters and try again.</li>
                        <li>Try different keywords.</li>
                        <li>Try more general keywords.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="findCareListMainDiv" v-else v-for="(facility, index) in facilityList">
            <div class="row findCareListNestedRow">
                <div class="col-md-5 col-sm-5 col-xs-12 providerContentLeft">
                    <div class="faclProvImg">
                        <img :src="`${facility.image}`" :alt="`${facility.name}`">
                    </div>
                    <!-- star rating html -->
                    <!--<fieldset class="rating">-->
                        <!--<input type="radio" id="star5" name="rating" value="5" /><label class = "full" for="star5" title="Awesome - 5 stars"></label>-->
                        <!--<input type="radio" id="star4half" name="rating" value="4 and a half" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>-->
                        <!--<input type="radio" id="star4" name="rating" value="4" /><label class = "full" for="star4" title="Pretty good - 4 stars"></label>-->
                        <!--<input type="radio" id="star3half" name="rating" value="3 and a half" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label>-->
                        <!--<input type="radio" id="star3" name="rating" value="3" /><label class = "full" for="star3" title="Meh - 3 stars"></label>-->
                        <!--<input type="radio" id="star2half" name="rating" value="2 and a half" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>-->
                        <!--<input type="radio" id="star2" name="rating" value="2" /><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>-->
                        <!--<input type="radio" id="star1half" name="rating" value="1 and a half" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label>-->
                        <!--<input type="radio" id="star1" name="rating" value="1" /><label class = "full" for="star1" title="Sucks big time - 1 star"></label>-->
                        <!--<input type="radio" id="starhalf" name="rating" value="half" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>-->
                    <!--</fieldset>-->
                    <div class="bookEnquireBtns clearfix">
                        <a href="javascript:;" class="bookBtn" @click="bookFacility(facility)">| {{ trans('frontend.common.btn_book') }}</a>
                        <a class="enquireBtn" href="javascript:;" @click="sendFacilityEnquiry(facility)">| {{ trans('frontend.common.btn_enquire') }}</a>
                    </div>
                </div>
                <div class="col-md-7 col-sm-7 col-xs-12 providerContentRight">
                    <div class="clearfix provierNames">
                        <a :href="`${facility.facilitySlug}`"><h4>{{ facility.name }}</h4></a>
                        <a href="javascript:;" class="searchBookMarkIcon" @click="toggleBookmark(facility.id)">
                            <i v-if="bookmarkedList[facility.id]" class="fa fa-bookmark fa-2x" aria-hidden="true"></i>
                            <i v-else class="fa fa-bookmark-o fa-2x" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="prov-CountryName-SrtDesc clearfix">
                        <span class="provCountryName">{{ facility.location.country }}</span>
                        <span class="provSortDesc">#{{ getProviderRanking(index+1) }} of {{ resultCount }} Provider(s) in {{ selectedDestination | capitalize }}</span>
                    </div>
                    <p class="providerDetail" v-html="facility.short_description"></p> <a :href="`${facility.facilitySlug}`" class="magReadmoreBtn">{{ trans('frontend.facility.read_more') }}</a>
                    <h5 class="provLocation">{{ trans('frontend.facility.location') }}</h5>
                    <p class="provAddress">{{ facility.primary_branch[0].location.address_1 }}, {{ facility.primary_branch[0].location.address_2 }}, {{ facility.primary_branch[0].location.postcode }}</p>
                    <div class="row opnHrsClnclFcs">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <h5 class="openHrs">{{ trans('frontend.facility.opening_hours') }}</h5>
                            <p v-for="timing in facility.primary_branch[0].branch_timings">
                                {{ timing.start_day | truncate('3') }} {{ timing.end_day| truncate('6') }} - {{ timing.start_time| truncate('5') }} - {{ timing.end_time| truncate('5') }}
                            </p>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <h5 class="clinicalFocus">{{ trans('frontend.facility.clinical_focus') }}</h5>
                            <!--<div style="overflow-y: scroll; height: 36px;" v-if="facility.primary_branch[0].specialities.length > 1">-->
                                <p v-if="facility.primary_branch[0].specialities.length > 1">Multiple</p>
                            <!--</div>-->
                            <p v-for="speciality in facility.primary_branch[0].specialities" v-else>{{ speciality.name }}</p>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <!-- Google Map Modal -->
                            <a href="javascript:;" @click="openModal(facility.id)" class="mapLink">{{ trans('frontend.facility.map') }}</a>
                            <section>
                                <modal v-model="modalList[facility.id]" size="lg" :header="false" :footer="false" @show="showMap(facility.primary_branch[0].slug)">
                                    <google-map :name="facility.primary_branch[0].slug" :lat="facility.primary_branch[0].location.lat" :lng="facility.primary_branch[0].location.long" ></google-map>
                                </modal>
                            </section>
                        </div>
                    </div>
                </div>
            </div>

            <linked-packages :packages="facility.linked_packages" :detailViewAllLink="facility.facilitySlug"></linked-packages>

            <linked-doctors :doctors="facility.linked_doctors" :detailViewAllLink="facility.facilitySlug"></linked-doctors>
        </div>
    </div>
</template>


<script>
    
    import { EventBus } from '../bundle.js';

    import LinkedPackages from './LinkedPackages';
    import LinkedDoctors from './LinkedDoctors';
    import GoogleMap from './GoogleMap';
    import { mapGetters, mapMutations } from 'vuex'

    export default {

        components: { LinkedPackages, LinkedDoctors, GoogleMap },

        data() {

            return {

                facilityList: [],
                resultList: [],
                modalList: [],
                bookmarkedList: [],
                resultCount: '',
                selectedDestination: '',
                currentPageNumber: 1,
                keyword: ''
            }   
        },

        computed: {
            ...mapGetters(['isUserLoggedIn', 'getCurrentUser'])
        },

        methods: {
            ...mapMutations(['setNextAction']),

            bookFacility(facility) {
                if(this.isUserLoggedIn) {
                    EventBus.$emit('SHOW_FACILITY_BOOKING_MODAL', {data: facility});
                } else {
                    this.setNextAction({name: 'SHOW_FACILITY_BOOKING_MODAL', data: facility});
                    EventBus.$emit('SHOW_LOGIN_MODAL');
                }
            },

            sendFacilityEnquiry(facility) {
                if(this.isUserLoggedIn) {
                    EventBus.$emit('SHOW_FACILITY_ENQUIRY_MODAL', {data: facility});
                } else {
                    this.setNextAction({name: 'SHOW_FACILITY_ENQUIRY_MODAL', data: facility});
                    EventBus.$emit('SHOW_LOGIN_MODAL');
                }
            },

            openModal(id) {
                this.$set(this.modalList, id, true);
            },

            showMap(mapName) {
                EventBus.$emit('SHOW_MAP_MODAL', {name: mapName});
            },

            toggleBookmark(id) {
                if(this.isUserLoggedIn) {
                    this.updateBookmark(id);
                } else {
                    this.setNextAction({name: 'UPDATE_BOOKMARK', data: id});
                    EventBus.$emit('SHOW_LOGIN_MODAL');
                }
            },

            updateBookmark(id) {
                let loader = this.$loading.show();

                var url = Globalhealth.userToggleBookmarkLinkUrl.replace('param1', 'facility').replace('param2', id);
                return axios.get(url)
                    .then(response => {
                        this.$set(this.bookmarkedList, id, response.data.status);

                        loader.hide();
                    });
            },

            populateBookmarks() {
                if(this.isUserLoggedIn && this.getCurrentUser.profile.bookmarks != null) {
                    this.getCurrentUser.profile.bookmarks.forEach( function (bookmark) {
                        if(bookmark.bookmark_type === 'facility') {
                            this.$set(this.bookmarkedList, bookmark.bookmarked_id, true);
                        }
                    }, this)
                }
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

            getProviderRanking(rank) {
                return (this.currentPageNumber-1) * 5 + rank;
            }
        },

        filters: {
            capitalize(value) {
                if (!value) return '';
                value = value.toString();
                return value.charAt(0).toUpperCase() + value.slice(1);
            }
        },

        created() {

            let loader = this.$loading.show();

            var searchParams = window.location.search;

            var params = this.getSearchParams(searchParams);
            this.selectedDestination = ('destination' in params) ? decodeURI(params['destination']) : 'all countries';
            if(this.selectedDestination == '') {
                this.selectedDestination = 'all countries';
            }

            return axios.get(Globalhealth.facilityListLinkUrl + searchParams)
                .then(response => {

                    this.resultList = response.data.search_result;

                    this.keyword = response.data.keyword;

                    this.facilityList = response.data.search_result.data;

                    this.resultCount = response.data.search_result.total;

                    this.currentPageNumber = this.resultList.current_page;

                    this.facilityList.forEach(function (facility) {
                        facility.facilitySlug = Globalhealth.facilityDetailTemplateLinkUrl + '/' + facility.slug;
                        this.modalList[facility.id] = false;

                    }, this);
                    //this.facilityList = this.getPagedData(this.resultList, 1, 5);
                    EventBus.$emit('PAGINATION', {module: 'facilities', currentPageNo: this.resultList.current_page, totalPages: this.resultList.last_page, searchParams: searchParams, updatedDestinations: response.data.search_destinations, updatedLocations: response.data.search_locations, updatedSpecialities: response.data.search_specialities});
                    if(searchParams != null && searchParams != '') {
                        EventBus.$emit('SEARCH_FILTERS', {searchParams: searchParams, updatedDestinations: response.data.search_destinations, updatedLocations: response.data.search_locations, updatedSpecialities: response.data.search_specialities});
                    }

                    loader.hide();
                });
        },

        mounted() {
            // EventBus.$on('SEARCH_RESULTS', searchResults => {
            //     this.resultList = searchResults;
            //     this.facilityList = this.getPagedData(this.resultList, 1, 5);
            //     EventBus.$emit('PAGINATION', {currentPageNo: 1, totalPages: this.getNumberOfPages(this.resultList.length, 5)});
            // });

            // EventBus.$on('PAGE_CLICKED', pageNumber => {
            //     this.facilityList = this.getPagedData(this.resultList, pageNumber, 5);
            //     this.currentPageNumber = pageNumber;
            //     this.fetchData();
            // });

            EventBus.$on('UPDATE_BOOKMARK', data => this.updateBookmark(data));

            EventBus.$on('USER_LOGIN_COMPLETE', data => this.populateBookmarks());

        },
    }
</script>