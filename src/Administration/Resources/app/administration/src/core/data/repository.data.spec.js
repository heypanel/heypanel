/**
 * @sw-package framework
 */

import ChangesetGenerator from 'src/core/data/changeset-generator.data';
import RepositoryData from 'src/core/data/repository.data';
import IdCollection from 'test/_helper_/id.collection';
import EntityCollection from 'src/core/data/entity-collection.data';
import Criteria from 'src/core/data/criteria.data';

const clientMock = global.repositoryFactoryMock.clientMock;
const responses = global.repositoryFactoryMock.responses;
const repositoryFactory = HeyPanel.Service('repositoryFactory');
const DEFAULT_CURRENCY = 'b7d2554b0ce847cd82f3ac9bd1c0dfca';

function mockContext() {
    return {
        apiPath: 'http://heypanel.local/api',
        apiResourcePath: 'http://heypanel.local/api/v2',
        assetsPath: 'http://heypanel.local/bundles/',
        basePath: '',
        host: 'heypanel.local',
        inheritance: false,
        installationPath: 'http://heypanel.local',
        languageId: '2fbb5fe2e29a4d70aa5854ce7ce3e20b',
        currencyId: '7924299acc9641bfb8237a06e5aa0fa4',
        liveVersionId: '0fa91ce3e96a4bc2be4bd9ce752c3425',
        pathInfo: '/admin',
        port: 80,
        scheme: 'http',
        schemeAndHttpHost: 'http://heypanel.local',
        uri: 'http://heypanel.local/admin',
        authToken: {
            access: 'BwP_OL47uNW6k8iQzChh6SxE31XaleO_l4unyLNmFco',
        },
    };
}

function createRepositoryData() {
    return new RepositoryData(undefined, undefined, undefined, undefined, undefined, undefined, undefined, {});
}

