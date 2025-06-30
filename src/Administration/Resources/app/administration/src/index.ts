/**
 * @sw-package framework
 */
import './app/assets/scss/all.scss';

// Import the HeyPanel instance
void import('src/core/heypanel').then(async ({ HeyPanelInstance }) => {
    // Set the global HeyPanel instance
    window.HeyPanel = HeyPanelInstance;

    if (window._swLoginOverrides) {
        window._swLoginOverrides.forEach((script) => {
            script();
        });
    }

    // Import the main file
    await import('src/app/main');

    // Start the main application and fingers crossed
    // that everything works as expected
    window.startApplication();
});
