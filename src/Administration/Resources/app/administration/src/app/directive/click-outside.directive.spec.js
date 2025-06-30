/**
 * @sw-package framework
 */
describe('directives/click-outside', () => {
    it('should register the directive', () => {
        expect(HeyPanel.Directive.getByName('click-outside')).toBeDefined();
    });
});
