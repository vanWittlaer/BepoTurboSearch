import template from './bepo-turbo-suggest-target-list.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('bepo-turbo-suggest-target-list', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('listing')
    ],

    data() {
        return {
            repository: null,
            searchTargets: null,
            isLoading: false,
            sortBy: 'priority',
            sortDirection: 'DESC'
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        columns() {
            return [
                {
                    property: 'title',
                    dataIndex: 'title',
                    label: this.$tc('bepo-turbo-suggest.list.columnTitle'),
                    allowResize: true,
                    primary: true
                },
                {
                    property: 'category.name',
                    dataIndex: 'category.name',
                    label: this.$tc('bepo-turbo-suggest.list.columnCategory'),
                    allowResize: true
                },
                {
                    property: 'cmsPage.name',
                    dataIndex: 'cmsPage.name',
                    label: this.$tc('bepo-turbo-suggest.list.columnCmsPage'),
                    allowResize: true
                },
                {
                    property: 'salesChannel.name',
                    dataIndex: 'salesChannel.name',
                    label: this.$tc('bepo-turbo-suggest.list.columnSalesChannel'),
                    allowResize: true
                },
                {
                    property: 'priority',
                    dataIndex: 'priority',
                    label: this.$tc('bepo-turbo-suggest.list.columnPriority'),
                    allowResize: true,
                    align: 'center'
                }
            ];
        }
    },

    created() {
        this.repository = this.repositoryFactory.create('bepo_turbo_suggest_target');
        this.getList();
    },

    methods: {
        getList() {
            this.isLoading = true;

            const criteria = new Criteria(this.page, this.limit);
            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection));
            criteria.addAssociation('category');
            criteria.addAssociation('cmsPage');
            criteria.addAssociation('salesChannel');
            criteria.addAssociation('searchTerms');

            this.repository.search(criteria).then((result) => {
                this.searchTargets = result;
                this.total = result.total;
                this.isLoading = false;
            });
        },

        onChangeLanguage() {
            this.getList();
        }
    }
});
