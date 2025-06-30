/**
 * @sw-package inventory
 */
import BulkEditApiFactory from 'src/module/sw-bulk-edit/service/bulk-edit.api.factory';
import BulkEditProductHandler from 'src/module/sw-bulk-edit/service/handler/bulk-edit-question.handler';

const EntityDefinitionFactory = require('src/core/factory/entity-definition.factory').default;

const highAssociationCount = 750;

function getBulkEditApiFactory() {
    return new BulkEditApiFactory();
}

function getBulkEditProductHandler() {
    const factory = getBulkEditApiFactory();

    const handler = factory.getHandler('question');

    handler.syncService = {
        sync: () => {
            return true;
        },
    };

    return handler;
}

function paginate(data, criteria) {
    return data.slice((criteria.page - 1) * criteria.limit, criteria.page * criteria.limit);
}

describe('module/sw-bulk-edit/service/handler/bulk-edit-question.handler', () => {
    it('is registered correctly', async () => {
        const factory = getBulkEditApiFactory();

        const handler = factory.getHandler('question');

        expect(handler).toBeInstanceOf(BulkEditProductHandler);
        expect(handler.name).toBe('bulkEditProductHandler');
    });

    it('should call buildBulkSyncPayload when using bulkEdit', async () => {
        const handler = getBulkEditProductHandler();

        const bulkEditProductHandler = jest.spyOn(handler, 'buildBulkSyncPayload').mockImplementation(() =>
            Promise.resolve({
                upsert: {
                    entity: 'order',
                },
            }),
        );

        const result = await handler.bulkEdit(
            [
                'abc',
                'xyz',
            ],
            [],
        );

        expect(bulkEditProductHandler).toHaveBeenCalledTimes(1);
        expect(bulkEditProductHandler).toHaveBeenCalledWith([]);
        expect(handler.entityName).toBe('question');
        expect(handler.entityIds).toEqual([
            'abc',
            'xyz',
        ]);
        expect(result).toBe(true);
    });

    it('should call syncService sync when using bulkEditProductHandler', async () => {
        const handler = getBulkEditProductHandler();
        const payload = {
            question: { operation: 'upsert', entity: 'question', payload: [] },
        };

        const buildBulkSyncPayloadMethod = jest
            .spyOn(handler, 'buildBulkSyncPayload')
            .mockImplementation(() => Promise.resolve(payload));
        const syncMethod = jest.spyOn(handler.syncService, 'sync').mockImplementation(() => Promise.resolve(true));

        const changes = [
            { type: 'overwrite', field: 'description', value: 'test' },
        ];

        const result = await handler.bulkEdit([], changes);

        expect(buildBulkSyncPayloadMethod).toHaveBeenCalledTimes(1);
        expect(buildBulkSyncPayloadMethod).toHaveBeenCalledWith(changes);

        expect(syncMethod).toHaveBeenCalledTimes(1);
        expect(syncMethod).toHaveBeenCalledWith(
            payload,
            {},
            {
                'single-operation': 1,
                'sw-language-id': HeyPanel.Context.api.languageId,
            },
        );
        expect(result).toBe(true);
    });

    describe('test buildBulkSyncPayload', () => {
        let handler = null;

        beforeEach(async () => {
            handler = getBulkEditProductHandler();

            handler.groupedPayload = {
                upsert: {},
                delete: {},
            };
            handler.entityName = 'question';
            handler.entityIds = [
                'question_1',
                'question_2',
            ];
        });

        const cases = [
            [
                'empty changes',
                [],
                {},
            ],
            [
                'invalid field',
                [
                    {
                        type: 'overwrite',
                        field: 'invalid-field',
                        value: 'test',
                    },
                    {
                        type: 'clear',
                        field: 'invalid-field-2',
                        value: 'test',
                    },
                ],
                {},
            ],
            [
                'unsupported type',
                [
                    {
                        type: 'not-support-type',
                        field: 'description',
                        value: 'test',
                    },
                ],
                {},
            ],
            [
                'overwrite single field',
                [{ type: 'overwrite', field: 'description', value: 'test' }],
                {
                    'upsert-question': {
                        action: 'upsert',
                        entity: 'question',
                        payload: [
                            {
                                id: 'question_1',
                                description: 'test',
                            },
                            {
                                id: 'question_2',
                                description: 'test',
                            },
                        ],
                    },
                },
            ],
            [
                'overwrite custom field',
                [
                    {
                        type: 'overwrite',
                        field: 'customFields',
                        value: {
                            custom_health_nostrum_facere_quo: 'lorem ipsum',
                        },
                    },
                ],
                {
                    'upsert-question': {
                        action: 'upsert',
                        entity: 'question',
                        payload: [
                            {
                                id: 'question_1',
                                customFields: {
                                    custom_health_nostrum_facere_quo: 'lorem ipsum',
                                },
                            },
                            {
                                id: 'question_2',
                                customFields: {
                                    custom_health_nostrum_facere_quo: 'lorem ipsum',
                                },
                            },
                        ],
                    },
                },
            ],
            [
                'clear single string field',
                [{ type: 'clear', field: 'description' }],
                {
                    'upsert-question': {
                        action: 'upsert',
                        entity: 'question',
                        payload: [
                            {
                                id: 'question_1',
                                description: null,
                            },
                            {
                                id: 'question_2',
                                description: null,
                            },
                        ],
                    },
                },
            ],
            [
                'clear multiple scalar fields',
                [
                    { type: 'clear', field: 'description' },
                    { type: 'clear', field: 'stock' },
                ],
                {
                    'upsert-question': {
                        action: 'upsert',
                        entity: 'question',
                        payload: [
                            {
                                id: 'question_1',
                                description: null,
                                stock: 0,
                            },
                            {
                                id: 'question_2',
                                description: null,
                                stock: 0,
                            },
                        ],
                    },
                },
            ],
            [
                'overwrite multiple fields',
                [
                    { type: 'overwrite', field: 'description', value: 'test' },
                    {
                        type: 'overwrite',
                        field: 'stock',
                        value: 10,
                    },
                ],
                {
                    'upsert-question': {
                        action: 'upsert',
                        entity: 'question',
                        payload: [
                            {
                                id: 'question_1',
                                description: 'test',
                                stock: 10,
                            },
                            {
                                id: 'question_2',
                                description: 'test',
                                stock: 10,
                            },
                        ],
                    },
                },
            ],
            [
                'changes with invalid field and unsupported type',
                [
                    { type: 'overwrite', field: 'description', value: 'test' },
                    {
                        type: 'overwrite',
                        field: 'invalid-field',
                        value: 10,
                    },
                    { type: 'un-support-type', field: 'name', value: 10 },
                ],
                {
                    'upsert-question': {
                        action: 'upsert',
                        entity: 'question',
                        payload: [
                            {
                                id: 'question_1',
                                description: 'test',
                            },
                            {
                                id: 'question_2',
                                description: 'test',
                            },
                        ],
                    },
                },
            ],
            [
                'change association with invalid field',
                [
                    {
                        type: 'overwrite',
                        field: 'invalidField',
                        value: [
                            'category_1',
                            'category_2',
                        ],
                    },
                ],
                {},
            ],
            [
                'overwrite an association with no duplicated',
                [
                    {
                        type: 'overwrite',
                        field: 'categories',
                        value: [
                            { id: 'category_1' },
                            { id: 'category_2' },
                        ],
                    },
                ],
                {
                    'upsert-question_category': {
                        action: 'upsert',
                        entity: 'question_category',
                        payload: [
                            {
                                questionId: 'question_1',
                                categoryId: 'category_1',
                            },
                            {
                                questionId: 'question_2',
                                categoryId: 'category_1',
                            },
                            {
                                questionId: 'question_1',
                                categoryId: 'category_2',
                            },
                            {
                                questionId: 'question_2',
                                categoryId: 'category_2',
                            },
                        ],
                    },
                },
                {
                    question_category: [],
                },
            ],
            [
                'overwrite an association with some duplicated',
                [
                    {
                        type: 'overwrite',
                        field: 'categories',
                        value: [
                            { id: 'category_1' },
                            { id: 'category_2' },
                        ],
                    },
                ],
                {
                    'upsert-question_category': {
                        action: 'upsert',
                        entity: 'question_category',
                        payload: [
                            {
                                questionId: 'question_2',
                                categoryId: 'category_1',
                            },
                            {
                                questionId: 'question_1',
                                categoryId: 'category_2',
                            },
                        ],
                    },
                    'delete-question_category': {
                        action: 'delete',
                        entity: 'question_category',
                        payload: [
                            {
                                questionId: 'question_1',
                                categoryId: 'category_3',
                            },
                            {
                                questionId: 'question_2',
                                categoryId: 'category_4',
                            },
                        ],
                    },
                },
                {
                    question_category: [
                        {
                            questionId: 'question_1',
                            categoryId: 'category_1',
                        },
                        {
                            questionId: 'question_2',
                            categoryId: 'category_2',
                        },
                        {
                            questionId: 'question_1',
                            categoryId: 'category_3',
                        },
                        {
                            questionId: 'question_2',
                            categoryId: 'category_4',
                        },
                    ],
                },
            ],
            [
                'overwrite an oneToMany association',
                [
                    {
                        type: 'overwrite',
                        field: 'media',
                        mappingReferenceField: 'mediaId',
                        value: [
                            { mediaId: 'media_1' },
                            { mediaId: 'media_2' },
                        ],
                    },
                ],
                {
                    'upsert-question_media': {
                        action: 'upsert',
                        entity: 'question_media',
                        payload: [
                            {
                                questionId: 'question_1',
                                mediaId: 'media_1',
                            },
                            {
                                questionId: 'question_2',
                                mediaId: 'media_1',
                            },
                        ],
                    },
                    'delete-question_media': {
                        action: 'delete',
                        entity: 'question_media',
                        payload: [
                            {
                                id: 'question_media_3',
                            },
                        ],
                    },
                },
                {
                    question_media: [
                        {
                            id: 'question_media_1',
                            questionId: 'question_1',
                            mediaId: 'media_2',
                        },
                        {
                            id: 'question_media_2',
                            questionId: 'question_2',
                            mediaId: 'media_2',
                        },
                        {
                            id: 'question_media_3',
                            questionId: 'question_2',
                            mediaId: 'media_3',
                        },
                    ],
                },
            ],
            [
                'overwrite an oneToMany association with extra field',
                [
                    {
                        type: 'overwrite',
                        field: 'visibilities',
                        mappingReferenceField: 'channelId',
                        value: [
                            { channelId: 'scn_1', visibility: 20 },
                            { channelId: 'scn_2', visibility: 30 },
                        ],
                    },
                ],
                {
                    'upsert-question_visibility': {
                        action: 'upsert',
                        entity: 'question_visibility',
                        payload: [
                            {
                                questionId: 'question_2',
                                channelId: 'scn_1',
                                visibility: 20,
                            },
                            {
                                questionId: 'question_1',
                                channelId: 'scn_2',
                                visibility: 30,
                            },
                            {
                                id: 'question_scn_2',
                                visibility: 30,
                            },
                        ],
                    },
                    'delete-question_visibility': {
                        action: 'delete',
                        entity: 'question_visibility',
                        payload: [
                            {
                                id: 'question_scn_3',
                            },
                            {
                                id: 'question_scn_4',
                            },
                        ],
                    },
                },
                {
                    question_visibility: [
                        {
                            id: 'question_scn_1',
                            questionId: 'question_1',
                            visibility: 20,
                            channelId: 'scn_1',
                        },
                        {
                            id: 'question_scn_2',
                            questionId: 'question_2',
                            visibility: 20,
                            channelId: 'scn_2',
                        },
                        {
                            id: 'question_scn_3',
                            questionId: 'question_1',
                            channelId: 'scn_3',
                        },
                        {
                            id: 'question_scn_4',
                            questionId: 'question_2',
                            channelId: 'scn_4',
                        },
                    ],
                },
            ],
            [
                'add an oneToMany association with mapping reference field',
                [
                    {
                        type: 'add',
                        field: 'media',
                        mappingReferenceField: 'mediaId',
                        value: [
                            { mediaId: 'media_1' },
                            { mediaId: 'media_2' },
                        ],
                    },
                ],
                {
                    'upsert-question_media': {
                        action: 'upsert',
                        entity: 'question_media',
                        payload: [
                            {
                                questionId: 'question_1',
                                mediaId: 'media_1',
                            },
                            {
                                questionId: 'question_2',
                                mediaId: 'media_1',
                            },
                            {
                                questionId: 'question_2',
                                mediaId: 'media_2',
                            },
                        ],
                    },
                },
                {
                    question_media: [
                        {
                            id: 'question_media_1',
                            questionId: 'question_1',
                            mediaId: 'media_2',
                        },
                    ],
                },
            ],
            [
                'add an oneToMany association without mapping reference field',
                [
                    {
                        type: 'add',
                        field: 'questionLocations',
                        value: [
                            { name: 'location 2' },
                            { name: 'location 3' },
                        ],
                    },
                ],
                {
                    'upsert-question_location': {
                        action: 'upsert',
                        entity: 'question_location',
                        payload: [
                            {
                                questionId: 'question_1',
                                name: 'location 2',
                            },
                            {
                                questionId: 'question_1',
                                name: 'location 3',
                            },
                            {
                                questionId: 'question_2',
                                name: 'location 2',
                            },
                            {
                                questionId: 'question_2',
                                name: 'location 3',
                            },
                        ],
                    },
                },
                {
                    question_location: [
                        {
                            id: 'question_location_1',
                            questionId: 'question_1',
                            name: 'location 1',
                        },
                    ],
                },
            ],
            [
                'remove an oneToMany association',
                [
                    {
                        type: 'clear',
                        field: 'media',
                        mappingReferenceField: 'mediaId',
                        value: [
                            { mediaId: 'media_1' },
                            { mediaId: 'media_2' },
                        ],
                    },
                ],
                {
                    'delete-question_media': {
                        action: 'delete',
                        entity: 'question_media',
                        payload: [
                            {
                                id: 'question_media_1',
                            },
                        ],
                    },
                },
                {
                    question_media: [
                        {
                            id: 'question_media_1',
                            questionId: 'question_1',
                            mediaId: 'media_2',
                        },
                    ],
                },
            ],
            [
                'clear an oneToMany association',
                [
                    {
                        type: 'clear',
                        field: 'media',
                        mappingReferenceField: 'mediaId',
                    },
                ],
                {
                    'delete-question_media': {
                        action: 'delete',
                        entity: 'question_media',
                        payload: [
                            {
                                id: 'question_media_1',
                            },
                            {
                                id: 'question_media_2',
                            },
                        ],
                    },
                },
                {
                    question_media: [
                        {
                            id: 'question_media_1',
                            questionId: 'question_1',
                            mediaId: 'media_1',
                        },
                        {
                            id: 'question_media_2',
                            questionId: 'question_1',
                            mediaId: 'media_2',
                        },
                    ],
                },
            ],
            [
                'overwrite an association with all duplicated',
                [
                    {
                        type: 'overwrite',
                        field: 'categories',
                        value: [
                            { id: 'category_1' },
                            { id: 'category_2' },
                        ],
                    },
                ],
                {},
                {
                    question_category: [
                        {
                            questionId: 'question_1',
                            categoryId: 'category_1',
                        },
                        {
                            questionId: 'question_1',
                            categoryId: 'category_2',
                        },
                        {
                            questionId: 'question_2',
                            categoryId: 'category_1',
                        },
                        {
                            questionId: 'question_2',
                            categoryId: 'category_2',
                        },
                    ],
                },
            ],
            [
                'overwrite an association with duplicated',
                [
                    {
                        type: 'overwrite',
                        field: 'categories',
                        value: [
                            { id: 'category_1' },
                            { id: 'category_2' },
                        ],
                    },
                ],
                {
                    'upsert-question_category': {
                        action: 'upsert',
                        entity: 'question_category',
                        payload: [
                            {
                                questionId: 'question_2',
                                categoryId: 'category_1',
                            },
                            {
                                questionId: 'question_1',
                                categoryId: 'category_2',
                            },
                        ],
                    },
                },
                {
                    question_category: [
                        {
                            questionId: 'question_1',
                            categoryId: 'category_1',
                        },
                        {
                            questionId: 'question_2',
                            categoryId: 'category_2',
                        },
                    ],
                },
            ],
            [
                'add an association',
                [
                    {
                        type: 'add',
                        field: 'categories',
                        value: [
                            { id: 'category_1' },
                            { id: 'category_2' },
                            { id: 'category_3' },
                        ],
                    },
                ],
                {
                    'upsert-question_category': {
                        action: 'upsert',
                        entity: 'question_category',
                        payload: [
                            {
                                questionId: 'question_2',
                                categoryId: 'category_1',
                            },
                            {
                                questionId: 'question_1',
                                categoryId: 'category_2',
                            },
                            {
                                questionId: 'question_2',
                                categoryId: 'category_3',
                            },
                        ],
                    },
                },
                {
                    question_category: [
                        {
                            questionId: 'question_1',
                            categoryId: 'category_1',
                        },
                        {
                            questionId: 'question_2',
                            categoryId: 'category_2',
                        },
                        {
                            questionId: 'question_1',
                            categoryId: 'category_3',
                        },
                        {
                            questionId: 'question_2',
                            categoryId: 'category_4',
                        },
                    ],
                },
            ],
            [
                'remove an association',
                [
                    {
                        type: 'remove',
                        field: 'categories',
                        value: [
                            { id: 'category_1' },
                            { id: 'category_2' },
                        ],
                    },
                ],
                {
                    'delete-question_category': {
                        action: 'delete',
                        entity: 'question_category',
                        payload: [
                            {
                                questionId: 'question_1',
                                categoryId: 'category_1',
                            },
                            {
                                questionId: 'question_2',
                                categoryId: 'category_2',
                            },
                        ],
                    },
                },
                {
                    question_category: [
                        {
                            questionId: 'question_1',
                            categoryId: 'category_1',
                        },
                        {
                            questionId: 'question_2',
                            categoryId: 'category_2',
                        },
                    ],
                },
            ],
            [
                'all operators at once',
                [
                    { type: 'overwrite', field: 'description', value: 'test' },
                    { type: 'clear', field: 'stock' },
                    {
                        type: 'remove',
                        mappingReferenceField: 'mediaId',
                        field: 'media',
                        value: { mediaId: 'media_1' },
                    },
                    {
                        type: 'add',
                        field: 'categories',
                        value: [
                            { id: 'category_1' },
                            { id: 'category_2' },
                        ],
                    },
                ],
                {
                    'upsert-question': {
                        action: 'upsert',
                        entity: 'question',
                        payload: [
                            {
                                id: 'question_1',
                                description: 'test',
                                stock: 0,
                            },
                            {
                                id: 'question_2',
                                description: 'test',
                                stock: 0,
                            },
                        ],
                    },
                    'upsert-question_category': {
                        action: 'upsert',
                        entity: 'question_category',
                        payload: [
                            {
                                questionId: 'question_2',
                                categoryId: 'category_1',
                            },
                            {
                                questionId: 'question_1',
                                categoryId: 'category_2',
                            },
                        ],
                    },
                    'delete-question_media': {
                        action: 'delete',
                        entity: 'question_media',
                        payload: [
                            {
                                id: 'question_media_1',
                            },
                        ],
                    },
                },
                {
                    question_category: [
                        {
                            questionId: 'question_1',
                            categoryId: 'category_1',
                        },
                        {
                            questionId: 'question_2',
                            categoryId: 'category_2',
                        },
                        {
                            questionId: 'question_1',
                            categoryId: 'category_3',
                        },
                        {
                            questionId: 'question_2',
                            categoryId: 'category_4',
                        },
                    ],
                    question_media: [
                        {
                            questionId: 'question_2',
                            mediaId: 'media_1',
                            id: 'question_media_1',
                        },
                    ],
                },
            ],
            [
                'add more than 500 oneToMany association',
                [
                    {
                        type: 'add',
                        field: 'media',
                        mappingReferenceField: 'mediaId',
                        value: Array(highAssociationCount)
                            .fill(0)
                            .map((v, k) => ({ mediaId: `media_${k}` })),
                    },
                ],
                {
                    'upsert-question_media': {
                        action: 'upsert',
                        entity: 'question_media',
                        payload: Array(highAssociationCount)
                            .fill(0)
                            .map((v, k) => ({
                                questionId: 'question_1',
                                mediaId: `media_${k}`,
                            })),
                    },
                },
                {
                    question_media: Array(highAssociationCount)
                        .fill(0)
                        .map((v, k) => ({
                            id: `question_media_${k}`,
                            questionId: 'question_2',
                            mediaId: `media_${k}`,
                        })),
                },
            ],
            [
                'add more than 500 manyToMany association',
                [
                    {
                        type: 'add',
                        field: 'categories',
                        value: Array(highAssociationCount)
                            .fill(0)
                            .map((v, k) => ({ id: `category_${k}` })),
                    },
                ],
                {
                    'upsert-question_category': {
                        action: 'upsert',
                        entity: 'question_category',
                        payload: Array(highAssociationCount)
                            .fill(0)
                            .map((v, k) => ({
                                questionId: 'question_1',
                                categoryId: `category_${k}`,
                            })),
                    },
                },
                {
                    question_category: Array(highAssociationCount)
                        .fill(0)
                        .map((v, k) => ({
                            id: `question_category_${k}`,
                            questionId: 'question_2',
                            categoryId: `category_${k}`,
                        })),
                },
            ],
            [
                'overwrite an oneToOne association',
                [
                    {
                        type: 'overwrite',
                        field: 'questionAI',
                        value: [{ name: 'ai 1' }],
                    },
                ],
                {
                    'upsert-question_ai': {
                        action: 'upsert',
                        entity: 'question_ai',
                        payload: [
                            {
                                id: 'question_ai_1',
                                name: 'ai 1',
                            },
                            {
                                questionId: 'question_2',
                                name: 'ai 1',
                            },
                        ],
                    },
                },
                {
                    question_ai: [
                        {
                            id: 'question_ai_1',
                            questionId: 'question_1',
                            name: 'b',
                        },
                    ],
                },
            ],
            [
                'add an oneToOne association',
                [
                    {
                        type: 'add',
                        field: 'questionAI',
                        value: [{ name: 'ai 1' }],
                    },
                ],
                {
                    'upsert-question_ai': {
                        action: 'upsert',
                        entity: 'question_ai',
                        payload: [
                            {
                                id: 'question_ai_1',
                                name: 'ai 1',
                            },
                            {
                                questionId: 'question_2',
                                name: 'ai 1',
                            },
                        ],
                    },
                },
                {
                    question_ai: [
                        {
                            id: 'question_ai_1',
                            questionId: 'question_1',
                            name: 'b',
                        },
                    ],
                },
            ],
            [
                'remove an oneToOne association',
                [
                    {
                        type: 'clear',
                        field: 'questionAI',
                        value: [{ name: 'ai 1' }],
                    },
                ],
                {
                    'delete-question_ai': {
                        action: 'delete',
                        entity: 'question_ai',
                        payload: [
                            {
                                id: 'question_ai_1',
                            },
                        ],
                    },
                },
                {
                    question_ai: [
                        {
                            id: 'question_ai_1',
                            questionId: 'question_1',
                            name: 'b',
                        },
                    ],
                },
            ],
        ];

        it.each(cases)('%s', async (testName, input, output, existAssociations = {}) => {
            const mockEntitySchema = {
                question: {
                    entity: 'question',
                    properties: {
                        id: {
                            type: 'uuid',
                        },
                        price: {
                            type: 'json_object',
                            properties: [],
                        },
                        cover: {
                            type: 'association',
                            relation: 'many_to_one',
                            entity: 'question_media',
                        },
                        name: {
                            type: 'string',
                        },
                        description: {
                            type: 'string',
                        },
                        stock: {
                            type: 'int',
                        },
                        customFields: {
                            type: 'json_object',
                        },
                        media: {
                            type: 'association',
                            relation: 'one_to_many',
                            entity: 'question_media',
                            localField: 'id',
                            referenceField: 'questionId',
                        },
                        manufacturer: {
                            type: 'association',
                            relation: 'many_to_one',
                            entity: 'question_manufacturer',
                        },
                        translations: {
                            type: 'association',
                            relation: 'one_to_many',
                            entity: 'question_translation',
                        },
                        categories: {
                            type: 'association',
                            relation: 'many_to_many',
                            entity: 'category',
                            flags: {},
                            localField: 'id',
                            referenceField: 'id',
                            mapping: 'question_category',
                            local: 'questionId',
                            reference: 'categoryId',
                        },
                        visibilities: {
                            type: 'association',
                            relation: 'one_to_many',
                            entity: 'question_visibility',
                            localField: 'id',
                            referenceField: 'questionId',
                        },
                        questionAI: {
                            type: 'association',
                            relation: 'one_to_one',
                            entity: 'question_ai',
                            localField: 'id',
                            referenceField: 'questionId',
                        },
                        questionLocations: {
                            type: 'association',
                            relation: 'one_to_many',
                            entity: 'question_location',
                            localField: 'id',
                            referenceField: 'questionId',
                        },
                    },
                },
                question_category: {
                    entity: 'question_category',
                    relation: 'many_to_many',
                },
                question_visibility: {
                    entity: 'question_visibility',
                    properties: {
                        id: {
                            type: 'uuid',
                        },
                        questionId: {
                            type: 'uuid',
                        },
                        channelId: {
                            type: 'uuid',
                        },
                        visibility: {
                            type: 'int',
                        },
                    },
                },
                question_manufacturer: {
                    entity: 'question_manufacturer',
                    properties: {
                        id: {
                            type: 'uuid',
                        },
                        name: {
                            type: 'string',
                        },
                        media: {
                            type: 'association',
                            relation: 'many_to_one',
                            entity: 'media',
                        },
                        questions: {
                            type: 'association',
                            relation: 'one_to_many',
                            entity: 'question',
                        },
                    },
                },
                question_media: {
                    entity: 'question_media',
                    properties: {
                        id: {
                            type: 'uuid',
                        },
                        media: {
                            type: 'association',
                            relation: 'many_to_one',
                            entity: 'media',
                        },
                    },
                },
                media: {
                    entity: 'media',
                    properties: {
                        id: {
                            type: 'uuid',
                        },
                        translations: {
                            type: 'association',
                            relation: 'one_to_many',
                            entity: 'media_translation',
                        },
                    },
                },
                question_ai: {
                    entity: 'question_ai',
                    relation: 'one_to_one',
                    properties: {
                        id: {
                            type: 'uuid',
                        },
                        questionId: {
                            type: 'uuid',
                        },
                        name: {
                            type: 'string',
                        },
                    },
                },
                question_location: {
                    entity: 'question_ai',
                    relation: 'one_to_many',
                    properties: {
                        id: {
                            type: 'uuid',
                        },
                        questionId: {
                            type: 'uuid',
                        },
                        name: {
                            type: 'string',
                        },
                    },
                },
            };

            HeyPanel.EntityDefinition = EntityDefinitionFactory;
            Object.keys(mockEntitySchema).forEach((entity) => {
                HeyPanel.EntityDefinition.add(entity, mockEntitySchema[entity]);
            });

            const spy = jest.spyOn(console, 'warn').mockImplementation();

            const spyRepository = jest.spyOn(handler.repositoryFactory, 'create').mockImplementation((entity) => {
                return {
                    search: async (criteria) => {
                        const response = paginate(existAssociations[entity], criteria);
                        response.total = existAssociations[entity].length;

                        return Promise.resolve(response);
                    },
                    searchIds: async (criteria) => {
                        const response = {
                            data: paginate(existAssociations[entity], criteria),
                            total: existAssociations[entity].length,
                        };

                        return Promise.resolve(response);
                    },
                };
            });

            expect(await handler.buildBulkSyncPayload(input)).toEqual(output);

            spy.mockRestore();

            spyRepository.mockRestore();
        });
    });
});
