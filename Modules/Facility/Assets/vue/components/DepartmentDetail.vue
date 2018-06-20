<template>
    <div>
        <div class="drDtlLeftPanel">
            <div class="row">
                <div class="col-md-3 col-sm-3 col-xs-12">
                    <img :src="`${departmentDetail.image}`" :alt="`${departmentDetail.name}`" class="drDetailImg">
                </div>
                <div class="col-md-9 col-sm-9 col-xs-12 drDetailContentMainDiv">
                    <h3>{{ departmentDetail.name }}</h3>
                    <hr>
                    <div class="hopsDtlContText">
                        <div class="hospDetlContent">
                            <p v-html="departmentDetail.description"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="departmentDetail.doctors">
                <linked-doctors :doctors="departmentDetail.doctors"></linked-doctors>
            </div>

        </div>
    </div>
</template>

<script>

    import { EventBus } from '../bundle.js';
    import LinkedDoctors from './LinkedDoctors';

    export default {
        components: { LinkedDoctors },

        data() {
            return {
                departmentDetail: '',
                breadcrumbs: [],
            }
        },

        created() {

            let loader = this.$loading.show();

            return axios.get(Globalhealth.facilityDepartmentLinkUrl + '/' + window.location.pathname.split("/").pop(), {
                params: {
                    slug: window.location.pathname.split("/").pop()
                }
            }).then(response => {
                this.departmentDetail = response.data;
                this.breadcrumbs.push({titleName: this.departmentDetail.facility.name, href: Globalhealth.facilityDetailTemplateLinkUrl + '/' + this.departmentDetail.facility.slug});
                this.breadcrumbs.push({titleName: this.departmentDetail.name, href: '#'});
                EventBus.$emit('BREADCRUMB_LIST', this.breadcrumbs);
                EventBus.$emit('linked-branches', this.departmentDetail.facility.linked_branches);
                EventBus.$emit('facility-departments', {departments: this.departmentDetail.facility.departments, facilityName: this.departmentDetail.facility.name, facilitySlug: this.departmentDetail.facility.slug});

                loader.hide();
            });

        }
    }

</script>