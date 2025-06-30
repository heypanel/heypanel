/**
 * @sw-package framework
 */

/** Initializer */
import initializers from 'src/app/init/';
import preInitializer from 'src/app/init-pre/';
import postInitializer from 'src/app/init-post/';

/** View Adapter */
import VueAdapter from 'src/app/adapter/view/vue.adapter';

/** Services */
import FeatureService from 'src/app/service/feature.service';
import MenuService from 'src/app/service/menu.service';
import PrivilegesService from 'src/app/service/privileges.service';
import AclService from 'src/app/service/acl.service';
import LoginService from 'src/core/service/login.service';
import EntityMappingService from 'src/core/service/entity-mapping.service';
import JsonApiParser from 'src/core/service/jsonapi-parser.service';
import ValidationService from 'src/core/service/validation.service';
import TimezoneService from 'src/core/service/timezone.service';
import RuleConditionService from 'src/app/service/rule-condition.service';
import ProductStreamConditionService from 'src/app/service/question-stream-condition.service';
import StateStyleService from 'src/app/service/state-style.service';
import CustomFieldService from 'src/app/service/custom-field.service';
import ExtensionHelperService from 'src/app/service/extension-helper.service';
import LanguageAutoFetchingService from 'src/app/service/language-auto-fetching.service';
import SearchTypeService from 'src/app/service/search-type.service';
import LicenseViolationsService from 'src/app/service/license-violations.service';
import ShortcutService from 'src/app/service/shortcut.service';
import LocaleToLanguageService from 'src/app/service/locale-to-language.service';
import addPluginUpdatesListener from 'src/core/service/plugin-updates-listener.service';
import addHeyPanelUpdatesListener from 'src/core/service/heypanel-updates-listener.service';
import addCustomerGroupRegistrationListener from 'src/core/service/member-group-registration-listener.service';
import LocaleHelperService from 'src/app/service/locale-helper.service';
import FilterService from 'src/app/service/filter.service';
import MediaDefaultFolderService from 'src/app/service/media-default-folder.service';
import HeyPanelDiscountCampaignService from 'src/app/service/discount-campaign.service';
import SearchRankingService from 'src/app/service/search-ranking.service';
import SearchPreferencesService from 'src/app/service/search-preferences.service';
import RecentlySearchService from 'src/app/service/recently-search.service';
import UserActivityService from 'src/app/service/user-activity.service';
import EntityValidationService from 'src/app/service/entity-validation.service';
import CustomEntityDefinitionService from 'src/app/service/custom-entity-definition.service';
import addUsageDataConsentListener from 'src/core/service/usage-data-consent-listener.service';
import FileValidationService from 'src/app/service/file-validation.service';

/** Import Feature */
import Feature from 'src/core/feature';

/** Import decorators */
import 'src/app/decorator';

/** Import Meteor Component Library styles */
// eslint-disable-next-line import/no-unresolved
import '@heypanel-ag/meteor-component-library/styles.css';
// eslint-disable-next-line import/no-unresolved
import '@heypanel-ag/meteor-component-library/font.css';

import ChangesetGenerator from '../core/data/changeset-generator.data';
import ErrorResolver from '../core/data/error-resolver.data';

/** Application Bootstrapper */
const { Application } = HeyPanel;

const factoryContainer = Application.getContainer('factory');

/** Create View Adapter */
const adapter = new VueAdapter(Application);

Application.setViewAdapter(adapter);

// Add pre-initializers to application
Object.keys(preInitializer).forEach((key) => {
    // @ts-expect-error
    // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
    const initializer = preInitializer[key];
    // @ts-expect-error
    // eslint-disable-next-line @typescript-eslint/no-unsafe-argument
    Application.addInitializer(key, initializer, '-pre');
});

// Add initializers to application
Object.keys(initializers).forEach((key) => {
    // @ts-expect-error
    // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
    const initializer = initializers[key];
    // @ts-expect-error
    // eslint-disable-next-line @typescript-eslint/no-unsafe-argument
    Application.addInitializer(key, initializer);
});

// Add post-initializers to application
Object.keys(postInitializer).forEach((key) => {
    // @ts-expect-error
    // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
    const initializer = postInitializer[key];

    // @ts-expect-error
    // eslint-disable-next-line @typescript-eslint/no-unsafe-argument
    Application.addInitializer(key, initializer, '-post');
});

