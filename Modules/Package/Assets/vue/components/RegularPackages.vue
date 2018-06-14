<template>
    <div class="morePkgSection clearfix" v-if="regularPackages && regularPackages.length > 0">

        <h2>{{ trans('frontend.package.more') }}</h2>

        <div class="row">
            <div class="pkgLandingFtrdPkgSection" v-for="regularPackage in regularPackages">
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="pkgLandgBox">
                        <a :href="`${regularPackage.packageSlug}`"><img :src="`${regularPackage.image}`" :alt="`${regularPackage.title}`"></a>
                    </div>
                    <div class="pkgLandgPkgsBoxContent">
                        <a :href="`${regularPackage.packageSlug}`"><h4>{{ regularPackage.title }}</h4></a>
                        <hr>
                        <a :href="`${regularPackage.facilitySlug}`" v-if="regularPackage.branches.length > 1"><h6>{{ regularPackage.branches[0].name }}...</h6></a>
                        <a :href="`${regularPackage.facilitySlug}`" v-else><h6>{{ regularPackage.branches[0].name }}</h6></a>
                        <div class="country-price-row clearfix">
                            <!-- <h5>{{ regularPackage.branches[0].facility.location.country }}</h5> -->
                            <div class="hlthPkgsCntryLrnMorDiv">
                                <h5>{{ regularPackage.branches[0].facility.location.country }}</h5>
                                <a :href="`${regularPackage.packageSlug}`">{{ trans('frontend.package.learn_more') }}</a>
                            </div>
                            <div class="travelPkgSecRowPrice">
                                <h3>{{ trans('frontend.package.us_dollar') }}{{ regularPackage.price.discounted_price }}</h3>
                                <span style="text-decoration:line-through; color:red;" v-if="regularPackage.price.discounted_price !== regularPackage.price.user_price">
                                    <span style='color:black;'>{{ trans('frontend.package.usual_price_usd') }}{{ regularPackage.price.user_price }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pkgLandgViewAllLink viewAllPkgsMargins">
            <a :href="`${viewAllRegularPackages}`">{{ trans('frontend.package.view_all') }}</a>
        </div>

    </div>
</template>


<script>

    export default {

        data() {

            return {
                regularPackages: [],
                viewAllRegularPackages: Globalhealth.packageListTemplateLinkUrl + '/' + 'regular'
            }
        },

        created() {

            return axios.get(Globalhealth.packageRegularLinkUrl)
                .then(response => {
                    this.regularPackages = response.data;
                    this.regularPackages.forEach(function (regularPackage) {
                        regularPackage.packageSlug = Globalhealth.packageDetailTemplateLinkUrl + '/' + regularPackage.slug;
                        regularPackage.facilitySlug = Globalhealth.facilityDetailTemplateLinkUrl + '/' + regularPackage.branches[0].facility.slug;
                    })
                });
        }
    }
</script>