import ApiService from '../api.service';

/**
 * @sw-package checkout
 * Gateway for the API end point "member-group-registration"
 * @class
 * @extends ApiService
 */
class CustomerGroupRegistrationApiService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = 'member-group-registration') {
        super(httpClient, loginService, apiEndpoint);
        this.name = 'memberGroupRegistrationService';
    }

    accept(memberId, additionalParams = {}, additionalHeaders = {}, additionalRequest = {}) {
        const route = `/_action/${this.getApiBasePath()}/accept`;
        return this.httpClient
            .post(
                route,
                {
                    memberIds: Array.isArray(memberId) ? memberId : [memberId],
                    ...additionalRequest,
                },
                {
                    params: additionalParams,
                    headers: this.getBasicHeaders(additionalHeaders),
                },
            )
            .then((response) => {
                return ApiService.handleResponse(response);
            });
    }

    decline(memberId, additionalParams = {}, additionalHeaders = {}, additionalRequest = {}) {
        const route = `/_action/${this.getApiBasePath()}/decline`;
        return this.httpClient
            .post(
                route,
                {
                    memberIds: Array.isArray(memberId) ? memberId : [memberId],
                    ...additionalRequest,
                },
                {
                    params: additionalParams,
                    headers: this.getBasicHeaders(additionalHeaders),
                },
            )
            .then((response) => {
                return ApiService.handleResponse(response);
            });
    }
}

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default CustomerGroupRegistrationApiService;
