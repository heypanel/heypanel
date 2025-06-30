/**
 * @sw-package framework
 */
import createAppMixin from 'src/app/init/mixin.init';

describe('src/app/init/mixin.init.js', () => {
    it('should register all app mixins', () => {
        createAppMixin();

        expect(HeyPanel.Mixin.getByName('sw-form-field')).toBeDefined();
        expect(HeyPanel.Mixin.getByName('generic-condition')).toBeDefined();
        expect(HeyPanel.Mixin.getByName('listing')).toBeDefined();
        expect(HeyPanel.Mixin.getByName('notification')).toBeDefined();
        expect(HeyPanel.Mixin.getByName('placeholder')).toBeDefined();
        expect(HeyPanel.Mixin.getByName('position')).toBeDefined();
        expect(HeyPanel.Mixin.getByName('remove-api-error')).toBeDefined();
        expect(HeyPanel.Mixin.getByName('ruleContainer')).toBeDefined();
        expect(HeyPanel.Mixin.getByName('salutation')).toBeDefined();
        expect(HeyPanel.Mixin.getByName('sw-inline-snippet')).toBeDefined();
        expect(HeyPanel.Mixin.getByName('user-settings')).toBeDefined();
        expect(HeyPanel.Mixin.getByName('validation')).toBeDefined();
    });
});
