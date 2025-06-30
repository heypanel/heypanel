<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Store\Services;

use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Plugin\PluginCollection;
use HeyPanel\Core\Framework\Plugin\PluginEntity;
use HeyPanel\Core\Framework\Store\Authentication\LocaleProvider;
use HeyPanel\Core\Framework\Store\Struct\BinaryCollection;
use HeyPanel\Core\Framework\Store\Struct\ExtensionCollection;
use HeyPanel\Core\Framework\Store\Struct\ExtensionStruct;
use HeyPanel\Core\Framework\Store\Struct\FaqCollection;
use HeyPanel\Core\Framework\Store\Struct\ImageCollection;
use HeyPanel\Core\Framework\Store\Struct\StoreCategoryCollection;
use HeyPanel\Core\Framework\Store\Struct\StoreCollection;
use HeyPanel\Core\Framework\Store\Struct\VariantCollection;
use HeyPanel\Core\System\Locale\LanguageLocaleCodeProvider;
use HeyPanel\Core\System\SystemConfig\Service\ConfigurationService;
use Symfony\Component\Intl\Languages;
use Symfony\Component\Intl\Locales;

/**
 * @internal
 */
class ExtensionLoader
{
    private const DEFAULT_LOCALE = 'zh_CN';

    public function __construct(
        private readonly ConfigurationService $configurationService,
        private readonly LocaleProvider $localeProvider,
        private readonly LanguageLocaleCodeProvider $languageLocaleProvider
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function loadFromArray(Context $context, array $data, ?string $locale = null): ExtensionStruct
    {
        if ($locale === null) {
            $locale = $this->localeProvider->getLocaleFromContext($context);
        }

        $localeWithUnderscore = str_replace('-', '_', $locale);
        $data = $this->prepareArrayData($data, $localeWithUnderscore);

        return ExtensionStruct::fromArray($data);
    }

    /**
     * @param array<array<string, mixed>> $data
     */
    public function loadFromListingArray(Context $context, array $data): ExtensionCollection
    {
        $locale = $this->localeProvider->getLocaleFromContext($context);
        $localeWithUnderscore = str_replace('-', '_', $locale);
        $extensions = new ExtensionCollection();

        foreach ($data as $extension) {
            $extension = ExtensionStruct::fromArray($this->prepareArrayData($extension, $localeWithUnderscore));
            $extensions->set($extension->getName(), $extension);
        }

        return $extensions;
    }

    public function loadFromPluginCollection(Context $context, PluginCollection $collection): ExtensionCollection
    {
        $extensions = new ExtensionCollection();

        foreach ($collection as $app) {
            $plugin = $this->loadFromPlugin($context, $app);
            $extensions->set($plugin->getName(), $plugin);
        }

        return $extensions;
    }

    /**
     * @param array<string> $languageIds
     *
     * @return array<string>
     */
    public function getLocalesCodesFromLanguageIds(array $languageIds): array
    {
        $codes = array_values($this->languageLocaleProvider->getLocalesForLanguageIds($languageIds));
        sort($codes);

        return array_map(static fn (string $locale): string => str_replace('-', '_', $locale), $codes);
    }

    private function loadFromPlugin(Context $context, PluginEntity $plugin): ExtensionStruct
    {
        $data = [
            'localId' => $plugin->getId(),
            'description' => $plugin->getTranslation('description'),
            'name' => $plugin->getName(),
            'label' => $plugin->getTranslation('label'),
            'producerName' => $plugin->getAuthor(),
            'license' => $plugin->getLicense(),
            'version' => $plugin->getVersion(),
            'latestVersion' => $plugin->getUpgradeVersion(),
            'iconRaw' => $plugin->getIcon(),
            'installedAt' => $plugin->getInstalledAt(),
            'active' => $plugin->getActive(),
            'type' => ExtensionStruct::EXTENSION_TYPE_PLUGIN,
            'configurable' => $this->configurationService->checkConfiguration(\sprintf('%s.config', $plugin->getName()), $context),
            'updatedAt' => $plugin->getUpgradedAt(),
            'allowDisable' => true,
            'allowUpdate' => !$plugin->getManagedByComposer() || $plugin->isLocatedInCustomDirectory(),
            'managedByComposer' => $plugin->getManagedByComposer(),
        ];

        return ExtensionStruct::fromArray($this->replaceCollections($data));
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, StoreCollection|mixed|null>
     */
    private function prepareArrayData(array $data, ?string $locale): array
    {
        return $this->translateExtensionLanguages($this->replaceCollections($data), $locale);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, StoreCollection|mixed|null>
     */
    private function replaceCollections(array $data): array
    {
        $replacements = [
            'variants' => VariantCollection::class,
            'faq' => FaqCollection::class,
            'binaries' => BinaryCollection::class,
            'images' => ImageCollection::class,
            'categories' => StoreCategoryCollection::class,
        ];

        foreach ($replacements as $key => $collectionClass) {
            $data[$key] = new $collectionClass($data[$key] ?? []);
        }

        return $data;
    }

    /**
     * @param array<string, StoreCollection|mixed|null> $data
     *
     * @return array<string, StoreCollection|mixed|null>
     */
    private function translateExtensionLanguages(array $data, ?string $locale = self::DEFAULT_LOCALE): array
    {
        if (!isset($data['languages'])) {
            return $data;
        }

        $locale = $locale && Locales::exists($locale) ? $locale : self::DEFAULT_LOCALE;

        foreach ($data['languages'] as $key => $language) {
            $data['languages'][$key] = Languages::getName($language['name'], $locale);
        }

        return $data;
    }
}
