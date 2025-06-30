/**
 * @sw-package framework
 */

type ServiceObject = {
    get: <SN extends keyof ServiceContainer>(serviceName: SN) => ServiceContainer[SN];
    list: () => (keyof ServiceContainer)[];
    register: typeof HeyPanel.Application.addServiceProvider;
    registerMiddleware: typeof HeyPanel.Application.addServiceProviderMiddleware;
    registerDecorator: typeof HeyPanel.Application.addServiceProviderDecorator;
};

/**
 * Return the ServiceObject (HeyPanel.Service().myService)
 * or direct access the services (HeyPanel.Service('myService')
 */
function serviceAccessor<SN extends keyof ServiceContainer>(serviceName: SN): ServiceContainer[SN];
function serviceAccessor(): ServiceObject;
function serviceAccessor<SN extends keyof ServiceContainer>(serviceName?: SN): ServiceContainer[SN] | ServiceObject {
    if (serviceName) {
        // eslint-disable-next-line @typescript-eslint/no-unsafe-return
        return HeyPanel.Application.getContainer('service')[serviceName];
    }

    const serviceObject: ServiceObject = {
        // eslint-disable-next-line @typescript-eslint/no-unsafe-return
        get: (name) => HeyPanel.Application.getContainer('service')[name],
        list: () => HeyPanel.Application.getContainer('service').$list(),
        register: (name, service) => HeyPanel.Application.addServiceProvider(name, service),
        registerMiddleware: (...args) => HeyPanel.Application.addServiceProviderMiddleware(...args),
        registerDecorator: (...args) => HeyPanel.Application.addServiceProviderDecorator(...args),
    };

    return serviceObject;
}

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default serviceAccessor;
