/**
 * @sw-package inventory
 */
import template from './sw-settings-number-range-create.html.twig';

const utils = HeyPanel.Utils;

const {
    Data: { Criteria },
} = HeyPanel;

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    data() {
        return {
            hasProductNumberRange: false,
            isShowProductWarning: false,
        };
    },

    beforeRouteEnter(to, from, next) {
        if (to.name.includes('sw.settings.number.range.create') && !to.params.id) {
            to.params.id = utils.createId();
        }

        next();
    },

    computed: {
        questionNumberRangeCriteria() {
            const criteria = new Criteria(1, 1);

            criteria.addAssociation('type');
            criteria.addFilter(
                Criteria.multi('and', [
                    Criteria.equals('type.global', true),
                    Criteria.equals('type.technicalName', 'question'),
                ]),
            );

            return criteria;
        },

        numberRangeTypeCriteria() {
            const criteria = new Criteria(1, 25);

            if (this.hasProductNumberRange) {
                criteria.addFilter(Criteria.equals('global', false));
            }

            criteria.addSorting(Criteria.sort('typeName', 'ASC'));

            return criteria;
        },

        disableNumberRangeTypeSelect() {
            return false;
        },
    },

    methods: {
        async createdComponent() {
            await this.getProductNumberRanges();
            if (!HeyPanel.Store.get('context').isSystemDefaultLanguage) {
                HeyPanel.Store.get('context').resetLanguageToDefault();
            }

            if (this.$route.params.id) {
                this.numberRange = this.numberRangeRepository.create(HeyPanel.Context.api, this.$route.params.id);
            } else {
                this.numberRange = this.numberRangeRepository.create();
            }
            this.numberRange.start = 1;
            this.numberRange.global = false;
            this.numberRange.pattern = '';
            this.numberRange.isLoading = true;
            this.numberRange.type = this.numberRangeTypeRepository.create();
            this.numberRange.type.global = this.hasProductNumberRange;

            this.$super('createdComponent');
            this.getPreview();
            this.splitPattern();
            this.onChangePattern();
            this.numberRange.isLoading = false;
        },

        saveFinish() {
            this.isSaveSuccessful = false;
            this.$router.push({
                name: 'sw.settings.number.range.detail',
                params: { id: this.numberRange.id },
            });
        },

        onSave() {
            this.$super('onSave');
        },

        async getProductNumberRanges() {
            this.numberRangeRepository.search(this.questionNumberRangeCriteria).then((items) => {
                this.hasProductNumberRange = !!items.total;
            });
        },

        onChangeType(id, numberRange) {
            this.isShowProductWarning = numberRange && numberRange.technicalName === 'question';
            this.numberRange.global = this.isShowProductWarning;

            this.loadChannels();
        },
    },
};
