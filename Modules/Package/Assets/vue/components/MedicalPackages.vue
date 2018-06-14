<template>
    <div class="ftrdPkgSection clearfix" v-if="medicalPackages && medicalPackages.length > 0">

        <h2>{{ trans('frontend.package.medical') }}</h2>

        <div class="row">
            <div class="pkgLandingFtrdPkgSection" v-for="medicalPackage in medicalPackages">
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="pkgLandgBox">
                        <a :href="`${medicalPackage.packageSlug}`"><img :src="`${medicalPackage.image}`" :alt="`${medicalPackage.title}`"></a>
                    </div>
                    <div class="pkgLandgPkgsBoxContent">
                        <a :href="`${medicalPackage.packageSlug}`"><h4>{{ medicalPackage.title }}</h4></a>
                        <hr>
                        <a :href="`${medicalPackage.facilitySlug}`" v-if="medicalPackage.branches.length > 1"><h6>{{ medicalPackage.branches[0].name }}...</h6></a>
                        <a :href="`${medicalPackage.facilitySlug}`" v-else><h6>{{ medicalPackage.branches[0].name }}</h6></a>
                        <div class="country-price-row clearfix">
                            <!-- <h5>{{ medicalPackage.branches[0].facility.location.country }}</h5> -->
                            <div class="hlthPkgsCntryLrnMorDiv">
                                <h5>{{ medicalPackage.branches[0].facility.location.country }}</h5>
                                <a :href="`${medicalPackage.packageSlug}`">{{ trans('frontend.package.learn_more') }}</a>
                            </div>
                            <div class="travelPkgSecRowPrice">
                                <h3>{{ trans('frontend.package.us_dollar') }}{{ medicalPackage.price.discounted_price }}</h3>
                                <span style="text-decoration:line-through; color:red;" v-if="medicalPackage.price.discounted_price !== medicalPackage.price.user_price">
                                    <span style='color:black;'>{{ trans('frontend.package.usual_price_usd') }}{{ medicalPackage.price.user_price }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pkgLandgViewAllLink">
            <a :href="`${viewAllMedicalPackages}`">{{ trans('frontend.package.view_all') }}</a>
        </div>

    </div>
</template>


<script>

    export default {

        data() {

            return {

                medicalPackages: [],

                viewAllMedicalPackages: Globalhealth.packageListTemplateLinkUrl + '/' + 'medical'
            }
        },

        created() {

            return axios.get(Globalhealth.packageMedicalLinkUrl)
                .then(response => {

                    this.medicalPackages = response.data;

                    this.medicalPackages.forEach(function(medicalPackage) {

                        medicalPackage.packageSlug = Globalhealth.packageDetailTemplateLinkUrl + '/' + medicalPackage.slug;
                        medicalPackage.facilitySlug = Globalhealth.facilityDetailTemplateLinkUrl + '/' + medicalPackage.branches[0].facility.slug;
                    })
                });
        }
    }
</script>