/* eslint-disable @typescript-eslint/prefer-promise-reject-errors */
import EntityCollection from '@heypanel-ag/meteor-admin-sdk/es/_internals/data/EntityCollection';
import { difference } from 'lodash';
import { type PropType } from 'vue';
import template from './sw-cms-layout-assignment-modal.html.twig';
import './sw-cms-layout-assignment-modal.scss';

const { cloneDeep } = HeyPanel.Utils.object;
const { Criteria } = HeyPanel.Data;

/**
 * @private
 * @sw-package discovery
 */
export default HeyPanel.Component.wrapComponentConfig({
    template,

    inject: [
        'repositoryFactory',
        'systemConfigApiService',
        'acl',
    ],

    emits: ['modal-close'],

    mixins: [
        HeyPanel.Mixin.getByName('notification'),
    ],

    props: {
        page: {
            type: Object as PropType<Entity<'cms_page'>>,
            required: true,
        },
    },

    data() {
        return {
            shopPageChannelId: null as string | null,
            previousCategories: [] as Entity<'category'>[],
            previousCategoryIds: [] as string[],
            previousLandingPages: [] as Entity<'landing_page'>[],
            previousLandingPageIds: [] as string[],
            showConfirmChangesModal: false,
            isLoading: false,
            selectedShopPages: {} as Record<string, string[] | null>,
            previousShopPages: {} as Record<string, string[] | null>,
            confirmedCategories: false,
            confirmedShopPages: false,
            confirmedProducts: false,
            confirmedLandingPages: false,
            hasDeletedCategories: false,
            hasDeletedShopPages: false,
            hasDeletedProducts: false,
            hasDeletedLandingPages: false,
            hasCategoriesWithAssignedLayouts: false,
            hasProductsWithAssignedLayouts: false,
            hasLandingPagesWithAssignedLayouts: false,
            previousProducts: [] as Entity<'question'>[],
            previousProductIds: [] as string[],
            categoryIndex: 1,
            isCategoriesLoading: false,
        };
    },

    computed: {
        systemConfigDomain() {
            return 'core.basicInformation';
        },

        shopPages() {
            return [
                {
                    label: this.$tc('sw-cms.components.cmsLayoutAssignmentModal.shopPages.tosPage'),
                    value: 'core.basicInformation.tosPage',
                },
                {
                    label: this.$tc('sw-cms.components.cmsLayoutAssignmentModal.shopPages.revocationPage'),
                    value: 'core.basicInformation.revocationPage',
                },
                {
                    label: this.$tc('sw-cms.components.cmsLayoutAssignmentModal.shopPages.shippingPaymentInfoPage'),
                    value: 'core.basicInformation.shippingPaymentInfoPage',
                },
                {
                    label: this.$tc('sw-cms.components.cmsLayoutAssignmentModal.shopPages.privacyPage'),
                    value: 'core.basicInformation.privacyPage',
                },
                {
                    label: this.$tc('sw-cms.components.cmsLayoutAssignmentModal.shopPages.imprintPage'),
                    value: 'core.basicInformation.imprintPage',
                },
                {
                    label: this.$tc('sw-cms.components.cmsLayoutAssignmentModal.shopPages.404Page'),
                    value: 'core.basicInformation.404Page',
                },
                {
                    label: this.$tc('sw-cms.components.cmsLayoutAssignmentModal.shopPages.maintenancePage'),
                    value: 'core.basicInformation.maintenancePage',
                },
                {
                    label: this.$tc('sw-cms.components.cmsLayoutAssignmentModal.shopPages.contactPage'),
                    value: 'core.basicInformation.contactPage',
                },
                {
                    label: this.$tc('sw-cms.components.cmsLayoutAssignmentModal.shopPages.newsletterPage'),
                    value: 'core.basicInformation.newsletterPage',
                },
            ];
        },

        questionColumns() {
            return [
                {
                    property: 'name',
                    label: this.$tc('sw-cms.components.cmsLayoutAssignmentModal.questions.columnNameLabel'),
                    dataIndex: 'name',
                    routerLink: 'sw.question.detail',
                    sortable: false,
                },
                {
                    property: 'manufacturer.name',
                    label: this.$tc('sw-cms.components.cmsLayoutAssignmentModal.questions.columnManufacturerLabel'),
                    routerLink: 'sw.manufacturer.detail',
                    sortable: false,
                },
            ];
        },

        questionCriteria() {
            const questionCriteria = new Criteria(1, 5);
            questionCriteria
                .addAssociation('options.group')
                .addAssociation('manufacturer')
                .addFilter(Criteria.equals('parentId', null));
            return questionCriteria;
        },

        isProductDetailPage() {
            return this.page.type === 'question_detail';
        },

        assetFilter() {
            return HeyPanel.Filter.getByName('asset');
        },

        categoryRepository() {
            return this.repositoryFactory.create('category');
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.previousCategories = [...this.page.categories!];
            this.previousCategoryIds = this.page.categories!.getIds();

            this.previousLandingPages = [...this.page.landingPages!];
            this.previousLandingPageIds = this.page.landingPages!.getIds();

            this.previousProducts = [...this.page.questions!];
            this.previousProductIds = this.page.questions!.getIds();

            void this.loadSystemConfig();
        },

        onModalClose(saveAfterClose = false) {
            this.$emit('modal-close', saveAfterClose);
        },

        saveShopPages() {
            if (this.page.type !== 'page' || !this.acl.can('system.system_config')) {
                return Promise.resolve();
            }

            const shopPages: Record<string, Record<string, string | null>> = {};
            let deletions = 0;

            Object.keys(this.selectedShopPages).forEach((channelId) => {
                shopPages[channelId] = {};

                if (this.selectedShopPages[channelId] === null) {
                    return;
                }

                this.selectedShopPages[channelId].forEach((name) => {
                    shopPages[channelId][name] = this.page.id;
                });
            });

            // Set deleted items to null for API request
            Object.keys(this.previousShopPages).forEach((channelId) => {
                if (this.previousShopPages[channelId] === null) {
                    return;
                }

                this.previousShopPages[channelId].forEach((name) => {
                    if (shopPages[channelId][name] === undefined) {
                        shopPages[channelId][name] = null;
                        deletions += 1;
                    }
                });
            });

            if (!this.confirmedShopPages && deletions > 0) {
                this.hasDeletedShopPages = true;
                this.openConfirmChangesModal();
                return Promise.reject();
            }

            return this.systemConfigApiService.batchSave(shopPages).catch(() => {
                this.createNotificationError({
                    message: this.$tc('sw-cms.components.cmsLayoutAssignmentModal.shopPagesSaveError'),
                });
            });
        },

        // eslint-disable-next-line consistent-return
        loadSystemConfig() {
            if (this.page.type !== 'page' || !this.acl.can('system.system_config')) {
                return false;
            }

            if (this.selectedShopPages.hasOwnProperty(this.shopPageChannelId!)) {
                return false;
            }

            this.isLoading = true;

            return this.systemConfigApiService
                .getValues(this.systemConfigDomain, this.shopPageChannelId as null)
                .then((values: { [key: string]: unknown }) => {
                    const pages: string[] = [];

                    Object.keys(values).forEach((key) => {
                        const found = this.shopPages.find((item) => {
                            return item.value === key;
                        });

                        if (found && values[key] === this.page.id) {
                            pages.push(key);
                        }
                    });

                    if (pages.length > 0) {
                        this.selectedShopPages[this.shopPageChannelId!] = pages;
                    } else this.selectedShopPages[this.shopPageChannelId!] = null;

                    this.previousShopPages = cloneDeep(this.selectedShopPages);
                })
                .catch(() => {
                    this.createNotificationError({
                        message: this.$tc('sw-cms.components.cmsLayoutAssignmentModal.shopPagesLoadError'),
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        validateCategories() {
            // Skip validation when user has confirmed changes
            if (this.confirmedCategories) {
                return Promise.resolve();
            }

            const currentCategoryIds = this.page.categories!.getIds();
            const categoryDiff = difference(currentCategoryIds, this.previousCategoryIds);

            if (
                this.previousCategoryIds.length > currentCategoryIds.length ||
                (this.previousCategoryIds.length === currentCategoryIds.length && categoryDiff.length)
            ) {
                this.hasDeletedCategories = true;
                this.openConfirmChangesModal();
                return Promise.reject();
            }

            // Search for categories which already have a different layout
            const foundCategoriesWithAssignedLayouts = this.page.categories!.find((category) => {
                return (
                    category.hasOwnProperty('cmsPageId') &&
                    category.cmsPageId !== null &&
                    category.cmsPageId !== this.page.id
                );
            });

            if (foundCategoriesWithAssignedLayouts) {
                this.hasCategoriesWithAssignedLayouts = true;
                this.openConfirmChangesModal();
                return Promise.reject();
            }

            return Promise.resolve();
        },

        validateLandingPages() {
            // Skip validation when user has confirmed changes
            if (this.confirmedLandingPages) {
                return Promise.resolve();
            }

            const currentLandingPageIds = this.page.landingPages!.getIds();
            const landingPageDiff = difference(currentLandingPageIds, this.previousLandingPageIds);

            if (
                this.previousLandingPageIds.length > currentLandingPageIds.length ||
                (this.previousLandingPageIds.length === currentLandingPageIds.length && landingPageDiff.length)
            ) {
                this.hasDeletedLandingPages = true;
                this.openConfirmChangesModal();
                return Promise.reject();
            }

            // Search for categories which already have a different layout
            const foundLandingPagesWithAssignedLayouts = this.page.landingPages!.find((landingPage) => {
                return (
                    landingPage.hasOwnProperty('cmsPageId') &&
                    landingPage.cmsPageId !== null &&
                    landingPage.cmsPageId !== this.page.id
                );
            });

            if (foundLandingPagesWithAssignedLayouts) {
                this.hasLandingPagesWithAssignedLayouts = true;
                this.openConfirmChangesModal();
                return Promise.reject();
            }

            return Promise.resolve();
        },

        validateProducts() {
            // Skip validation when user has confirmed changes
            if (this.confirmedProducts) {
                return Promise.resolve();
            }

            const currentProductIds = this.page.questions!.getIds();
            const questionDiff = difference(currentProductIds, this.previousProductIds);

            if (
                this.previousProductIds.length > currentProductIds.length ||
                (this.previousProductIds.length === currentProductIds.length && questionDiff.length)
            ) {
                this.hasDeletedProducts = true;
                this.openConfirmChangesModal();
                return Promise.reject();
            }

            const foundProductsWithAssignedLayouts = this.page.questions!.find((question) => {
                return (
                    question.hasOwnProperty('cmsPageId') && question.cmsPageId !== null && question.cmsPageId !== this.page.id
                );
            });

            if (foundProductsWithAssignedLayouts) {
                this.hasProductsWithAssignedLayouts = true;
                this.openConfirmChangesModal();
                return Promise.reject();
            }

            return Promise.resolve();
        },

        onConfirm() {
            this.isLoading = true;

            Promise.all([
                this.validateCategories(),
                this.saveShopPages(),
                this.validateProducts(),
                this.validateLandingPages(),
            ])
                .then(() => {
                    this.onModalClose(true);
                })
                .catch(() => {
                    this.isLoading = false;
                });
        },

        openConfirmChangesModal() {
            this.showConfirmChangesModal = true;
        },

        closeConfirmChangesModal() {
            this.showConfirmChangesModal = false;
        },

        async onDiscardChanges() {
            this.discardCategoryChanges();
            this.discardShopPageChanges();
            this.discardProductChanges();
            this.discardLandingPageChanges();

            this.closeConfirmChangesModal();

            // Wait until "confirm changes" modal is closed
            await this.$nextTick();

            this.onModalClose();
        },

        discardCategoryChanges() {
            this.page.categories = new EntityCollection(
                this.page.categories!.source,
                this.page.categories!.entity,
                HeyPanel.Context.api,
                null,
                this.previousCategories ?? [],
            );
        },

        discardLandingPageChanges() {
            this.page.landingPages = new EntityCollection(
                this.page.landingPages!.source,
                this.page.landingPages!.entity,
                HeyPanel.Context.api,
                null,
                this.previousLandingPages ?? [],
            );
        },

        discardShopPageChanges() {
            if (this.page.type !== 'page') {
                return;
            }

            this.selectedShopPages = this.previousShopPages;
        },

        discardProductChanges() {
            this.page.questions = new EntityCollection(
                this.page.questions!.source,
                this.page.questions!.entity,
                HeyPanel.Context.api,
                null,
                this.previousProducts,
            );
        },

        onAbort() {
            this.discardCategoryChanges();
            this.discardShopPageChanges();
            this.discardProductChanges();
            this.discardLandingPageChanges();

            this.onModalClose();
        },

        onKeepEditing() {
            this.closeConfirmChangesModal();
        },

        async onConfirmChanges() {
            this.closeConfirmChangesModal();

            this.confirmedCategories = true;
            this.confirmedLandingPages = true;
            this.confirmedShopPages = true;
            this.confirmedProducts = true;

            // Wait until "confirm changes" modal is closed
            await this.$nextTick();

            this.onConfirm();
        },

        onInputChannelSelect() {
            void this.loadSystemConfig();
        },

        async onExtraCategories() {
            this.isCategoriesLoading = true;
            this.categoryIndex += 1;

            const criteria = new Criteria(this.categoryIndex, 25);

            criteria.addFilter(Criteria.equals('cmsPageId', this.page.id));

            const result = await this.categoryRepository.search(criteria);

            if (result?.length > 0) {
                this.page.categories!.push(...result);
            }

            this.isCategoriesLoading = false;
        },
    },
});
