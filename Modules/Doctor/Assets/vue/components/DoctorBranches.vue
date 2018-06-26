<template>
    <div class="drDetailSidebarLocSec">
        <h4>{{ trans('frontend.doctor.location') }}</h4>
        <!--<h5>{{ doctorBranches.name }}</h5>-->
        <div class="panel-group">
            <div class="panel panel-default">
                <div class="drClncAddress" v-for="(branch, index) in doctorBranches.branches">
                    <div class="panel-heading" role="button" @click="toggleDoctorBranchesAccordion(index)">
                        <div class="option-heading">
                            <h3>{{ branch.name }}</h3>
                            <div class="arrow-up" v-if="showDoctorBranchesAccordion[index]">&#8743;</div>
                            <div class="arrow-down" v-else>&#8744;</div>
                        </div>
                        <p>{{ branch.location.address_1 }}, {{ branch.location.address_2 }}, {{ branch.location.postcode }}</p>
                    </div>

                    <collapse v-model="showDoctorBranchesAccordion[index]">
                        <div class="panel-body">
                            <div class="option-content locatAccCont">
                                <p><strong>{{ trans('frontend.facility.contact') }}</strong> {{ branch.contact.phone_inquiry }}</p>
                                <div v-if="branch.branch_timings && branch.branch_timings.length > 0">
                                    <p><strong>{{ trans('frontend.facility.opening_hours') }}</strong></p>
                                    <p v-for="timing in branch.branch_timings">
                                        {{ timing.start_day | truncate('3') }} {{ timing.end_day| truncate('6') }} - {{ timing.start_time| truncate('5') }} - {{ timing.end_time| truncate('5') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </collapse>

                    <a href="javascript:;" @click="openModal(branch.id)" class="getDirectionLink">{{ trans('frontend.doctor.map') }}</a>
                    <section>
                        <modal v-model="modalList[branch.id]" size="lg" :header="false" :footer="false" @show="showMap(branch.slug)">
                            <google-map :name="branch.slug" :lat="branch.location.lat" :lng="branch.location.long" ></google-map>
                        </modal>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import { EventBus } from '../bundle.js';
    import GoogleMap from './GoogleMap';

    export default {

        components: { GoogleMap },

        name: "doctor-branches",

        data() {

            return {

                doctorBranches: [],

                showDoctorBranchesAccordion: [],
                modalList: [],
            }
        },

        created() {

            EventBus.$on('doctor-branches', doctorBranches => {

                this.doctorBranches = doctorBranches;

                this.showDoctorBranchesAccordion = doctorBranches.showDoctorBranchesAccordion;

                this.doctorBranches.branches.forEach(function (branch) {
                    this.modalList[branch.id] = false;
                }, this);
            });
        },

        methods: {

            toggleDoctorBranchesAccordion(index) {

                if (this.showDoctorBranchesAccordion[index]) {
                    this.$set(this.showDoctorBranchesAccordion, index, false)
                } else {
                    this.showDoctorBranchesAccordion = this.showDoctorBranchesAccordion.map((v, i) => i === index)
                }
            },

            openModal(id) {
                this.$set(this.modalList, id, true);
            },

            showMap(mapName) {
                EventBus.$emit('SHOW_MAP_MODAL', {name: mapName});
            }
        }
    }
</script>