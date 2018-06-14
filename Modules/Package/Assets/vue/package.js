import FeaturedHorizontal from './components/FeaturedHorizontal.vue';
import FeaturedVertical from './components/FeaturedVertical.vue';
import FeaturedMedHorizontal from './components/FeaturedMedHorizontal.vue';
import FeaturedPackages from './components/FeaturedPackages.vue';
import MedicalPackages from './components/MedicalPackages.vue';
import HealthPackages from './components/HealthPackages.vue';
import RegularPackages from './components/RegularPackages.vue';
import PopularPackages from './components/PopularPackages.vue';
import PackageList from './components/PackageList.vue';
import PackageDetail from './components/PackageDetail.vue';
import PackageSearch from './components/PackageSearch.vue';
import FilterShortcuts from './components/FilterShortcuts';
import CartCount from './components/CartCount';

if($("#packageIndexPage").length > 0) {
    new Vue({

        el: '#packageIndexPage',

        components: { FeaturedHorizontal, FeaturedVertical, FeaturedMedHorizontal, FeaturedPackages, MedicalPackages, HealthPackages, RegularPackages }
    })
}

if($("#packageListSidebar").length > 0) {
    new Vue({

        el: '#packageListSidebar',

        store,

        components: { PopularPackages, PackageSearch, CartCount }
    })
}

if($("#packageListContent").length > 0) {
    new Vue({

        el: '#packageListContent',

        store,

        components: { FilterShortcuts, PackageList }
    })
}

if($("#packageDetailContent").length > 0) {
    new Vue({

        el: '#packageDetailContent',

        store,

        components: { PackageDetail }
    })
}

if($("#packageDetailSidebar").length > 0) {
    new Vue({

        el: '#packageDetailSidebar',

        store,

        components: { CartCount }
    })
}

if($("#packageMcHsSidebar").length > 0) {
    new Vue({

        el: '#packageMcHsSidebar',

        components: { PopularArticles }
    })
}

if($("#packageMcFertilitySidebar").length > 0) {
    new Vue({

        el: '#packageMcFertilitySidebar',

        components: { PopularArticles }
    })
}