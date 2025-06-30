import BulkEditBaseHandler from './bulk-edit-base.handler';
import RetryHelper from '../../../../core/helper/retry.helper';

const types = HeyPanel.Utils.types;

/**
 * @class
 * @extends BulkEditBaseHandler
 * @sw-package checkout
 */
class BulkEditCustomerHandler extends BulkEditBaseHandler {
    constructor() {
        super();
        this.name = 'bulkEditCustomerHandler';
        this.entityName = 'member';
        this.entityIds = [];
        this.memberGroupRegistrationService = HeyPanel.Service('memberGroupRegistrationService');
        this.memberRepository = HeyPanel.Service('repositoryFactory').create('member');
    }

    async bulkEdit(entityIds, payload) {
        this.entityIds = entityIds;

        const syncPayload = await this.buildBulkSyncPayload(payload);

        if (types.isEmpty(syncPayload)) {
            return Promise.resolve({ success: true });
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

    async bulkEditRequestedGroup(entityIds, payload) {
        const promises = [];
        const shouldTriggerFlows = HeyPanel.Store.get('swBulkEdit').isFlowTriggered;

        payload.forEach((change) => {
            if (!change.value) {
                return;
            }

            switch (change.value) {
                case 'decline':
                    promises.push(
                        RetryHelper.retry(() => {
                            this.memberGroupRegistrationService.decline(
                                entityIds,
                                {},
                                {
                                    'sw-skip-trigger-flow': !shouldTriggerFlows,
                                },
                                {
                                    silentError: true,
                                },
                            );
                        }),
                    );
                    break;
                case 'accept':
                    promises.push(
                        RetryHelper.retry(() => {
                            this.memberGroupRegistrationService.accept(
                                entityIds,
                                {},
                                {
                                    'sw-skip-trigger-flow': !shouldTriggerFlows,
                                },
                                {
                                    silentError: true,
                                },
                            );
                        }),
                    );
                    break;
                default:
                    throw new Error();
            }
        });

        return Promise.all(promises);
    }
}

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default BulkEditCustomerHandler;
