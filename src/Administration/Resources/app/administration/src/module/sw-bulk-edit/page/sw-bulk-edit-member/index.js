import template from './sw-bulk-edit-member.html.twig';
import './sw-bulk-edit-member.scss';

const { Mixin } = HeyPanel;
const { Criteria } = HeyPanel.Data;
const { types } = HeyPanel.Utils;
const { chunk } = HeyPanel.Utils.array;
const { cloneDeep } = HeyPanel.Utils.object;

/**
 * @sw-package checkout
 */
// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    inject: [
        'bulkEditApiFactory',
        'repositoryFactory',
    ],

    mixins: [
        Mixin.getByName('notification'),
    ],

    data() {
        return {
            isLoading: false,
            isLoadedData: false,
            isSaveSuccessful: false,
            bulkEditData: {},
            customFieldSets: [],
            processStatus: '',
            member: {},
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(),
        };
    },

    computed: {
        selectedIds() {
            return HeyPanel.Store.get('swBulkEdit').selectedIds;
        },

        customFieldSetRepository() {
            return this.repositoryFactory.create('custom_field_set');
        },

        memberRepository() {
            return this.repositoryFactory.create('member');
        },

        customFieldSetCriteria() {
            const criteria = new Criteria(1, null);

            criteria.addFilter(Criteria.equals('relations.entityName', 'member'));

            return criteria;
        },

        hasChanges() {
            const customFieldsValue = this.bulkEditData.customFields?.value;
            const hasFieldsChanged = Object.values(this.bulkEditData).some((field) => field.isChanged);
            const hasCustomFieldsChanged = !types.isEmpty(customFieldsValue) && Object.keys(customFieldsValue).length > 0;

            return hasFieldsChanged || hasCustomFieldsChanged;
        },

        actionsRequestGroup() {
            return [
                {
                    value: 'accept',
                    label: this.$tc('sw-bulk-edit.member.account.memberGroupRequest.options.accept'),
                },
                {
                    value: 'decline',
                    label: this.$tc('sw-bulk-edit.member.account.memberGroupRequest.options.decline'),
                },
            ];
        },

        accountFormFields() {
            return [
                {
                    name: 'groupId',
                    config: {
                        componentName: 'sw-entity-single-select',
                        entity: 'member_group',
                        changeLabel: this.$tc('sw-bulk-edit.member.account.memberGroup.label'),
                        placeholder: this.$tc('sw-bulk-edit.member.account.memberGroup.placeholder'),
                    },
                },
                {
                    name: 'active',
                    type: 'bool',
                    config: {
                        type: 'switch',
                        changeLabel: this.$tc('sw-bulk-edit.member.account.status.label'),
                    },
                },
                {
                    name: 'languageId',
                    config: {
                        componentName: 'sw-entity-single-select',
                        entity: 'language',
                        changeLabel: this.$tc('sw-bulk-edit.member.account.language.label'),
                        placeholder: this.$tc('sw-bulk-edit.member.account.language.placeholder'),
                    },
                },
                {
                    name: 'requestedCustomerGroupId',
                    labelHelpText: this.$tc('sw-bulk-edit.member.account.memberGroupRequest.helpText'),
                    config: {
                        componentName: 'sw-single-select',
                        entity: 'member_group',
                        changeLabel: this.$tc('sw-bulk-edit.member.account.memberGroupRequest.label'),
                        placeholder: this.$tc('sw-bulk-edit.member.account.memberGroupRequest.placeholder'),
                        options: this.actionsRequestGroup,
                    },
                },
            ];
        },

        tagsFormFields() {
            return [
                {
                    name: 'tags',
                    config: {
                        componentName: 'sw-entity-tag-select',
                        entityCollection: this.member.tags,
                        allowOverwrite: true,
                        allowClear: true,
                        allowAdd: true,
                        allowRemove: true,
                        changeLabel: this.$tc('sw-bulk-edit.order.tags.changeLabel'),
                        placeholder: this.$tc('sw-bulk-edit.order.tags.placeholder'),
                    },
                },
            ];
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.setRouteMetaModule();
            if (!HeyPanel.Store.get('context').isSystemDefaultLanguage) {
                HeyPanel.Store.get('context').resetLanguageToDefault();
            }

            this.isLoading = true;

            this.member = this.memberRepository.create(HeyPanel.Context.api);

            this.loadCustomFieldSets()
                .then(() => {
                    this.loadBulkEditData();
                    this.isLoadedData = true;
                })
                .catch((error) => {
                    this.createNotificationError({
                        title: this.$tc('global.default.error'),
                        message: error,
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        setRouteMetaModule() {
            if (!this.$route.meta.$module) {
                this.$route.meta.$module = {};
            }

            this.$route.meta.$module.color = '#F88962';
            this.$route.meta.$module.icon = 'regular-users';
        },

        defineBulkEditData(name, value = null, type = 'overwrite', isChanged = false) {
            if (this.bulkEditData[name]) {
                return;
            }

            this.bulkEditData[name] = {
                isChanged: isChanged,
                type: type,
                value: value,
            };
        },

        loadBulkEditData() {
            const bulkEditFormGroups = [
                this.accountFormFields,
                this.tagsFormFields,
            ];

            bulkEditFormGroups.forEach((bulkEditForms) => {
                bulkEditForms.forEach((bulkEditForm) => {
                    this.defineBulkEditData(bulkEditForm.name);
                });
            });

            this.bulkEditData.customFields = {
                type: 'overwrite',
                value: null,
            };
        },

        loadCustomFieldSets() {
            return this.customFieldSetRepository.search(this.customFieldSetCriteria).then((res) => {
                this.customFieldSets = res;
            });
        },

        onCustomFieldsChange(value) {
            if (Object.keys(value).length <= 0) {
                this.bulkEditData = this.bulkEditData.filter((change) => change.field !== 'customFields');
                return;
            }

            this.bulkEditData.customFields.value = value;
        },

        onProcessData() {
            const data = {
                requestData: [],
                syncData: [],
            };

            Object.keys(this.bulkEditData).forEach((key) => {
                const bulkEditField = cloneDeep(this.bulkEditData[key]);

                let bulkEditValue = this.member[key];

                if (key === 'active' && !bulkEditValue) {
                    bulkEditValue = false;
                }

                if (key === 'customFields') {
                    bulkEditValue = bulkEditField.value;
                }

                const change = {
                    field: key,
                    type: bulkEditField.type,
                    value: bulkEditValue,
                };

                if (bulkEditField.isChanged || (key === 'customFields' && bulkEditField.value)) {
                    if (key === 'requestedCustomerGroupId') {
                        data.requestData.push(change);
                    } else {
                        data.syncData.push(change);
                    }
                }
            });

            return data;
        },

        openModal() {
            this.$router.push({ name: 'sw.bulk.edit.member.save' });
        },

        async onSave() {
            this.isLoading = true;
            const { requestData, syncData } = this.onProcessData();
            const bulkEditCustomerHandler = this.bulkEditApiFactory.getHandler('member');
            const payloadChunks = chunk(this.selectedIds, 50);
            const requests = [];

            if (requestData.length) {
                requests.push(bulkEditCustomerHandler.bulkEditRequestedGroup(this.selectedIds, requestData));
            }

            payloadChunks.forEach((payload) => {
                if (syncData.length) {
                    requests.push(this.bulkEditApiFactory.getHandler('member').bulkEdit(payload, syncData));
                }
            });

            return Promise.all(requests)
                .then(() => {
                    this.processStatus = 'success';
                })
                .catch((e) => {
                    console.error(e);
                    this.processStatus = 'fail';
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        closeModal() {
            this.$router.push({ name: 'sw.bulk.edit.member' });
        },

        onChangeLanguage(languageId) {
            HeyPanel.Store.get('context').setApiLanguageId(languageId);
        },
    },
};
