<template>
    <div class="drDtlLeftPanel row">
        <div class="col-md-3 col-sm-3 col-xs-12">
            <img :src="`${doctorDetail.image}`" :alt="`${doctorDetail.first_name}`" class="drDetailImg">
            <button type="button" class="thanksBtn" @click="saveAction('like')">{{ trans('frontend.doctor.say_thanks') }}</button>
            <div class="likeDiv"><div class="likeHandIcon"><img src="/themes/globalhealth/images/likeHandIcon.png" alt=""></div><span>{{doctorLikes}} {{ trans('frontend.doctor.thanked') }}</span></div>
        </div>
        <div class="col-md-9 col-sm-9 col-xs-12 drDetailContentMainDiv">
            <h3>{{ doctorDetail.first_name }} {{ doctorDetail.last_name }}</h3>
            <h5 v-if="doctorDetail.primary_speciality">{{ doctorDetail.primary_speciality.name }}, {{ doctorDetail.linked_facility.location.country }}</h5>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 drDetailContent">
                    <p class="profStatementP">
                        <span>{{ trans('frontend.doctor.professional_statement') }}</span><br>
                        <span v-html="doctorDetail.professional_statement"></span>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="drDetailContent col-md-6 col-sm-6 col-xs-12 " v-if="doctorDetail.known_languages && doctorDetail.known_languages.length > 0">
                    <!--<div class="clearfix drDtlContIconMngDiv"><img src="/themes/globalhealth/images/icon-year.png" alt=""><p><span>Years of Experience</span><br> 15 Years</p></div>-->
                    <div class="clearfix drDtlContIconMngDiv"><img src="/themes/globalhealth/images/icon-language.png" alt="">
                        <p><span>{{ trans('frontend.doctor.language_spoken') }}</span><br/>
                            <span v-for="knownLanguage in doctorDetail.known_languages">{{ knownLanguage.name }}<br/></span>
                        </p>
                    </div>
                    <!--<div class="clearfix drDtlContIconMngDiv"><img src="/themes/globalhealth/images/icon-hospital.png" alt=""><p><span>Hospital Affiliation</span><br>The Chelsea Clinic</p></div>-->
                </div>
                <div class="drDetailContent col-md-6 col-sm-6 col-xs-12" v-if="doctorDetail.education">
                    <div class="clearfix drDtlContIconMngDiv"><img src="/themes/globalhealth/images/icon-qualification.png" alt="">
                        <p class="drDtlQualTxt"><span>{{ trans('frontend.doctor.qualifications') }}</span><br/>
                            {{ doctorDetail.education }}
                        </p>
                    </div>
                    <!--<div class="clearfix drDtlContIconMngDiv"><img src="/themes/globalhealth/images/icon-clinicalFocus.png" alt=""><p><span>Clinical Focus</span><br> Aesthetics (Cosmetics)</p></div>-->
                </div>
            </div>
        </div>
    </div>
</template>


<script>

    import { EventBus } from '../bundle.js';

    export default {

        data() {
            return {

                doctorDetail: [],
                doctorLikes: 0,
            }
        },

        created() {
            return axios.get(Globalhealth.doctorDetailLinkUrl + '/{slug}', {
                params: {
                    slug: window.location.pathname.split("/").pop()
                }
            }).then(response => {
                this.doctorDetail = response.data;
                EventBus.$emit('DETAIL_TITLE', this.doctorDetail.first_name + ' ' + this.doctorDetail.last_name);
                EventBus.$emit('doctor-branches', this.doctorDetail);
                EventBus.$emit('PROVIDER_DATA', this.doctorDetail);
                this.saveAction('view');
                this.doctorLikes = response.data.analytics != null ? response.data.analytics.likes : 0;
            });
        },

        methods: {
 
            saveAction(type) {
                var url = Globalhealth.doctorActionLinkUrl.replace('param1', type).replace('param2', this.doctorDetail.id);
                return axios.get(url)
                    .then(response => {
                        if(type === 'like') {
                            this.doctorLikes++
                        }
                    });
            },
        }
    }
</script>