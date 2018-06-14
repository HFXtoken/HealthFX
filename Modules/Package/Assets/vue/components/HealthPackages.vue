<template>
    <div class="ftrdPkgSection clearfix" v-if="healthPackages && healthPackages.length > 0">

        <h2>{{ trans('frontend.package.health_screening') }}</h2>

        <div class="row">
            <div class="pkgLandingFtrdPkgSection" v-for="healthPackage in healthPackages">
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="pkgLandgBox">
                        <a :href="`${healthPackage.packageSlug}`"><img :src="`${healthPackage.image}`" :alt="`${healthPackage.title}`"></a>
                    </div>
                    <div class="pkgLandgPkgsBoxContent">
                        <a :href="`${healthPackage.packageSlug}`"><h4>{{ healthPackage.title }}</h4></a>
                        <hr>
                        <a :href="`${healthPackage.facilitySlug}`" v-if="healthPackage.branches.length > 1"><h6>{{ healthPackage.branches[0].name }}...</h6></a>
                        <a :href="`${healthPackage.facilitySlug}`" v-else><h6>{{ healthPackage.branches[0].name }}</h6></a>
                        <div class="country-price-row clearfix">
                            <!-- <h5>{{ healthPackage.branches[0].facility.location.country }}</h5> -->
                            <div class="hlthPkgsCntryLrnMorDiv">
                                <h5>{{ healthPackage.branches[0].facility.location.country }}</h5>
                                <a :href="`${healthPackage.packageSlug}`">{{ trans('frontend.package.learn_more') }}</a>
                            </div>
                            <div class="travelPkgSecRowPrice">
                                <h3>{{ trans('frontend.package.us_dollar') }}{{ healthPackage.price.discounted_price }}</h3>
                                <span style="text-decoration:line-through; color:red;" v-if="healthPackage.price.discounted_price !== healthPackage.price.user_price">
                                    <span style='color:black;'>{{ trans('frontend.package.usual_price_usd') }}{{ healthPackage.price.user_price }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pkgLandgViewAllLink">
            <a :href="`${viewAllHealthPackages}`">{{ trans('frontend.package.view_all') }}</a>
        </div>

    </div>
</template>


<script>

    export default {

        data() {

            return {

                healthPackages: [],

                viewAllHealthPackages: Globalhealth.packageListTemplateLinkUrl + '/' + 'health-screening'
            }
        },

        created() {

            return axios.get(Globalhealth.packageHealthLinkUrl)
                .then(response => {

                    this.healthPackages = response.data;

                    this.healthPackages.forEach(function (healthPackage) {

                        healthPackage.packageSlug = Globalhealth.packageDetailTemplateLinkUrl + '/' + healthPackage.slug;
                        healthPackage.facilitySlug = Globalhealth.facilityDetailTemplateLinkUrl + '/' + healthPackage.branches[0].facility.slug;
                    })
                });
        }
    }
</script>