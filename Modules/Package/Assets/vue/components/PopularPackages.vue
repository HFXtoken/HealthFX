<template>

    <div class="artDetailSidebarPopArtSec" style="margin-top: 20px;" v-if="popularPackages && popularPackages.length > 0">

        <h4>{{ trans('frontend.package.popular') }}</h4>

        <div class="ArtPopPkgs" v-for="popularPackage in popularPackages">
            <h6 v-if="popularPackage.branches">{{ popularPackage.branches[0].name }}, {{ popularPackage.branches[0].facility.location.country }}</h6>
            <a :href="`${popularPackage.packageSlug}`">{{ popularPackage.title }}</a>
        </div>

        <div class="ArtPopPkgs">
            <a :href="`${viewAllPackages}`">{{ trans('frontend.package.view_all') }}</a>
        </div>

    </div>

</template>


<script>

    export default {

        data() {

            return {

                popularPackages: [],

                viewAllPackages: Globalhealth.packageListTemplateLinkUrl + '/' + 'regular'
            }
        },

        created() {

            return axios.get(Globalhealth.packagePopularLinkUrl)
                .then(response => {

                    this.popularPackages = response.data;

                    this.popularPackages.forEach(function (popularPackage) {

                        popularPackage.packageSlug = Globalhealth.packageDetailTemplateLinkUrl + '/' + popularPackage.slug;
                    })
                });
        }
    }
</script>