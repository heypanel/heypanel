/**
 * @sw-package discovery
 */
const defaultPageTypes = [
    {
        name: 'page',
        icon: 'regular-lightbulb',
    },
    {
        name: 'landingpage',
        icon: 'regular-dashboard',
    },
    {
        name: 'question_list',
        icon: 'regular-shopping-basket',
    },
    {
        name: 'question_detail',
        icon: 'regular-tag',
    },
];

/**
 * @private
 */
export default () => {
    const pageTypeService = HeyPanel.Service().get('cmsPageTypeService');

    defaultPageTypes.forEach((type: { name: string; icon: string }) => {
        pageTypeService.register(type);
    });
};
