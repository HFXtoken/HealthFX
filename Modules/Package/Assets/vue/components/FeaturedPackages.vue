<template>
    <div class="ftrdPkgSection clearfix" v-if="featuredPackages && featuredPackages.length > 0">

        <h2>{{ trans('frontend.package.featured_packages') }}</h2>

        <div class="row">
            <div class="pkgLandingFtrdPkgSection" v-for="featuredPackage in featuredPackages">
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="pkgLandgBox">
                        <a :href="`${featuredPackage.packageSlug}`"><img :src="`${featuredPackage.image}`" :alt="`${featuredPackage.title}`"></a>
                    </div>
                    <div class="pkgLandgPkgsBoxContent">
                        <a :href="`${featuredPackage.packageSlug}`"><h4>{{ featuredPackage.title }}</h4></a>
                        <hr>
                        <a :href="`${featuredPackage.facilitySlug}`" v-if="featuredPackage.branches.length > 1"><h6>{{ featuredPackage.branches[0].name }}...</h6></a>
                        <a :href="`${featuredPackage.facilitySlug}`" v-else><h6>{{ featuredPackage.branches[0].name }}</h6></a>
                        <div class="country-price-row clearfix">
                            <!-- <h5>{{ medicalPackage.branches[0].facility.location.country }}</h5> -->
                            <div class="hlthPkgsCntryLrnMorDiv">
                                <h5>{{ featuredPackage.branches[0].facility.location.country }}</h5>
                                <a :href="`${featuredPackage.packageSlug}`">{{ trans('frontend.package.learn_more') }}</a>
                            </div>
                            <div class="travelPkgSecRowPrice">
                                <h3>{{ trans('frontend.package.us_dollar') }}{{ featuredPackage.price.discounted_price }}</h3>
                                <span style="text-decoration:line-through; color:red;" v-if="featuredPackage.price.discounted_price !== featuredPackage.price.user_price">
                                    <span style='color:black;'>{{ trans('frontend.package.usual_price_usd') }}{{ featuredPackage.price.user_price }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pkgLandgViewAllLink">
            <a :href="`${viewAllRegularPackages}`">{{ trans('frontend.package.view_all') }}</a>
        </div>

    </div>


    <!--<div>-->
        <!--<div class="row trvlPkgMargin">-->
            <!--<div class="col-md-4 col-sm-4 col-xs-12 trvlPkgSecRowRightSmallBoxPadding" v-for="featuredPackage in featuredPackages">-->
                <!--<div class="trvlPkgSecRowRightSmallBox">-->
                    <!--<a :href="`${featuredPackage.packageSlug}`"><img :src="`${featuredPackage.image}`" :alt="`${featuredPackage.title}`"></a>-->
                <!--</div>-->
                <!--<div class="trvlPkgSecRowContent">-->
                    <!--<a :href="`${featuredPackage.packageSlug}`"><h2>{{ featuredPackage.title }}</h2></a>-->
                    <!--<hr>-->
                    <!--<a :href="`${featuredPackage.facilitySlug}`" v-if="featuredPackage.branches.length > 1"><h6>{{ featuredPackage.branches[0].name }}...</h6></a>-->
                    <!--<a :href="`${featuredPackage.facilitySlug}`" v-else><h6>{{ featuredPackage.branches[0].name }}</h6></a>-->
                    <!--<div class="trvlPkgSecRow-countryPrice clearfix">-->
                        <!--&lt;!&ndash;<h5 class="travelSecCntryName">{{ featuredPackage.branches[0].facility.location.country }}</h5>&ndash;&gt;-->
                        <!--<div class="travelSecCntryName">-->
                            <!--<h5 class="trvlPkgFltLeft">{{ featuredPackage.branches[0].facility.location.country }}</h5>-->
                            <!--<a :href="`${featuredPackage.packageSlug}`">{{ trans('frontend.package.learn_more') }}</a>-->
                        <!--</div>-->

                        <!--<div class="travelPkgSecRowPrice">-->
                            <!--<h3>{{ trans('frontend.package.us_dollar') }}{{ featuredPackage.price.discounted_price }}</h3>-->
                            <!--<span style="text-decoration:line-through; color:red;" v-if="featuredPackage.price.discounted_price !== featuredPackage.price.user_price">-->
                                <!--<span style='color:black;'>{{ trans('frontend.package.usual_price_usd') }}{{ featuredPackage.price.user_price }}</span>-->
                            <!--</span>-->
                        <!--</div>-->
                    <!--</div>-->
                <!--</div>-->
            <!--</div>-->
        <!--</div>-->

        <!--<div class="pkgLandgViewAllLink">-->
            <!--<a :href="`${viewAllRegularPackages}`">{{ trans('frontend.package.view_all') }}</a>-->
        <!--</div>-->
    <!--</div>-->
</template>


<script>

    export default {

        data() {

            return {

                featuredPackages: [],
                viewAllRegularPackages: Globalhealth.packageListTemplateLinkUrl + '/' + 'regular'
            }
        },

        created() {

            return axios.get(Globalhealth.packageFeaturedLinkUrl)
                .then(response => {

                    this.featuredPackages = response.data;

                    this.featuredPackages.forEach(function (featuredPackage) {

                        featuredPackage.packageSlug = Globalhealth.packageDetailTemplateLinkUrl + '/' + featuredPackage.slug;
                        featuredPackage.facilitySlug = Globalhealth.facilityDetailTemplateLinkUrl + '/' + featuredPackage.branches[0].facility.slug;
                    })
                });
        }
    }
</script>