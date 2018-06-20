<template>
    <div v-if="departments && departments.length > 0">
        <a :href="`${facilitySlugUrl}`" class="hospOwnLinkBtn">{{ facilityName }}</a>
        <div class="drDetailSidebarLocSec">
            <h4>{{ trans('frontend.facility.departments') }}</h4>
            <ul class="subSpltyLinks" v-for="department in departments">
                <li>
                    <a :href="`${department.departmentSlug}`">{{ department.name }}</a>
                </li>
            </ul>
        </div>
    </div>
</template>


<script>
    import { EventBus } from '../bundle.js';

    export default {

        data() {
            return {
                departments: [],
                facilityName: '',
                facilitySlug: '',
                facilitySlugUrl: ''
            }
        },

        mounted() {
            EventBus.$on('facility-departments', facilityDepartments => {
                this.departments = facilityDepartments.departments;
                this.facilityName = facilityDepartments.facilityName;
                this.facilitySlug = facilityDepartments.facilitySlug;
                this.facilitySlugUrl = Globalhealth.facilityDetailTemplateLinkUrl + '/' + this.facilitySlug;
                this.departments.forEach(function (facilityDepartment) {
                    facilityDepartment.departmentSlug = Globalhealth.facilityDepartmentTemplateLinkUrl.replace('param1', this.facilitySlug).replace('param2', facilityDepartment.slug);
                }, this);
            });
        },
    }
</script>