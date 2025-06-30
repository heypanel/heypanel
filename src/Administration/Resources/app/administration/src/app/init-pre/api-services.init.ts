/**
 * @sw-package framework
 */

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default async function initializeApiServices() {
    // Add custom api service providers
    // eslint-disable-next-line no-restricted-syntax
    for (const ApiServicePromise of HeyPanel._private.ApiServices()) {
        // eslint-disable-next-line no-await-in-loop,@typescript-eslint/no-unsafe-assignment
        const ApiServiceRaw = await ApiServicePromise();

        // @ts-expect-error
        // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
        const ApiService = ApiServiceRaw.default;

        const factoryContainer = HeyPanel.Application.getContainer('factory');
        const initContainer = HeyPanel.Application.getContainer('init');

        const apiServiceFactory = factoryContainer.apiService;
        // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-assignment
        const service = new ApiService(initContainer.httpClient, HeyPanel.Service('loginService'));
        // eslint-disable-next-line @typescript-eslint/no-unsafe-member-access
        const serviceName = service.name as keyof ServiceContainer;
        // eslint-disable-next-line @typescript-eslint/no-unsafe-argument
        apiServiceFactory.register(serviceName, service);

        HeyPanel.Application.addServiceProvider(serviceName, () => {
            // eslint-disable-next-line @typescript-eslint/no-unsafe-return
            return service;
        });
    }
}
