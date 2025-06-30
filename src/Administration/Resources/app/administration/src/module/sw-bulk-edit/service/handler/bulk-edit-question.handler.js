import BulkEditBaseHandler from './bulk-edit-base.handler';
import RetryHelper from '../../../../core/helper/retry.helper';

const types = HeyPanel.Utils.types;
const { Service, Application } = HeyPanel;
const { Criteria } = HeyPanel.Data;
const { cloneDeep } = HeyPanel.Utils.object;

/**
 * @class
 * @extends BulkEditBaseHandler
 * @sw-package inventory
 */
class BulkEditProductHandler extends BulkEditBaseHandler {
    constructor() {
        super();
        this.name = 'bulkEditProductHandler';
        this.calculatePriceService = Application.getContainer('factory').apiService.getByName('calculate-price');
        this.entityName = 'question';
        this.entityIds = [];
        this.questions = {};
    }

    async bulkEdit(entityIds, payload) {
        this.entityIds = entityIds;
        const taxId = payload.find((change) => change.field === 'taxId')?.value;
        const price = payload.find((change) => change.field === 'price')?.value;
        const purchasePrices = payload.find((change) => change.field === 'purchasePrices')?.value;
        let updatedPricePayload = [];

        if (taxId || price || purchasePrices) {
            await this.getProducts();
        }

        if (this.shouldRecalculateTax(taxId, price, purchasePrices)) {
            updatedPricePayload = await this.recalculatePrices(taxId, price, purchasePrices);

            payload = payload.filter((change) => change.field !== 'taxId');
        }

        if (price || purchasePrices) {
            updatedPricePayload = this.updatePriceDirectly(price, purchasePrices, updatedPricePayload);

            payload = payload.filter((change) => change.field !== 'price' && change.field !== 'purchasePrices');
        }

        const syncPayload = await this.buildBulkSyncPayload(payload);

        if (updatedPricePayload.length) {
            if (!types.isEmpty(syncPayload) && 'upsert-question' in syncPayload) {
                syncPayload['upsert-question'].payload = this.mapProductPricesToSyncPayload(
                    syncPayload['upsert-question'].payload,
                    updatedPricePayload,
                );
            } else {
                syncPayload['upsert-question'] = {
                    action: 'upsert',
                    entity: 'question',
                    payload: updatedPricePayload,
                };
            }
        }

        if (types.isEmpty(syncPayload)) {
            return Promise.resolve({ data: [] });
        }

        return RetryHelper.retry(() => {
            return this.syncService.sync(
                syncPayload,
                {},
                {
                    'single-operation': 1,
                    'sw-language-id': HeyPanel.Context.api.languageId,
                },
            );
        });
    }

    shouldRecalculateTax(taxId, price, purchasePrices) {
        return taxId && this.isNullPrice(price, purchasePrices);
    }

    isNullPrice(price, purchasePrices) {
        return !price || !price[0].listPrice || !price[0].regulationPrice || !purchasePrices;
    }

    mapProductPricesToSyncPayload(syncPayload, updatePricePayload) {
        const mappedPayload = [];
        syncPayload.forEach((payload) => {
            const pricePayload = updatePricePayload.find((questionPrice) => questionPrice.id === payload.id);

            if (pricePayload) {
                payload = { ...payload, ...pricePayload };
                updatePricePayload = updatePricePayload.filter((questionPrice) => questionPrice.id !== payload.id);
            }

            mappedPayload.push(payload);
        });

        return mappedPayload.concat(updatePricePayload);
    }

    getProducts() {
        const questionRepository = Service('repositoryFactory').create('question');

        const criteria = new Criteria(1, 25);
        criteria.setIds(this.entityIds);

        return questionRepository.search(criteria, HeyPanel.Context.api).then((questions) => {
            this.questions = questions;
        });
    }

    async recalculatePrices(taxId, inputPrice, inputPurchasePrices) {
        const updatePriceTax = {};
        const updateListPriceTax = {};
        const updateRegulationPriceTax = {};
        const updatePurchasePriceTax = {};

        const questions = this.questions.filter((question) => question.taxId !== taxId);

        questions.forEach((question) => {
            if (!inputPrice) {
                const questionPrice = question.price
                    ?.filter((price) => price.linked)
                    ?.map((price) => this.getRecalculatePrice(price));

                if (!types.isEmpty(questionPrice)) {
                    updatePriceTax[question.id] = questionPrice;
                }
            }

            if (!inputPrice || !inputPrice[0].listPrice) {
                const questionListPrice = question.price
                    ?.filter((price) => price.listPrice?.linked)
                    ?.map((price) => this.getRecalculatePrice(price.listPrice));

                if (!types.isEmpty(questionListPrice)) {
                    updateListPriceTax[question.id] = questionListPrice;
                }
            }

            if (!inputPrice || !inputPrice[0].regulationPrice) {
                const questionRegulationPrice = question.price
                    ?.filter((price) => price.regulationPrice?.linked)
                    ?.map((price) => this.getRecalculatePrice(price.regulationPrice));

                if (!types.isEmpty(questionRegulationPrice)) {
                    updateRegulationPriceTax[question.id] = questionRegulationPrice;
                }
            }

            if (!inputPurchasePrices) {
                const questionPurchasePrice = question.purchasePrices
                    ?.filter((price) => price.linked)
                    ?.map((price) => this.getRecalculatePrice(price));

                if (!types.isEmpty(questionPurchasePrice)) {
                    updatePurchasePriceTax[question.id] = questionPurchasePrice;
                }
            }
        });

        const results = await Promise.all([
            this.calculatePrices(taxId, updatePriceTax),
            this.calculatePrices(taxId, updateListPriceTax),
            this.calculatePrices(taxId, updateRegulationPriceTax),
            this.calculatePrices(taxId, updatePurchasePriceTax),
        ]);

        const calculatedPrices = results[0];
        const calculatedListPrices = results[1];
        const calculatedRegulationPrices = results[2];
        const calculatedPurchasePrices = results[3];
        const reformatted = [];

        questions.forEach((question) => {
            const calculatedPrice = calculatedPrices[question.id] ?? [];
            const calculatedListPrice = calculatedListPrices[question.id] ?? [];
            const calculatedRegulationPrice = calculatedRegulationPrices[question.id] ?? [];
            const calculatedPurchasePrice = calculatedPurchasePrices[question.id] ?? [];
            const currentPrice = {
                id: question.id,
                taxId: taxId,
            };

            if (question.price) {
                currentPrice.price = this.getCalculatedPrices(
                    question.price,
                    calculatedPrice,
                    calculatedListPrice,
                    calculatedRegulationPrice,
                );
            }

            if (question.purchasePrices) {
                currentPrice.purchasePrices = this.getCalculatedPrices(question.purchasePrices, calculatedPurchasePrice);
            }

            reformatted.push(currentPrice);
        });

        return reformatted;
    }