// Add service providers
Application.addServiceProvider('feature', () => {
    return new FeatureService(Feature);
})
    .addServiceProvider('customEntityDefinitionService', () => {
        return new CustomEntityDefinitionService();
    })
    .addServiceProvider('menuService', () => {
        return new MenuService(factoryContainer.module);
    })
    .addServiceProvider('privileges', () => {
        return new PrivilegesService();
    })
    .addServiceProvider('acl', () => {
        return new AclService();
    })
    .addServiceProvider('loginService', () => {
        const serviceContainer = Application.getContainer('service');
        const initContainer = Application.getContainer('init');

        const loginService = LoginService(initContainer.httpClient, HeyPanel.Context.api);

        addPluginUpdatesListener(loginService, serviceContainer);
        addHeyPanelUpdatesListener(loginService, serviceContainer);
        addCustomerGroupRegistrationListener(loginService);
        addUsageDataConsentListener(loginService, serviceContainer);

        return loginService;
    })
    .addServiceProvider('jsonApiParserService', () => {
        return JsonApiParser;
    })
    .addServiceProvider('validationService', () => {
        return ValidationService;
    })
    .addServiceProvider('entityValidationService', () => {
        return new EntityValidationService(
            // eslint-disable-next-line @typescript-eslint/no-unsafe-argument
            Application.getContainer('factory').entityDefinition,
            new ChangesetGenerator(),
            new ErrorResolver(),
        );
    })
    .addServiceProvider('timezoneService', () => {
        return new TimezoneService();
    })
    .addServiceProvider('ruleConditionDataProviderService', () => {
        return new RuleConditionService();
    })
    .addServiceProvider('questionStreamConditionService', () => {
        return new ProductStreamConditionService();
    })
    .addServiceProvider('customFieldDataProviderService', () => {
        return new CustomFieldService();
    })
    .addServiceProvider('extensionHelperService', () => {
        return new ExtensionHelperService({
            // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
            extensionStoreActionService: HeyPanel.Service('extensionStoreActionService'),
        });
    })
    .addServiceProvider('languageAutoFetchingService', () => {
        return LanguageAutoFetchingService();
    })
    .addServiceProvider('stateStyleDataProviderService', () => {
        return new StateStyleService();
    })
    .addServiceProvider('searchTypeService', () => {
        return new SearchTypeService();
    })
    .addServiceProvider('localeToLanguageService', () => {
        return LocaleToLanguageService();
    })
    .addServiceProvider('entityMappingService', () => {
        return EntityMappingService;
    })
    .addServiceProvider('shortcutService', () => {
        // eslint-disable-next-line @typescript-eslint/no-unsafe-argument
        return new ShortcutService(factoryContainer.shortcut);
    })
    .addServiceProvider('licenseViolationService', () => {
        return LicenseViolationsService(Application.getContainer('service').storeService);
    })
    .addServiceProvider('localeHelper', () => {
        return new LocaleHelperService({
            HeyPanel: HeyPanel,
            localeRepository: HeyPanel.Service('repositoryFactory').create('locale'),
            // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
            snippetService: HeyPanel.Service('snippetService'),
            // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
            localeFactory: Application.getContainer('factory').locale,
        });
    })
    .addServiceProvider('filterService', () => {
        return new FilterService({
            userConfigRepository: HeyPanel.Service('repositoryFactory').create('user_config'),
        });
    })
    .addServiceProvider('mediaDefaultFolderService', () => {
        return MediaDefaultFolderService();
    })
    .addServiceProvider('heypanelDiscountCampaignService', () => {
        return new HeyPanelDiscountCampaignService();
    })
    .addServiceProvider('searchRankingService', () => {
        return new SearchRankingService();
    })
    .addServiceProvider('recentlySearchService', () => {
        return new RecentlySearchService();
    })
    .addServiceProvider('searchPreferencesService', () => {
        return new SearchPreferencesService({
            userConfigRepository: HeyPanel.Service('repositoryFactory').create('user_config'),
        });
    })
    .addServiceProvider('userActivityService', () => {
        return new UserActivityService();
    })
    .addServiceProvider('fileValidationService', () => {
        return FileValidationService();
    });
