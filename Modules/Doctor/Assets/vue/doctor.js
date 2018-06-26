import DoctorList from './components/DoctorList';
import DoctorDetail from './components/DoctorDetail';
import DoctorBranches from './components/DoctorBranches';
import DoctorSearch from './components/DoctorSearch';

if($("#doctorListContent").length > 0) {
    new Vue({

        el: '#doctorListContent',

        store,

        components: { DoctorList }
    });
}

if($("#doctorListSidebar").length > 0) {
    new Vue({

        el: '#doctorListSidebar',

        components: { Subscribe, RelatedArticles, DoctorSearch }
    });
}

if($("#doctorDetailContent").length > 0) {
    new Vue({

        el: '#doctorDetailContent',

        components: { DoctorDetail }
    });
}

if($("#doctorDetailSidebar").length > 0) {
    new Vue({

        el: '#doctorDetailSidebar',

        store,

        components: { BookEnquire, DoctorBranches }
    });
}