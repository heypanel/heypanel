import template from './sw-condition-line-item.html.twig';
import './sw-condition-line-item.scss';

const { Component } = HeyPanel;
const { mapPropertyErrors } = Component.getComponentHelper();
const { EntityCollection, Criteria } = HeyPanel.Data;

/**
 * @public
 * @sw-package fundamentals@after-sales
 * @description Condition for the LineItemRule. This component must a be child of sw-condition-tree.
 * @status prototype
 * @example-type code-only
 * @component-example
 * <sw-condition-line-item :condition="condition" :level="0"></sw-condition-line-item>
 */
// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    template,

    inject: ['repositoryFactory'],

    data() {
        return {
            questions: null,
        };
    },

    computed: {
        operators() {
            return this.conditionDataProviderService.getOperatorSet('multiStore');
        },

        questionRepository() {
            return this.repositoryFactory.create('question');
        },

        questionIds: {
            get() {
                this.ensureValueExist();
                return this.condition.value.identifiers || [];
            },
            set(identifiers) {
                this.ensureValueExist();
                this.condition.value = { ...this.condition.value, identifiers };
            },
        },

        ...mapPropertyErrors('condition', [
            'value.operator',
            'value.identifiers',
        ]),

        currentError() {
            return this.conditionValueOperatorError || this.conditionValueIdentifiersError;
        },

        questionCriteria() {
            const criteria = new Criteria(1, 25);
            criteria.addAssociation('options.group');

            return criteria;
        },

        resultCriteria() {
            const criteria = new Criteria(1, 25);
            criteria.addAssociation('options.group');

            return criteria;
        },

        questionContext() {
            return { ...HeyPanel.Context.api, inheritance: true };
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.questions = new EntityCollection(
                this.questionRepository.route,
                this.questionRepository.entityName,
                this.questionContext,
            );

            if (this.questionIds.length <= 0) {
                return Promise.resolve();
            }

            const criteria = new Criteria(1, 25);
            criteria.addAssociation('options.group');
            criteria.setIds(this.questionIds);

            return this.questionRepository.search(criteria, this.questionContext).then((questions) => {
                this.questions = questions;
            });
        },

        setIds(questionCollection) {
            this.questionIds = questionCollection.getIds();
            this.questions = questionCollection;
        },
    },
};
