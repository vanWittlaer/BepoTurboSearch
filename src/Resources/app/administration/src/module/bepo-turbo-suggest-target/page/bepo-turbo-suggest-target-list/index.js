import template from './bepo-turbo-suggest-target-list.html.twig';

const { Component, Mixin, Context } = Shopware;
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
            sortDirection: 'DESC',
            showDeleteModal: null,
            total: 0
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        isSystemDefaultLanguage() {
            return Context.api.languageId === Context.api.systemLanguageId;
        },

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
                    property: 'landingPage.name',
                    dataIndex: 'landingPage.name',
                    label: this.$tc('bepo-turbo-suggest.list.columnLandingPage'),
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
            if (!this.repository) {
                return;
            }

            this.isLoading = true;

            const criteria = new Criteria(this.page, this.limit);
            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection));
            criteria.addAssociation('category');
            criteria.addAssociation('landingPage');
            criteria.addAssociation('salesChannel');
            criteria.addAssociation('searchTerms');

            this.repository.search(criteria, Context.api).then((result) => {
                this.searchTargets = result;
                this.total = result.total;
                this.isLoading = false;
            });
        },

        onChangeLanguage() {
            this.getList();
        },

        onCloseDeleteModal() {
            this.showDeleteModal = null;
        },

        onConfirmDelete(id) {
            if (!id) {
                return;
            }

            this.onCloseDeleteModal();

            this.repository.delete(id, Context.api).then(() => {
                this.getList();
            });
        },

        onInlineEditCancel() {
            this.getList();
        }
    }
});
