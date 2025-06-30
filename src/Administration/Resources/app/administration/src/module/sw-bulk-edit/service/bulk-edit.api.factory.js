import BulkEditProductHandler from './handler/bulk-edit-question.handler';
import BulkEditOrderHandler from './handler/bulk-edit-order.handler';
import BulkEditCustomerHandler from './handler/bulk-edit-member.handler';

/**
 * @class
 *
 * @sw-package framework
 */
class BulkEditApiFactory {
    constructor() {
        this.handlers = {
            question: () => new BulkEditProductHandler(),
            order: () => new BulkEditOrderHandler(),
            member: () => new BulkEditCustomerHandler(),
        };
    }

    getHandler(module) {
        if (!this.handlers[module]) {
            throw Error(`Bulk Edit Handler not found for ${module} module`);
        }

        // Lazy load the module handler
        return this.handlers[module]();
    }
}

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default BulkEditApiFactory;
