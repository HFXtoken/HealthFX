<template>
    <div class="row pkglistleftSearchSec" v-if="filterShortcuts && filterShortcuts.length > 0">
        <div class="col-md-2 col-sm-4 col-xs-12" v-for="filterShortcut in filterShortcuts">
            <a class="splCarouselBoxs" :class="{active: selectedFilter === filterShortcut.slug}" :href="`${filterShortcutLink + '/regular?category=' + filterShortcut.slug}`">
                <img :src="`${filterShortcut.image}`" :alt="`${filterShortcut.code}`">
                <h5>{{ filterShortcut.code }}</h5>
            </a>
        </div>
    </div>
</template>

<script>

    import { EventBus } from '../bundle.js';

    export default {

        name: "filter-shortcuts",

        data() {

            return {

                filterShortcuts: [],
                filterShortcutLink: Globalhealth.packageListTemplateLinkUrl,
                selectedFilter: ''
            }
        },

        created() {

            return axios.get(Globalhealth.categoryFilterLinkUrl)
                .then(response => this.filterShortcuts = response.data);
        },

        methods: {

            getSearchParams(queryStr) {
                var queryArr = queryStr.replace('?','').split('&'),
                    queryParams = [];

                for (var q = 0, qArrLength = queryArr.length; q < qArrLength; q++) {
                    var qArr = queryArr[q].split('=');
                    queryParams[qArr[0]] = qArr[1];
                }

                return queryParams;
            },
        },

        mounted() {
            EventBus.$on('SEARCH_FILTERS', searchFilters => {
                var params = this.getSearchParams(searchFilters.searchParams);
                this.selectedFilter = ('category' in params) ? params['category'] : '';
            });
        }
    }
</script>