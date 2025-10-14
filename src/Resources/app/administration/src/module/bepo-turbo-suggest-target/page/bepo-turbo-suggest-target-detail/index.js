import template from './bepo-turbo-suggest-target-detail.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;
const { mapPropertyErrors} = Component.getComponentHelper();

Component.register('bepo-turbo-suggest-target-detail', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
            searchTarget: null,
            isLoading: false,
            isSaveSuccessful: false,
            processSuccess: false,
            showTermModal: false,
            currentTerm: null
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.identifier)
        };
    },

    computed: {
        identifier() {
            return this.searchTarget !== null ? this.searchTarget.id : '';
        },

        searchTargetRepository() {
            return this.repositoryFactory.create('bepo_turbo_suggest_target');
        },

        searchTermRepository() {
            return this.repositoryFactory.create('bepo_turbo_suggest_term');
        },

        isCreateMode() {
            return this.$route.params.id === 'create';
        },

        ...mapPropertyErrors('searchTarget', [
            'salesChannelId'
        ]),

        termColumns() {
            return [
                {
                    property: 'term',
                    dataIndex: 'term',
                    label: this.$tc('bepo-turbo-suggest.detail.columnTerm'),
                    allowResize: true,
                    primary: true,
                    editable: true
                },
                {
                    property: 'language.name',
                    dataIndex: 'language.name',
                    label: this.$tc('bepo-turbo-suggest.detail.columnLanguage'),
                    allowResize: true
                },
                {
                    property: 'active',
                    dataIndex: 'active',
                    label: this.$tc('bepo-turbo-suggest.detail.columnTermActive'),
                    allowResize: true,
                    align: 'center'
                }
            ];
        }
    },

    watch: {
        'searchTarget.categoryId'(newValue) {
            if (newValue) {
                this.searchTarget.cmsPageId = null;
            }
        },

        'searchTarget.cmsPageId'(newValue) {
            if (newValue) {
                this.searchTarget.categoryId = null;
            }
        }
    },

    created() {
        this.loadEntityData();
    },

    methods: {
        loadEntityData() {
            this.isLoading = true;

            if (this.isCreateMode) {
                const context = Shopware.Context.api;
                this.searchTarget = this.searchTargetRepository.create(context);
                this.searchTarget.priority = 0;
                this.isLoading = false;
                return;
            }

            const criteria = new Criteria();
            criteria.addAssociation('category');
            criteria.addAssociation('cmsPage');
            criteria.addAssociation('salesChannel');
            criteria.addAssociation('searchTerms.language');

            this.searchTargetRepository.get(this.$route.params.id, Shopware.Context.api, criteria)
                .then((entity) => {
                    this.searchTarget = entity;
                    this.isLoading = false;
                });
        },

        onSave() {
            this.isLoading = true;

            this.searchTargetRepository.save(this.searchTarget).then(() => {
                this.isLoading = false;
                this.isSaveSuccessful = true;

                if (this.isCreateMode) {
                    this.$router.push({ name: 'bepo.turbo.suggest.target.detail', params: { id: this.searchTarget.id } });
                    return;
                }

                this.loadEntityData();
            }).catch(() => {
                this.isLoading = false;
                this.createNotificationError({
                    message: this.$tc('global.notification.notificationSaveErrorMessageRequiredFieldsInvalid')
                });
            });
        },

        onCancel() {
            this.$router.push({ name: 'bepo.turbo.suggest.target.list' });
        },

        onAddTerm() {
            if (!this.searchTarget || !this.searchTarget.id) {
                this.createNotificationError({
                    message: this.$tc('bepo-turbo-suggest.detail.errorSaveTargetFirst')
                });
                return;
            }

            const context = Shopware.Context.api;
            this.currentTerm = this.searchTermRepository.create(context);
            this.currentTerm.searchTargetId = this.searchTarget.id;
            this.currentTerm.active = true;
            this.showTermModal = true;
        },

        onEditTerm(term) {
            this.currentTerm = term;
            this.showTermModal = true;
        },

        onSaveTerm() {
            if (!this.currentTerm.term || !this.currentTerm.languageId) {
                this.createNotificationError({
                    message: this.$tc('bepo-turbo-suggest.detail.errorTermRequired')
                });
                return;
            }

            this.isLoading = true;

            this.searchTermRepository.save(this.currentTerm, Shopware.Context.api).then(() => {
                this.createNotificationSuccess({
                    message: this.$tc('bepo-turbo-suggest.detail.successTermSaved')
                });

                this.showTermModal = false;
                this.currentTerm = null;

                this.loadEntityData();
            }).catch(() => {
                this.isLoading = false;
                this.createNotificationError({
                    message: this.$tc('global.notification.notificationSaveErrorMessage')
                });
            });
        },

        onCancelTerm() {
            this.showTermModal = false;
            this.currentTerm = null;
        },

        onDeleteTerm(term) {
            this.searchTermRepository.delete(term.id, Shopware.Context.api).then(() => {
                this.createNotificationSuccess({
                    message: this.$tc('global.notification.notificationDeleteSuccessMessage')
                });
                this.loadEntityData();
            }).catch(() => {
                this.createNotificationError({
                    message: this.$tc('global.notification.notificationDeleteErrorMessage')
                });
            });
        },

        saveOnLanguageChange() {
            return this.onSave();
        },

        abortOnLanguageChange() {
            return this.loadEntityData();
        },

        onChangeLanguage() {
            this.loadEntityData();
        }
    }
});
