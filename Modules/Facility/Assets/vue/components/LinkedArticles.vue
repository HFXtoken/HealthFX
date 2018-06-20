<template>
    <div v-if="articles && articles.length > 0">
        <div class="row provRelPkgs hospDtlProvRelPkgs">
            <h4>{{ trans('frontend.facility.related_articles') }}</h4>

            <div class="col-md-6 col-sm-12 col-xs-12 relPkgsLists" v-for="article in articles">
                <div class="row relPkgsSection">
                    <div class="col-md-4 col-sm-4 col-xs-12 provRelPkgsImg provRelArticles">
                        <img :src="`${article.image}`" :alt="`${article.title}`">
                    </div>
                    <div class="col-md-7 col-sm-7 col-xs-12 provRelPkgsLink">
                        <a :href="`${article.articleSlug}`">{{ article.title }}</a>
                    </div>
                </div>
            </div>

        </div>
        <!--<div class="viewAllDiv">-->
            <!--<a href="" class="noBorder">{{ trans('frontend.facility.view_all') }}</a>-->
        <!--</div>-->
    </div>
</template>

<script>
    export default {

        props: ['articles'],

        created() {

            this.articles.forEach(function (article) {
                article.articleSlug = Globalhealth.articleDetailTemplateLinkUrl + '/' + article.slug;
            });
        }
    }
</script>