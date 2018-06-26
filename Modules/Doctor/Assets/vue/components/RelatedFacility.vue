<template>
    <div v-if="facilities && facilities.length > 0">
        <div class="row provRelDoctor assocHospDiv">
            <h4>{{ trans('frontend.facility.related_facility') }}</h4>
            <div class="col-md-4 col-sm-4 col-xs-12" v-for="facility in facilities">
                <div class="row assHospSection">
                    <div class="col-md-5 col-sm-5 col-xs-12 provRelDocImg">
                        <img :src="`${facility.image}`" :alt="`${facility.name}`">
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12 provRelDocLink">
                        <a :href="`${facility.facilitySlug}`">{{ facility.name }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="viewAllDivTwo clearfix">
            <a :href="`${detailViewAllLink}`">{{ trans('frontend.facility.view_all') }}</a>
        </div>
    </div>
</template>


<script>

    export default {

        props: ['facilities', 'detailViewAllLink'],

        data() {

            return {

                detailPageLink: this.detailViewAllLink
            }
        },

        created() {

            this.facilities.forEach(function (facility) {
                facility.facilitySlug = Globalhealth.facilityDetailTemplateLinkUrl + '/' + facility.slug;
            });
        }
    }
</script>