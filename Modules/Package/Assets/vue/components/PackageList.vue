<template>
    <div>
        <div v-if="packages.length <= 0">
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

        <div class="articleListSection row" v-else v-for="package in packages">
            <div class="col-md-3 col-sm-3 col-xs-12 articleListImg pkgListImgs">
                <div class="pkgListImg">
                    <a :href="`${package.packageSlug}`"><img :src="`${package.image}`" :alt="`${package.title}`"></a>
                    <h4 v-if="package.item_label !== null">{{ package.item_label }}</h4>
                </div>
            </div>
            <div class="col-md-9 col-sm-9 col-xs-12 articleListTexts">
                <div class="row">
                    <div class="col-md-9 col-sm-9 col-xs-12 pkgListContent">
                        <div class="pkgListText">
                            <a :href="`${package.packageSlug}`"><h4>{{ package.title }}</h4></a>
                            <a :href="`${package.facilitySlug}`" v-if="package.branches.length > 1"><h5>{{ package.branches[0].name }}...</h5></a>
                            <a :href="`${package.facilitySlug}`" v-else><h5>{{ package.branches[0].name }}</h5></a>
                            <h6>{{ package.branches[0].facility.location.country }}</h6>
                            <p v-html="package.short_description"></p> <a :href="`${package.packageSlug}`">{{ trans('frontend.package.read_more') }}</a>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-12 pkgListBtnSec">
                        <div class="dollarBookNowBtn">
                            <div class="dollarDiv">
                                <h4 class="red">{{ trans('frontend.package.us_dollar') }}{{ package.price.discounted_price }}</h4>
                                <span style="text-decoration:line-through; color:red;" v-if="package.price.discounted_price !== package.price.user_price">
                                    <h6 style='color:black;'>{{ trans('frontend.package.usual_price_usd') }}{{ package.price.user_price }}</h6>
                                </span>
                            </div>
                            <div class="clearfix">
                                <img src="/themes/globalhealth/images/pkgListCalIcon.png" alt="" class="pkglistCalIcon">
                                <a href="javascript:;" @click="buyPackage(package)">{{ trans('frontend.common.btn_buy_book') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    
    import { EventBus } from '../bundle.js';
    import { mapGetters, mapMutations } from 'vuex'

    export default {

        data() {

            return {
                packages: [],
                resultList: [],
                keyword: ''
            }
        },

        created() {
            var searchParams = window.location.search;
            return axios.get(Globalhealth.packageListLinkUrl + '/{listType}' + searchParams, {
                params: {
                    listType: window.location.pathname.split("/").pop()
                }
            }).then(response => {
                this.resultList = response.data.search_result;
                this.keyword = response.data.keyword;
                this.packages = response.data.search_result.data;
                this.packages.forEach(function (packageGroup) {
                    packageGroup.packageSlug = Globalhealth.packageDetailTemplateLinkUrl + '/' + packageGroup.slug;
                    packageGroup.facilitySlug = Globalhealth.facilityDetailTemplateLinkUrl + '/' + packageGroup.branches[0].facility.slug;
                });
                //this.packages = this.getPagedData(this.resultList, 1, 8);
                EventBus.$emit('PAGINATION', {currentPageNo: this.resultList.current_page, totalPages: this.resultList.last_page, searchParams: searchParams, updatedCategories: response.data.search_categories, updatedDestinations: response.data.search_destinations, updatedLocations: response.data.search_locations});
                if(searchParams != null && searchParams != '') {
                    EventBus.$emit('SEARCH_FILTERS', {searchParams: searchParams, updatedCategories: response.data.search_categories, updatedDestinations: response.data.search_destinations, updatedLocations: response.data.search_locations});
                }
            });
        },

        computed: {
            ...mapGetters(['isUserLoggedIn'])
        },

        methods: {
            ...mapMutations(['setNextAction']),

            buyPackage(item) {
                // if(this.isUserLoggedIn) {
                //     EventBus.$emit('SHOW_PACKAGE_BUY_MODAL', {data: item});
                // } else {
                //     this.setNextAction({name: 'SHOW_PACKAGE_BUY_MODAL', data: item});
                //     EventBus.$emit('SHOW_LOGIN_MODAL');
                // }
                EventBus.$emit('SHOW_PACKAGE_BUY_MODAL', {data: item});
            }
        },

        mounted() {
            // EventBus.$on('SEARCH_RESULTS', searchResults => {
            //     this.resultList = searchResults;
            //     this.packages = this.getPagedData(this.resultList, 1, 8);
            //     EventBus.$emit('PAGINATION', {currentPageNo: 1, totalPages: this.getNumberOfPages(this.resultList.length, 8)});
            // });
            //
            // EventBus.$on('PAGE_CLICKED', pageNumber => {
            //     this.packages = this.getPagedData(this.resultList, pageNumber, 8);
            // });
        }
    }
</script>