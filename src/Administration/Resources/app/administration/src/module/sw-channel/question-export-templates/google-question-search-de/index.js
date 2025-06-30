/**
 * @sw-package discovery
 */

import header from './header.xml.twig';
import body from './body.xml.twig';
import footer from './footer.xml.twig';

HeyPanel.Service('exportTemplateService').registerProductExportTemplate({
    name: 'google-question-search-de',
    translationKey: 'sw-channel.detail.questionComparison.templates.template-label.google-question-search-de',
    headerTemplate: header.trim(),
    bodyTemplate: body,
    footerTemplate: footer.trim(),
    fileName: 'google.xml',
    encoding: 'UTF-8',
    fileFormat: 'xml',
    generateByCronjob: false,
    interval: 86400,
});
