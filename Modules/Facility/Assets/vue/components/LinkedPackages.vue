<template>
    <div v-if="packages && packages.length > 0">
        <div class="row provRelPkgs">
            <h4>{{ trans('frontend.facility.related_packages') }} </h4>
            <div class="col-md-5 col-sm-12 col-xs-12 relPkgsLists" v-for="package in packages">
                <div class="row relPkgsSection">
                    <div class="col-md-4 col-sm-4 col-xs-4 provRelPkgsImg">
                        <img :src="`${package.image}`" :alt="`${package.title}`">
                    </div>
                    <div class="col-md-7 col-sm-7 col-xs-8 provRelPkgsLink">
                        <a :href="`${package.packageSlug}`">{{ package.title }}</a>
                        <h6>{{ trans('frontend.package.us_dollar') }}{{ package.price.discounted_price }}</h6>
                        <span style="text-decoration:line-through; color:red;" v-if="package.price.discounted_price !== package.price.user_price">
                            <span style='color:black;'>{{ trans('frontend.package.usual_price_usd') }}{{ package.price.user_price }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="viewAllDiv" v-if="detailPageLink">
            <a :href="`${detailPageLink}`">{{ trans('frontend.facility.view_all') }}</a>
        </div>
    </div>
</template>


<script>

    export default {

        props: ['packages', 'detailViewAllLink'],

        data() {

            return {

                detailPageLink: this.detailViewAllLink
            }
        },

        created() {

            this.packages.forEach(function (relatedPackage) {
                relatedPackage.packageSlug = Globalhealth.packageDetailTemplateLinkUrl + '/' + relatedPackage.slug;
            });
        }

    }
</script>