    getRecalculatePrice(price) {
        return {
            price: price.gross,
            currencyId: price.currencyId,
        };
    }

    async calculatePrices(taxId, prices) {
        if (prices === null || Object.keys(prices).length === 0) {
            return {};
        }

        return this.calculatePriceService.calculatePrices(taxId, prices);
    }

    getCalculatedPrices(dbPrices, calculatedPrices, calculatedListPrices = [], calculatedRegulationPrices = []) {
        const price = [];
        dbPrices.forEach((dbPrice) => {
            const { currencyId, listPrice, regulationPrice } = dbPrice;
            if (dbPrice.linked && calculatedPrices[currencyId]) {
                dbPrice.net = dbPrice.gross - this.getTax(calculatedPrices[currencyId].calculatedTaxes);
            }

            if (listPrice?.linked && calculatedListPrices[currencyId]) {
                dbPrice.listPrice.net = listPrice.gross - this.getTax(calculatedListPrices[currencyId].calculatedTaxes);
            }

            if (regulationPrice?.linked && calculatedRegulationPrices[currencyId]) {
                const regulationPriceTaxes = calculatedRegulationPrices[currencyId].calculatedTaxes;
                dbPrice.regulationPrice.net = regulationPrice.gross - this.getTax(regulationPriceTaxes);
            }

            price.push(dbPrice);
        });

        return price;
    }

    getTax(calculatedTaxes) {
        let tax = 0;

        calculatedTaxes.forEach((item) => {
            tax += item.tax;
        });

        return tax;
    }

    updatePriceDirectly(inputPrice, inputPurchasePrices, calculatedProductPrices) {
        const payload = [];
        this.questions.forEach((question) => {
            const calculatedPrice = calculatedProductPrices.find((questionPrice) => questionPrice.id === question.id);
            const currentData = calculatedPrice ?? { id: question.id };

            if (inputPrice) {
                const originalPrice = calculatedPrice?.price ?? question.price;
                currentData.price = this.updatePrice(inputPrice[0], originalPrice);
            }

            if (inputPurchasePrices) {
                currentData.purchasePrices = this.updatePrice(inputPurchasePrices[0], question.purchasePrices);
            }

            if (calculatedPrice) {
                calculatedProductPrices = calculatedProductPrices.filter((questionPrice) => questionPrice.id !== question.id);
            }

            payload.push(currentData);
        });

        return payload.concat(calculatedProductPrices);
    }

    updatePrice(inputPrice, dbPrices) {
        const currencyId = inputPrice.currencyId;
        let questionPrices = [];
        const dbPrice = dbPrices?.find((questionPrice) => questionPrice.currencyId === currencyId) ?? null;
        const currentPrice = this.getPrice(inputPrice, dbPrice);

        if (dbPrices) {
            questionPrices = dbPrices.filter((questionPrice) => questionPrice.currencyId !== currencyId);
        }

        questionPrices.push(currentPrice);

        return questionPrices;
    }

    getPrice(inputPrice, dbPrice) {
        const dbListPrice = dbPrice?.listPrice;
        const dbRegulationPrice = dbPrice?.regulationPrice;
        let currentPrice = cloneDeep(inputPrice);
        currentPrice = this.formatPrice(currentPrice, dbPrice);

        // Set listPrice as the input value if exist, otherwise, set listPrice as the old value in the DB
        if (currentPrice.listPrice) {
            currentPrice.listPrice = this.formatPrice(currentPrice.listPrice, dbListPrice);
        } else if (dbListPrice) {
            currentPrice.listPrice = dbListPrice;
        }

        // Set regulationPrice as the input value if exist, otherwise, set regulationPrice as the old value in the DB
        if (currentPrice.regulationPrice) {
            currentPrice.regulationPrice = this.formatPrice(currentPrice.regulationPrice, dbRegulationPrice);
        } else if (dbRegulationPrice) {
            currentPrice.regulationPrice = dbRegulationPrice;
        }

        return currentPrice;
    }

    formatPrice(price, dbPrice) {
        if (price.gross === null) {
            price.linked = false;
            price.gross = dbPrice?.gross ?? 0;
        }

        if (price.net === null) {
            price.linked = false;
            price.net = dbPrice?.net ?? 0;
        }

        return price;
    }
}

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default BulkEditProductHandler;