describe('repository.data.ts', () => {
    beforeEach(async () => {
        clientMock.resetHistory();
    });

    it('should search with the criteria title', async () => {
        responses.addResponse({
            method: 'POST',
            url: '/search/question',
            status: 200,
            response: {
                data: [],
            },
        });

        responses.addResponse({
            method: 'POST',
            url: '/search-ids/question',
            status: 200,
            response: {
                data: [],
            },
        });

        responses.addResponse({
            method: 'POST',
            url: '/search/question?title=ImmaTest',
            status: 200,
            response: {
                data: [],
            },
        });

        responses.addResponse({
            method: 'POST',
            url: '/search-ids/question?title=ImmaTest',
            status: 200,
            response: {
                data: [],
            },
        });

        const repository = repositoryFactory.create('question');

        const criteriaWithoutTitle = new Criteria();
        const criteriaWithTitle = new Criteria();
        criteriaWithTitle.setTitle('ImmaTest');

        repository.search(criteriaWithoutTitle);
        repository.searchIds(criteriaWithoutTitle);

        expect(clientMock.history.post[0].url).toBe('/search/question');
        expect(clientMock.history.post[1].url).toBe('/search-ids/question');

        repository.search(criteriaWithTitle);
        repository.searchIds(criteriaWithTitle);

        expect(clientMock.history.post[2].url).toBe('/search/question?title=ImmaTest');
        expect(clientMock.history.post[3].url).toBe('/search-ids/question?title=ImmaTest');
    });

    it('should build the correct headers', async () => {
        const repositoryData = createRepositoryData('language');
        const actualHeaders = repositoryData.buildHeaders(mockContext());
        const exptectedHeaders = {
            'sw-language-id': '2fbb5fe2e29a4d70aa5854ce7ce3e20b',
            Accept: 'application/vnd.api+json',
            Authorization: 'Bearer BwP_OL47uNW6k8iQzChh6SxE31XaleO_l4unyLNmFco',
            'Content-Type': 'application/json',
            'sw-api-compatibility': true,
            'sw-currency-id': '7924299acc9641bfb8237a06e5aa0fa4',
        };

        expect(actualHeaders).toEqual(exptectedHeaders);
    });

    it('should create one delete operation for multiple deletes', async () => {
        const ids = new IdCollection();

        responses.addResponse({
            method: 'Post',
            url: '_action/sync',
            status: 200,
            response: {},
        });

        const repository = repositoryFactory.create('question', null, {
            useSync: true,
        });
        const context = HeyPanel.Context.api;
        const question = repository.create(context, ids.get('question'));

        question.name = 'test';
        question.questionNumber = ids.get('question');
        question.stock = 10;
        question.price = [
            { currencyId: DEFAULT_CURRENCY, gross: 15, net: 10, linked: false },
        ];
        question.tax = { name: 'test', taxRate: 15 };

        const categories = new EntityCollection(
            question.categories.source,
            question.categories.entity,
            question.categories.context,
            question.categories.criteria,
        );

        let factory = repositoryFactory.create('category');
        categories.add(factory.create(context, ids.get('cat-1')));
        categories.add(factory.create(context, ids.get('cat-2')));
        categories.add(factory.create(context, ids.get('cat-3')));

        const properties = new EntityCollection(
            question.properties.source,
            question.properties.entity,
            question.properties.context,
            question.properties.criteria,
        );

        factory = repositoryFactory.create('property_group_option');
        properties.add(factory.create(context, ids.get('option-1')));
        properties.add(factory.create(context, ids.get('option-2')));
        properties.add(factory.create(context, ids.get('option-3')));

        question.getOrigin().properties = properties;
        question.getOrigin().categories = categories;

        const changesetGenerator = new ChangesetGenerator();
        const changes = changesetGenerator.generate(question);

        expect(changes.deletionQueue).toHaveLength(6);

        // send new question to the server
        await repository.save(question);

        // expect that one request get send
        expect(clientMock.history.post).toHaveLength(1);

        // check that request for the question creation was created correctly
        const request = clientMock.history.post[0];

        expect(request.url).toBe('_action/sync');
        expect(request.headers['single-operation']).toBe(true);

        expect(request.data).toEqual(
            JSON.stringify([
                {
                    action: 'delete',
                    payload: [
                        {
                            questionId: ids.get('question'),
                            optionId: ids.get('option-1'),
                        },
                        {
                            questionId: ids.get('question'),
                            optionId: ids.get('option-2'),
                        },
                        {
                            questionId: ids.get('question'),
                            optionId: ids.get('option-3'),
                        },
                    ],
                    entity: 'question_property',
                },
                {
                    action: 'delete',
                    payload: [
                        {
                            questionId: ids.get('question'),
                            categoryId: ids.get('cat-1'),
                        },
                        {
                            questionId: ids.get('question'),
                            categoryId: ids.get('cat-2'),
                        },
                        {
                            questionId: ids.get('question'),
                            categoryId: ids.get('cat-3'),
                        },
                    ],
                    entity: 'question_category',
                },
                {
                    key: 'write',
                    action: 'upsert',
                    entity: 'question',
                    payload: [
                        {
                            id: ids.get('question'),
                            price: [
                                {
                                    currencyId: DEFAULT_CURRENCY,
                                    gross: 15,
                                    net: 10,
                                    linked: false,
                                },
                            ],
                            questionNumber: ids.get('question'),
                            stock: 10,
                            name: 'test',
                        },
                    ],
                },
            ]),
        );
    });

    it('should throw an 400 error when httpClient post call fails with error without source property', async () => {
        const questionRepository = repositoryFactory.create('question');
        const question = questionRepository.create();
        question.name = 'Our amazing question';

        responses.filterResponses((response) => {
            return response.url !== '_action/sync';
        });

        responses.addResponse({
            method: 'POST',
            url: '_action/sync',
            status: 400,
            response: {
                errors: [
                    {
                        status: '400',
                        code: 'CONTENT__DUPLICATE_PRODUCT_NUMBER',
                        title: 'Bad Request',
                        detail: 'Product with number "SW10000" already exists.',
                        meta: {
                            parameters: {
                                number: 'SW10000',
                            },
                        },
                    },
                ],
            },
        });

        let thrownError;

        try {
            await questionRepository.saveWithSync(question);
        } catch (e) {
            thrownError = e;
        }

        expect(thrownError.message).toBe('Request failed with status code 400');
    });
});
