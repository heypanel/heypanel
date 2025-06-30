const { Application, Service, Store } = HeyPanel;
const { Criteria } = HeyPanel.Data;

/**
 * @sw-package framework
 *
 * @module core/service/member-group-registration-listener
 */

/**
 * @sw-package checkout
 * @memberOf module:core/service/member-group-registration-listener
 * @method addCustomerGroupRegistrationListener
 * @param loginService
 */
// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default function addCustomerGroupRegistrationListener(loginService) {
    let applicationRoot = null;

    loginService.addOnLoginListener(checkForUpdates);

    async function checkForUpdates() {
        if (!HeyPanel.Service('acl').can('member.viewer')) {
            return;
        }

        const memberRepository = Service('repositoryFactory').create('member');
        const criteria = new Criteria(1, 25);
        criteria.addAssociation('requestedGroup');
        criteria.addFilter(Criteria.not('AND', [Criteria.equals('requestedGroupId', null)]));

        const members = await memberRepository.search(criteria, HeyPanel.Context.api);

        members.forEach(createNotification);
    }

    function createNotification(member) {
        const notification = {
            title: getApplicationRootReference().$tc('global.default.info'),
            message: getApplicationRootReference().$tc(
                'sw-member.memberGroupRegistration.notification.message',
                {
                    name: `${member.firstName} ${member.lastName}`,
                    groupName: member.requestedGroup.name,
                },
                0,
            ),
            actions: [
                {
                    label: getApplicationRootReference().$tc(
                        'sw-member.memberGroupRegistration.notification.openCustomer',
                    ),
                    route: {
                        name: 'sw.member.detail',
                        params: { id: member.id },
                    },
                },
            ],
            variant: 'info',
            appearance: 'notification',
            growl: true,
        };

        Store.get('notification').createNotification(notification);
    }

    function getApplicationRootReference() {
        if (!applicationRoot) {
            applicationRoot = Application.getApplicationRoot();
        }

        return applicationRoot;
    }
}
