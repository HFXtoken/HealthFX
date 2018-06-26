<template>
    <div>
        <div v-if="doctorList.length <= 0">
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

        <div class="findCareListMainDiv" v-else v-for="doctor in doctorList">
            <div class="row findCareListNestedRow">
                <div class="col-md-5 col-sm-5 col-xs-12 providerContentLeft mrgnBtmZero">
                    <div class="provDocImgs">
                        <img :src="`${doctor.image}`" :alt="`${doctor.first_name}`">
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
                        <a class="bookBtn" href="javascript:;" @click="bookDoctor(doctor)">| {{ trans('frontend.common.btn_book') }}</a>
                        <a class="enquireBtn" href="javascript:;" @click="sendDocEnquiry(doctor)">| {{ trans('frontend.common.btn_enquire') }}</a>
                    </div>
                </div>
                <div class="col-md-7 col-sm-7 col-xs-12 providerContentRight">
                    <div class="clearfix docNames">
                        <a :href="`${doctor.doctorSlug}`"><h4>{{ doctor.first_name }} {{ doctor.last_name }}</h4></a>
                        <a href="javascript:;" class="searchBookMarkIcon" @click="toggleBookmark(doctor.id)">
                            <i v-if="bookmarkedList[doctor.id]" class="fa fa-bookmark fa-2x" aria-hidden="true"></i>
                            <i v-else class="fa fa-bookmark-o fa-2x" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="prov-CountryName-SrtDesc clearfix">
                        <span class="provCountryName">{{ doctor.primary_branch.facility.location.country }}</span>
                    </div>
                    <p class="providerDetail" v-html="doctor.short_professional_statement"></p>
                    <h5 class="provLocation">{{ trans('frontend.doctor.location') }}</h5>
                    <p class="doctorAddress">{{ doctor.primary_branch.name }} <br/> {{ doctor.primary_branch.location.address_1 }}, {{ doctor.primary_branch.location.address_2 }}, {{ doctor.primary_branch.location.postcode }}</p>
                    <div class="row opnHrsClnclFcs">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <h5 class="openHrs">{{ trans('frontend.doctor.opening_hours') }}</h5>
                            <p v-for="timing in doctor.primary_branch.timings">
                                {{ timing.start_day | truncate('3') }} {{ timing.end_day| truncate('6') }} - {{ timing.start_time| truncate('5') }} - {{ timing.end_time| truncate('5') }}
                            </p>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <h5 class="clinicalFocus">{{ trans('frontend.doctor.clinical_focus') }}</h5>
                            <p>{{ doctor.primary_speciality.name }}</p>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-12">
                            <a href="javascript:;" @click="openModal(doctor.id)" class="mapLink">{{ trans('frontend.doctor.map') }}</a>
                            <section>
                                <modal v-model="modalList[doctor.id]" size="lg" :header="false" :footer="false" @show="showMap(doctor.primary_branch.slug)">
                                    <google-map :name="doctor.primary_branch.slug" :lat="doctor.primary_branch.location.lat" :lng="doctor.primary_branch.location.long" ></google-map>
                                </modal>
                            </section>
                        </div>
                    </div>
                </div>
            </div>

            <related-facility :facilities="doctor.linked_facilities" :detailViewAllLink="doctor.doctorSlug"></related-facility>
        </div>
    </div>
</template>


<script>
    
    import { EventBus } from '../bundle.js';
    import RelatedFacility from './RelatedFacility';
    import GoogleMap from './GoogleMap';
    import { mapGetters, mapMutations } from 'vuex'

    export default {

        data() {
            return {
                
                doctorList: [],
                resultList: [],
                modalList: [],
                bookmarkedList: [],
                keyword: ''
            }
        },

        created() {
            let loader = this.$loading.show();
            var searchParams = window.location.search;
            return axios.get(Globalhealth.doctorListLinkUrl + searchParams)
                .then(response => {
                    this.resultList = response.data.search_result;
                    this.keyword = response.data.keyword;
                    this.doctorList = response.data.search_result.data;
                    this.doctorList.forEach(function (doctor) {
                        doctor.doctorSlug = Globalhealth.doctorDetailTemplateLinkUrl + '/' + doctor.slug;
                        this.modalList[doctor.id] = false;
                    }, this)
                    //this.doctorList = this.getPagedData(this.resultList, 1);
                    EventBus.$emit('PAGINATION', {module: 'doctors', currentPageNo: this.resultList.current_page, totalPages: this.resultList.last_page, searchParams: searchParams, updatedLocations: response.data.search_locations, updatedSpecialities: response.data.search_specialities});
                    if(searchParams != null && searchParams != '') {
                        EventBus.$emit('SEARCH_FILTERS', {searchParams: searchParams, updatedLocations: response.data.search_locations, updatedSpecialities: response.data.search_specialities});
                    }

                    loader.hide();
                });
        },

        computed: {
            ...mapGetters(['isUserLoggedIn', 'getCurrentUser'])
        },

        methods: {
            ...mapMutations(['setNextAction']),

            bookDoctor(doctor) {
                // this.fetchSelectedDoc(doctor);
                if(this.isUserLoggedIn) {
                    EventBus.$emit('SHOW_DOCTOR_BOOKING_MODAL', {data: doctor});
                } else {
                    this.setNextAction({name: 'SHOW_DOCTOR_BOOKING_MODAL', data: doctor});
                    EventBus.$emit('SHOW_LOGIN_MODAL');
                }
            },

            sendDocEnquiry(doctor) {
                // this.fetchSelectedDoc(doc);
                if(this.isUserLoggedIn) {
                    EventBus.$emit('SHOW_DOCTOR_ENQUIRY_MODAL', {data: doctor});
                } else {
                    this.setNextAction({name: 'SHOW_DOCTOR_ENQUIRY_MODAL', data: doctor});
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
                var url = Globalhealth.userToggleBookmarkLinkUrl.replace('param1', 'doctor').replace('param2', id);
                return axios.get(url)
                    .then(response => {
                        this.$set(this.bookmarkedList, id, response.data.status);
                        loader.hide();
                    });
            },

            populateBookmarks() {
                if(this.isUserLoggedIn && this.getCurrentUser.profile.bookmarks != null) {
                    this.getCurrentUser.profile.bookmarks.forEach( function (bookmark) {
                        if(bookmark.bookmark_type === 'doctor') {
                            this.$set(this.bookmarkedList, bookmark.bookmarked_id, true);
                        }
                    }, this)
                }
            },
        },

        mounted() {
            // EventBus.$on('SEARCH_RESULTS', searchResults => {
            //     this.resultList = searchResults;
            //     this.doctorList = this.getPagedData(this.resultList, 1);
            //     EventBus.$emit('PAGINATION', {currentPageNo: 1, totalPages: this.getNumberOfPages(this.resultList.length)});
            // });
            //
            // EventBus.$on('PAGE_CLICKED', pageNumber => {
            //     this.doctorList = this.getPagedData(this.resultList, pageNumber);
            // });

            EventBus.$on('UPDATE_BOOKMARK', data => this.updateBookmark(data));

            EventBus.$on('USER_LOGIN_COMPLETE', data => this.populateBookmarks());
        },

        components: { RelatedFacility, GoogleMap }
    }
</script>