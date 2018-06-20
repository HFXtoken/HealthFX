<template>
    <div v-if="doctors && doctors.length > 0">
        <div class="row provRelDoctor">
            <h4>{{ trans('frontend.facility.label_related_doctors') }} </h4>
            <div class="col-md-4 col-sm-4 col-xs-12 relPkgsLists" v-for="doctor in doctors">
                <div class="row relPkgsSection">
                    <div class="col-md-4 col-sm-4 col-xs-3 provRelDocImg">
                        <img :src="`${doctor.image}`" :alt="`${doctor.first_name}`">
                    </div>
                    <div class="col-md-7 col-sm-7 col-xs-9 provRelDocLink">
                        <a :href="`${doctor.doctorSlug}`">{{ doctor.first_name }} {{ doctor.last_name }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="viewAllDivTwo clearfix" v-if="detailPageLink">
            <a :href="`${detailPageLink}`">{{ trans('frontend.facility.view_all') }}</a>
        </div>
    </div>
</template>


<script>

    export default {

        props: ['doctors', 'detailViewAllLink'],

        data() {

            return {

                detailPageLink: this.detailViewAllLink
            }
        },

        created() {

            this.doctors.forEach(function (doctor) {
                doctor.doctorSlug = Globalhealth.doctorDetailTemplateLinkUrl + '/' + doctor.slug;
            });
        }

    }

</script>