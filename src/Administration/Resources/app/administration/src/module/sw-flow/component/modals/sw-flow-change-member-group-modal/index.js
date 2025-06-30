import template from './sw-flow-change-member-group-modal.html.twig';

const { Component, Store } = HeyPanel;
const { Criteria } = HeyPanel.Data;
const { mapState } = Component.getComponentHelper();
const { HeyPanelError } = HeyPanel.Classes;

/**
 * @private
 * @sw-package after-sales
 */
export default {
    template,

    inject: ['repositoryFactory'],

    emits: [
        'modal-close',
        'process-finish',
    ],

    props: {
        sequence: {
            type: Object,
            required: true,
        },
    },

    data() {
        return {
            memberGroupId: '',
            fieldError: null,
        };
    },

    computed: {
        memberGroupRepository() {
            return this.repositoryFactory.create('member_group');
        },

        memberGroupCriteria() {
            const criteria = new Criteria(1, 100);
            criteria.addSorting(Criteria.sort('name', 'ASC'));

            return criteria;
        },

        ...mapState(() => Store.get('swFlow'), ['memberGroups']),
    },

    watch: {
        memberGroupId(value) {
            if (value && this.fieldError) {
                this.fieldError = null;
            }
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.memberGroupId = this.sequence?.config?.memberGroupId || '';

            if (!this.memberGroups.length) {
                this.memberGroupRepository.search(this.memberGroupCriteria).then((data) => {
                    HeyPanel.Store.get('swFlow').memberGroups = data;
                });
            }
        },

        onClose() {
            this.$emit('modal-close');
        },

        onAddAction() {
            if (!this.memberGroupId) {
                this.fieldError = new HeyPanelError({
                    code: 'c1051bb4-d103-4f74-8988-acbcafc7fdc3',
                });

                return;
            }

            const sequence = {
                ...this.sequence,
                config: {
                    memberGroupId: this.memberGroupId,
                },
            };

            this.$emit('process-finish', sequence);
        },
    },
};
