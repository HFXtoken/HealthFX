<template>
    <div>
        <div class="drDtlLeftPanel" v-for="facilityDetail in facilityDetails">
            <div class="row">
                <div class="col-md-3 col-sm-3 col-xs-12">
                    <img :src="`${facilityDetail.image}`" :alt="`${facilityDetail.name}`" class="drDetailImg">
                </div>
                <div class="col-md-9 col-sm-9 col-xs-12 drDetailContentMainDiv">
                    <h3>{{ facilityDetail.name }}</h3>
                    <hr>
                    <div class="hopsDtlContText">
                        <div class="pkgDetConSlider hospDetlSlider" v-if="facilityDetail.gallery && facilityDetail.gallery.length > 0">
                            <carousel :navigationEnabled="true" :paginationEnabled="false" :perPage="1">
                                <slide v-for="(gallery, index) in facilityDetail.gallery" :key="index">
                                    <img :src="`${gallery.path_string}`" :alt="`${facilityDetail.group_name}`" class="img-responsive">
                                </slide>
                            </carousel>
                        </div>
                        <div class="hospDetlContent">
                            <p v-html="facilityDetail.description"></p>
                        </div>
                    </div>
                    <!--<div class="row hospDetlOtherInfo">-->
                        <!--<div class="drDetailContent col-md-6 col-sm-6 col-xs-12 ">-->
                            <!--<div class="clearfix drDtlContIconMngDiv"><img src="/themes/globalhealth/images/icon-language.png" alt=""><p><span>Language Spoken</span><br>-->
                                <!--English <br>-->
                                <!--Mandarian</p></div>-->
                        <!--</div>-->
                        <!--<div class="drDetailContent col-md-6 col-sm-6 col-xs-12">-->
                            <!--<div class="clearfix drDtlContIconMngDiv"><img src="/themes/globalhealth/images/icon-clinicalFocus.png" alt=""><p><span>Clinical Focus</span><br> Aesthetics (Cosmetics)</p></div>-->
                        <!--</div>-->
                    <!--</div>-->
                </div>
            </div>

            <linked-packages :packages="facilityDetail.linked_packages"></linked-packages>

            <linked-doctors :doctors="facilityDetail.linked_doctors"></linked-doctors>

            <linked-articles :articles="facilityDetail.linked_articles"></linked-articles>
        </div>
    </div>
</template>


<script>

    import LinkedPackages from './LinkedPackages';
    import LinkedDoctors from './LinkedDoctors';
    import LinkedArticles from './LinkedArticles';
    import { EventBus } from '../bundle.js';
    import { Carousel, Slide } from 'vue-carousel';

    export default {

        components: { LinkedPackages, LinkedDoctors, LinkedArticles, Carousel, Slide },

        data() {

            return {

                facilityDetails: []
            }
        },

        created() {

            let loader = this.$loading.show();

            return axios.get(Globalhealth.facilityDetailLinkUrl + '/{slug}', {

                params: {

                    slug: window.location.pathname.split("/").pop()
                }
            })
                .then(response => {

                    this.facilityDetails = response.data;
                    EventBus.$emit('DETAIL_TITLE', this.facilityDetails[0].name);
                    EventBus.$emit('linked-branches', this.facilityDetails[0].linked_branches);
                    EventBus.$emit('PROVIDER_DATA', this.facilityDetails[0]);
                    EventBus.$emit('facility-departments', {departments: this.facilityDetails[0].departments, facilityName: this.facilityDetails[0].name, facilitySlug: this.facilityDetails[0].slug});

                    loader.hide();
                });

        }
    }
</script>

<style scoped>
    /* Using custom icons may require some additional CSS declarations */
    .carousel-control .my-icon {
        position: absolute;
        top: 50%;
        margin-top: -10px;
    }
</style>