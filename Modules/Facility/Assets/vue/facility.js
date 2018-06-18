import FacilityList from './components/FacilityList';
import FacilityDetail from './components/FacilityDetail';
import FacilityDepartments from './components/FacilityDepartments';
import LinkedBranches from './components/LinkedBranches';
import FacilitySearch from './components/FacilitySearch';
import BookEnquire from './components/BookEnquire';
import DepartmentDetail from './components/DepartmentDetail';

if($("#facilityListContent").length > 0) {
    new Vue({

        el: '#facilityListContent',

        store,

        components: { FacilityList }
    })
}

if($("#facilityListSidebar"). length > 0) {
    new Vue({

        el: '#facilityListSidebar',

        components: { Subscribe, RelatedArticles, FacilitySearch }
    })
}

if($("#facilityDetailContent").length > 0) {
    new Vue({

        el: '#facilityDetailContent',

        components: { FacilityDetail }
    })
}

if($("#facilityDetailSidebar").length > 0) {
    new Vue({

        el: '#facilityDetailSidebar',

        store,

        components: { BookEnquire, FacilityDepartments, LinkedBranches }
    })
}

if($("#departmentDetailContent").length > 0) {
    new Vue({

        el: '#departmentDetailContent',

        components: { DepartmentDetail }
    })
}