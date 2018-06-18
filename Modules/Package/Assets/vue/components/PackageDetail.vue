<template>
    <div class="pkgDtlLeftPanel row">
        <div class="col-md-9 col-sm-9 col-xs-12 paddingZero">
            <h5 class="pkgDtlLbl" v-if="packageDetail.item_label !== null">{{ packageDetail.item_label }}</h5>

            <h3>{{ packageDetail.title }}</h3>

            <div v-if="packageDetail.branches.length > 1" v-for="packageDetailFacility in packageDetail.branches" class="pkgDtlProvName">
                <a :href="`${packageDetailFacility.facilitySlug}`"><h6>{{ packageDetailFacility.name }}</h6>, <span>{{ packageDetailFacility.facility.location.country }}</span></a>
            </div>

            <div v-else v-for="packageDetailFacility in packageDetail.branches">
                <a :href="`${packageDetailFacility.facilitySlug}`"><h6>{{ packageDetailFacility.name }}</h6></a><br/>
                <span>{{ packageDetailFacility.facility.location.country }}</span>
            </div>

        </div>

        <div class="col-md-3 col-sm-3 col-xs-12 paddingZero">
            <div class="dollarBookNowBtn">
                <div class="dollarDiv" v-if="packageDetail.price">
                    <span>{{ trans('frontend.common.mastercard') }}</span>
                    <h4 class="red">{{ trans('frontend.package.us_dollar') }}{{ packageDetail.price.discounted_price }}</h4>
                    <span style="text-decoration:line-through; color:red;" v-if="packageDetail.price.discounted_price !== packageDetail.price.user_price">
                        <h5 style='color:black;'>{{ trans('frontend.package.usual_price_usd') }}{{ packageDetail.price.user_price }}</h5>
                    </span>
                </div>
                <div class="clearfix">
                    <img src="/themes/globalhealth/images/pkgListCalIcon.png" alt="" class="pkglistCalIcon">
                    <a href="javascript:;" @click="buyPackage(packageDetail)">{{ trans('frontend.common.btn_buy_book') }}</a>
                </div>
            </div>
        </div>

        <div class="clearfix" style="margin: 0 10px;"></div>
        <hr>
        <div class="drDetailContentMainDiv">
            <div class="hopsDtlContText">
                <div class="pkgDetConSlider hospDetlSlider">
                    <div class="" v-if="packageDetail.gallery && packageDetail.gallery.length > 0">
                        <carousel :navigationEnabled="true" :paginationEnabled="false" :perPage="1">
                            <slide v-for="(gallery, index) in packageDetail.gallery" :key="index">
                                <img :src="`${gallery.path_string}`" :alt="`${packageDetail.title}`" class="img-responsive">
                            </slide>
                        </carousel>
                    </div>

                    <div class="pkgDtlGalOnlyImg" v-else>
                        <img :src="`${packageDetail.image}`" :alt="`${packageDetail.title}`">
                    </div>
                </div>

                <div class="hospDetlContent" style="border-bottom: 0 !important;">

                    <div v-html="packageDetail.description"></div>

                    <div class="termsCondText">
                        <div v-html="packageDetail.terms_and_conditions"></div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</template>


<script>

    import { EventBus } from '../bundle.js';
    import { Carousel, Slide } from 'vue-carousel';
    import { mapGetters, mapMutations } from 'vuex';

    export default {

        components: { Carousel, Slide },

        data() {
            return {

                packageDetail: []
            }
        },

        created() {
            return axios.get(Globalhealth.packageDetailLinkUrl + '/{slug}', {
                params: {
                    slug: window.location.pathname.split("/").pop()
                }
            }).then(response => {
                this.packageDetail = response.data;
                this.packageDetail.branches.forEach(function (branch) {
                    branch.facilitySlug = Globalhealth.facilityDetailTemplateLinkUrl + '/' + branch.facility.slug;
                });
                EventBus.$emit('DETAIL_TITLE', this.packageDetail.title);
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
    }
</script>