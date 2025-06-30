<?php declare(strict_types=1);

namespace HeyPanel\Core\Migration\V6_7;

use Doctrine\DBAL\Connection;
use HeyPanel\Core\Content\Category\CategoryDefinition;
use HeyPanel\Core\Defaults;
use HeyPanel\Core\Framework\Api\Util\AccessKeyHelper;
use HeyPanel\Core\Framework\DataAbstractionLayer\Doctrine\MultiInsertQueryQueue;
use HeyPanel\Core\Framework\Migration\MigrationStep;
use HeyPanel\Core\Framework\Uuid\Uuid;

/**
 * @internal
 *
 * @codeCoverageIgnore
 */
class Migration1536233560BasicData extends MigrationStep
{
    private ?string $enLanguageId = null;

    public function getCreationTimestamp(): int
    {
        return 1536233560;
    }

    public function update(Connection $connection): void
    {
        $hasData = $connection->executeQuery('SELECT 1 FROM `language` LIMIT 1')->fetchAssociative();
        if ($hasData) {
            return;
        }
        $this->createLanguage($connection);
        $this->createCountry($connection);
        $this->createCurrency($connection);
        $this->createCustomerGroup($connection);
        $this->createRootCategory($connection);
        $this->createChannelTypes($connection);
        $this->createChannel($connection);
        $this->createDefaultSnippetSets($connection);
        $this->createDefaultMediaFolders($connection);
        $this->createNumberRanges($connection);
        $this->createSystemConfigOptions($connection);
    }

    private function createRootCategory(Connection $connection): void
    {
        $id = Uuid::randomBytes();
        $languageZH = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $languageEN = Uuid::fromHexToBytes($this->getEnLanguageId());
        $versionId = Uuid::fromHexToBytes(Defaults::LIVE_VERSION);

        $connection->insert('category', ['id' => $id, 'version_id' => $versionId, 'type' => CategoryDefinition::TYPE_PAGE, 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('category_translation', ['category_id' => $id, 'category_version_id' => $versionId, 'language_id' => $languageEN, 'name' => 'HeyPanel Full-Stack Framework Demo ', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('category_translation', ['category_id' => $id, 'category_version_id' => $versionId, 'language_id' => $languageZH, 'name' => 'HeyPanel 全栈开发框架演示站', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
    }

    private function createSystemConfigOptions(Connection $connection): void
    {
        $connection->insert('system_config', [
            'id' => Uuid::randomBytes(),
            'configuration_key' => 'core.store.apiUri',
            'configuration_value' => '{"_value": "https://api.heypanel.net"}',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        $connection->insert('system_config', [
            'id' => Uuid::randomBytes(),
            'configuration_key' => 'core.register.minPasswordLength',
            'configuration_value' => '{"_value": 8}',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        $query = 'INSERT IGNORE INTO system_config SET
                    id = :id,
                    configuration_value = :configValue,
                    configuration_key = :configKey,
                    created_at = :createdAt;';

        $connection->executeStatement($query, [
            'id' => Uuid::randomBytes(),
            'configKey' => 'core.sitemap.sitemapRefreshTime',
            'configValue' => '{"_value": 3600}',
            'createdAt' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        $connection->executeStatement($query, [
            'id' => Uuid::randomBytes(),
            'configKey' => 'core.sitemap.sitemapRefreshStrategy',
            'configValue' => '{"_value": "2"}',
            'createdAt' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);
    }

    private function createNumberRanges(Connection $connection): void
    {
        $definitionNumberRangeTypes = [
            'product' => [
                'id' => Uuid::randomHex(),
                'global' => 1,
                'nameZh' => '会员',
                'nameEn' => 'Customer',
            ],
        ];

        $definitionNumberRanges = [
            'product' => [
                'id' => Uuid::randomHex(),
                'name' => '会员',
                'nameEn' => 'Customers',
                'global' => 1,
                'typeId' => $definitionNumberRangeTypes['product']['id'],
                'pattern' => 'M{n}',
                'start' => 10000,
            ],
        ];

        $languageZh = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $languageEn = Uuid::fromHexToBytes($this->getEnLanguageId());

        foreach ($definitionNumberRangeTypes as $typeName => $numberRangeType) {
            $connection->insert(
                'number_range_type',
                [
                    'id' => Uuid::fromHexToBytes($numberRangeType['id']),
                    'global' => $numberRangeType['global'],
                    'technical_name' => $typeName,
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]
            );
            $connection->insert(
                'number_range_type_translation',
                [
                    'number_range_type_id' => Uuid::fromHexToBytes($numberRangeType['id']),
                    'type_name' => $numberRangeType['nameEn'],
                    'language_id' => $languageEn,
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]
            );
            $connection->insert(
                'number_range_type_translation',
                [
                    'number_range_type_id' => Uuid::fromHexToBytes($numberRangeType['id']),
                    'type_name' => $numberRangeType['nameZh'],
                    'language_id' => $languageZh,
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]
            );
        }

        foreach ($definitionNumberRanges as $numberRange) {
            $connection->insert(
                'number_range',
                [
                    'id' => Uuid::fromHexToBytes($numberRange['id']),
                    'global' => $numberRange['global'],
                    'type_id' => Uuid::fromHexToBytes($numberRange['typeId']),
                    'pattern' => $numberRange['pattern'],
                    'start' => $numberRange['start'],
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]
            );
            $connection->insert(
                'number_range_translation',
                [
                    'number_range_id' => Uuid::fromHexToBytes($numberRange['id']),
                    'name' => $numberRange['name'],
                    'language_id' => $languageZh,
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]
            );
            $connection->insert(
                'number_range_translation',
                [
                    'number_range_id' => Uuid::fromHexToBytes($numberRange['id']),
                    'name' => $numberRange['nameEn'],
                    'language_id' => $languageEn,
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]
            );
        }
    }

    private function createDefaultMediaFolders(Connection $connection): void
    {
        $queue = new MultiInsertQueryQueue($connection);

        $queue->addInsert('media_default_folder', ['id' => Uuid::randomBytes(), 'entity' => 'customer', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $queue->addInsert('media_default_folder', ['id' => Uuid::randomBytes(), 'entity' => 'user', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $queue->addInsert('media_default_folder', ['id' => Uuid::randomBytes(), 'entity' => 'post', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $queue->addInsert('media_default_folder', ['id' => Uuid::randomBytes(), 'entity' => 'cms_page', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $queue->addInsert('media_default_folder', ['id' => Uuid::randomBytes(), 'entity' => 'category', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $queue->execute();

        $notCreatedDefaultFolders = $connection->executeQuery('
            SELECT `media_default_folder`.`id` default_folder_id, `media_default_folder`.`entity` entity
            FROM `media_default_folder`
                LEFT JOIN `media_folder` ON `media_folder`.`default_folder_id` = `media_default_folder`.`id`
            WHERE `media_folder`.`id` IS NULL
        ')->fetchAllAssociative();

        foreach ($notCreatedDefaultFolders as $notCreatedDefaultFolder) {
            $this->createDefaultFolder(
                $connection,
                $notCreatedDefaultFolder['default_folder_id'],
                $notCreatedDefaultFolder['entity']
            );
        }
    }

    private function createDefaultFolder(Connection $connection, string $defaultFolderId, string $entity): void
    {
        $connection->transactional(function (Connection $connection) use ($defaultFolderId, $entity): void {
            $configurationId = Uuid::randomBytes();
            $folderId = Uuid::randomBytes();
            $folderName = $this->getMediaFolderName($entity);
            $private = 0;

            $connection->executeStatement('
                INSERT INTO `media_folder_configuration` (`id`, `thumbnail_quality`, `create_thumbnails`, `private`, created_at)
                VALUES (:id, 80, 1, :private, :createdAt)
            ', [
                'id' => $configurationId,
                'createdAt' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'private' => $private,
            ]);

            $connection->executeStatement('
                INSERT into `media_folder` (`id`, `name`, `default_folder_id`, `media_folder_configuration_id`, `use_parent_configuration`, `child_count`, `created_at`)
                VALUES (:folderId, :folderName, :defaultFolderId, :configurationId, 0, 0, :createdAt)
            ', [
                'folderId' => $folderId,
                'folderName' => $folderName,
                'defaultFolderId' => $defaultFolderId,
                'configurationId' => $configurationId,
                'createdAt' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]);
        });
    }

    private function getMediaFolderName(string $entity): string
    {
        $capitalizedEntityParts = array_map(
            static fn ($part) => ucfirst((string) $part),
            explode('_', $entity)
        );

        return implode(' ', $capitalizedEntityParts) . ' Media';
    }

    private function createDefaultSnippetSets(Connection $connection): void
    {
        $queue = new MultiInsertQueryQueue($connection);

        $queue->addInsert('snippet_set', ['id' => Uuid::randomBytes(), 'name' => 'BASE zh-CN', 'base_file' => 'messages.zh-CN', 'iso' => 'zh-CN', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $queue->addInsert('snippet_set', ['id' => Uuid::randomBytes(), 'name' => 'BASE en-GB', 'base_file' => 'messages.en-GB', 'iso' => 'en-GB', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);

        $queue->execute();
    }

    private function createChannel(Connection $connection): void
    {
        $currencies = $connection->executeQuery('SELECT id FROM currency')->fetchFirstColumn();
        $languages = $connection->executeQuery('SELECT id FROM language')->fetchFirstColumn();
        $countryStatement = $connection->executeQuery('SELECT id FROM country WHERE active = 1 ORDER BY `position`');
        $defaultCountry = $countryStatement->fetchOne();
        $rootCategoryId = $connection->executeQuery('SELECT id FROM category')->fetchOne();

        $id = Uuid::fromHexToBytes('98432def39fc4624b33213a56b8c944d');
        $languageZH = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $languageEN = Uuid::fromHexToBytes($this->getEnLanguageId());

        $connection->insert('channel', [
            'id' => $id,
            'type_id' => Uuid::fromHexToBytes(Defaults::CHANNEL_TYPE_API),
            'access_key' => AccessKeyHelper::generateAccessKey('channel'),
            'active' => 1,
            'language_id' => Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM),
            'currency_id' => Uuid::fromHexToBytes(Defaults::CURRENCY),
            'country_id' => $defaultCountry,
            'navigation_category_id' => $rootCategoryId,
            'navigation_category_version_id' => Uuid::fromHexToBytes(Defaults::LIVE_VERSION),
            'customer_group_id' => Uuid::fromHexToBytes('cfbd5018d38d41d8adca10d94fc8bdd6'),
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        $connection->insert('channel_translation', ['channel_id' => $id, 'language_id' => $languageEN, 'name' => 'Headless', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('channel_translation', ['channel_id' => $id, 'language_id' => $languageZH, 'name' => 'Headless', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);

        // country
        $connection->insert('channel_country', ['channel_id' => $id, 'country_id' => $defaultCountry]);

        // currency
        foreach ($currencies as $currency) {
            $connection->insert('channel_currency', ['channel_id' => $id, 'currency_id' => $currency]);
        }

        // language
        foreach ($languages as $language) {
            $connection->insert('channel_language', ['channel_id' => $id, 'language_id' => $language]);
        }
    }

    private function createCurrency(Connection $connection): void
    {
        $CNY = Uuid::fromHexToBytes(Defaults::CURRENCY);
        $USD = Uuid::randomBytes();
        $EUR = Uuid::randomBytes();
        $GBP = Uuid::randomBytes();

        $languageZH = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $languageEN = Uuid::fromHexToBytes($this->getEnLanguageId());

        $connection->insert('currency', ['id' => $CNY, 'iso_code' => 'CNY', 'factor' => 1, 'symbol' => '¥', 'position' => 1, 'decimal_precision' => 2, 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('currency_translation', ['currency_id' => $CNY, 'language_id' => $languageEN, 'short_name' => 'CNY', 'name' => 'CNY', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('currency_translation', ['currency_id' => $CNY, 'language_id' => $languageZH, 'short_name' => 'CNY', 'name' => '人民币', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);

        $connection->insert('currency', ['id' => $USD, 'iso_code' => 'USD', 'factor' => 0.1372, 'symbol' => '$', 'position' => 1, 'decimal_precision' => 2, 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('currency_translation', ['currency_id' => $USD, 'language_id' => $languageEN, 'short_name' => 'USD', 'name' => 'US-Dollar', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('currency_translation', ['currency_id' => $USD, 'language_id' => $languageZH, 'short_name' => 'USD', 'name' => '美元', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);

        $connection->insert('currency', ['id' => $EUR, 'iso_code' => 'EUR', 'factor' => 0.12, 'symbol' => '€', 'position' => 1, 'decimal_precision' => 2, 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('currency_translation', ['currency_id' => $EUR, 'language_id' => $languageEN, 'short_name' => 'EUR', 'name' => 'Euro', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('currency_translation', ['currency_id' => $EUR, 'language_id' => $languageZH, 'short_name' => 'EUR', 'name' => '欧元', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);

        $connection->insert('currency', ['id' => $GBP, 'iso_code' => 'GBP', 'factor' => 0.1, 'symbol' => '£', 'position' => 1, 'decimal_precision' => 2, 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('currency_translation', ['currency_id' => $GBP, 'language_id' => $languageEN, 'short_name' => 'GBP', 'name' => 'Pound', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('currency_translation', ['currency_id' => $GBP, 'language_id' => $languageZH, 'short_name' => 'GBP', 'name' => '英镑', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
    }

    private function createChannelTypes(Connection $connection): void
    {
        $languageZH = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $languageEN = Uuid::fromHexToBytes($this->getEnLanguageId());

        $html = Uuid::fromHexToBytes(Defaults::CHANNEL_TYPE_FRONTEND);
        $api = Uuid::fromHexToBytes(Defaults::CHANNEL_TYPE_API);

        $connection->insert('channel_type', ['id' => $html, 'icon_name' => 'regular-storefront', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('channel_type_translation', ['channel_type_id' => $html, 'language_id' => $languageEN, 'name' => 'Web', 'manufacturer' => 'HeyPanel AG', 'description' => 'Channel with HTML', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('channel_type_translation', ['channel_type_id' => $html, 'language_id' => $languageZH, 'name' => 'Web', 'manufacturer' => 'HeyPanel AG', 'description' => 'Channel mit HTML', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);

        $connection->insert('channel_type', ['id' => $api, 'icon_name' => 'regular-rocket', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('channel_type_translation', ['channel_type_id' => $api, 'language_id' => $languageEN, 'name' => 'Headless', 'manufacturer' => 'HeyPanel AG', 'description' => 'API only channel', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('channel_type_translation', ['channel_type_id' => $api, 'language_id' => $languageZH, 'name' => 'Headless', 'manufacturer' => 'HeyPanel AG', 'description' => 'API only channel', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
    }

    private function createCustomerGroup(Connection $connection): void
    {
        $connection->insert('customer_group', ['id' => Uuid::fromHexToBytes('cfbd5018d38d41d8adca10d94fc8bdd6'), 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('customer_group_translation', ['customer_group_id' => Uuid::fromHexToBytes('cfbd5018d38d41d8adca10d94fc8bdd6'), 'language_id' => Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM), 'name' => '普通会员组', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('customer_group_translation', ['customer_group_id' => Uuid::fromHexToBytes('cfbd5018d38d41d8adca10d94fc8bdd6'), 'language_id' => Uuid::fromHexToBytes($this->getEnLanguageId()), 'name' => 'Standard customer group', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
    }

    private function createCountry(Connection $connection): void
    {
        $languageEN = fn (string $countryId, string $name) => [
            'language_id' => Uuid::fromHexToBytes($this->getEnLanguageId()),
            'name' => $name,
            'country_id' => $countryId,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ];

        $languageZH = static fn (string $countryId, string $name) => [
            'language_id' => Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM),
            'name' => $name,
            'country_id' => $countryId,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ];
        $zhId = Uuid::randomBytes();
        $connection->insert('country', ['id' => $zhId, 'iso' => 'CN', 'position' => 1, 'iso3' => 'CHN', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('country_translation', $languageZH($zhId, '中国'));
        $connection->insert('country_translation', $languageEN($zhId, 'China'));
        $this->createCountryStates($connection, $zhId, 'CN');

        $gbId = Uuid::randomBytes();
        $connection->insert('country', ['id' => $gbId, 'iso' => 'GB', 'position' => 5, 'iso3' => 'GBR', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('country_translation', $languageEN($gbId, 'Great Britain'));
        $connection->insert('country_translation', $languageZH($gbId, '英国'));
        $this->createCountryStates($connection, $gbId, 'GB');

        $deId = Uuid::randomBytes();
        $connection->insert('country', ['id' => $deId, 'iso' => 'DE', 'position' => 1, 'iso3' => 'DEU', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('country_translation', $languageZH($deId, '德国'));
        $connection->insert('country_translation', $languageEN($deId, 'Germany'));

        $this->createCountryStates($connection, $deId, 'DE');

        $usId = Uuid::randomBytes();
        $connection->insert('country', ['id' => $usId, 'iso' => 'US', 'position' => 10, 'iso3' => 'USA', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('country_translation', $languageEN($usId, 'USA'));
        $connection->insert('country_translation', $languageZH($usId, '美国'));

        $this->createCountryStates($connection, $usId, 'US');

        $frId = Uuid::randomBytes();
        $connection->insert('country', ['id' => $frId, 'iso' => 'FR', 'position' => 10, 'iso3' => 'FRA', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('country_translation', $languageEN($frId, 'France'));
        $connection->insert('country_translation', $languageZH($frId, '法国'));
    }

    private function createCountryStates(Connection $connection, string $countryId, string $countryCode): void
    {
        $data = [
            'US' => [
                'US-AL' => '亚拉巴马州',
                'US-AK' => '阿拉斯加州',
                'US-AZ' => '亚利桑那州',
                'US-AR' => '阿肯色州',
                'US-CA' => '加利福尼亚州（加州）',
                'US-CO' => '科罗拉多州',
                'US-CT' => '康涅狄格州',
                'US-DE' => '特拉华州',
                'US-FL' => '佛罗里达州',
                'US-GA' => '佐治亚州',
                'US-HI' => '夏威夷州',
                'US-ID' => '爱达荷州',
                'US-IL' => '伊利诺伊州',
                'US-IN' => '印第安纳州',
                'US-IA' => '艾奥瓦州',
                'US-KS' => '堪萨斯州',
                'US-KY' => '肯塔基州',
                'US-LA' => '路易斯安那州',
                'US-ME' => '缅因州',
                'US-MD' => '马里兰州',
                'US-MA' => '马萨诸塞州（麻省）',
                'US-MI' => '密歇根州',
                'US-MN' => '明尼苏达州',
                'US-MS' => '密西西比州',
                'US-MO' => '密苏里州',
                'US-MT' => '蒙大拿州',
                'US-NE' => '内布拉斯加州',
                'US-NV' => '内华达州',
                'US-NH' => '新罕布什尔州',
                'US-NJ' => '新泽西州',
                'US-NM' => '新墨西哥州',
                'US-NY' => '纽约州',
                'US-NC' => '北卡罗来纳州',
                'US-ND' => '北达科他州',
                'US-OH' => '俄亥俄州',
                'US-OK' => '俄克拉何马州',
                'US-OR' => '俄勒冈州',
                'US-PA' => '宾夕法尼亚州（宾州）',
                'US-RI' => '罗得岛州',
                'US-SC' => '南卡罗来纳州',
                'US-SD' => '南达科他州',
                'US-TN' => '田纳西州',
                'US-TX' => '得克萨斯州（德州）',
                'US-UT' => '犹他州',
                'US-VT' => '佛蒙特州',
                'US-VA' => '弗吉尼亚州',
                'US-WA' => '华盛顿州',
                'US-WV' => '西弗吉尼亚州',
                'US-WI' => '威斯康星州',
                'US-WY' => '怀俄明州',
                'US-DC' => '哥伦比亚特区',
            ],
            'DE' => [
                'DE-BW' => '巴登-符腾堡州',
                'DE-BY' => '巴伐利亚州',
                'DE-BE' => '柏林',
                'DE-BB' => '勃兰登堡州',
                'DE-HB' => '不来梅',
                'DE-HH' => '汉堡',
                'DE-HE' => '黑森州',
                'DE-NI' => '下萨克森州',
                'DE-MV' => '梅克伦堡-前波莫瑞州',
                'DE-NW' => '北莱茵-威斯特法伦州',
                'DE-RP' => '莱茵兰-普法尔茨州',
                'DE-SL' => '萨尔州',
                'DE-SN' => '萨克森州',
                'DE-ST' => '萨克森-安哈尔特州',
                'DE-SH' => '石勒苏益格-荷尔斯泰因州',
                'DE-TH' => '图林根州',
            ],
            'GB' => [
                'GB-ENG' => '英格兰',
                'GB-NIR' => '北爱尔兰',
                'GB-SCT' => '苏格兰',
                'GB-WLS' => '威尔士',
                'GB-EAW' => '英格兰和威尔士',
                'GB-GBN' => '大不列颠',
                'GB-UKM' => '联合王国',
                'GB-BKM' => '白金汉郡',
                'GB-CAM' => '剑桥郡',
                'GB-CMA' => '坎布里亚郡',
                'GB-DBY' => '德比郡',
                'GB-DEV' => '德文郡',
                'GB-DOR' => '多塞特郡',
                'GB-ESX' => '东萨塞克斯郡',
                'GB-ESS' => '埃塞克斯郡',
                'GB-GLS' => '格洛斯特郡',
                'GB-HAM' => '汉普郡',
                'GB-HRT' => '赫特福德郡',
                'GB-KEN' => '肯特郡',
                'GB-LAN' => '兰开夏郡',
                'GB-LEC' => '莱斯特郡',
                'GB-LIN' => '林肯郡',
                'GB-NFK' => '诺福克郡',
                'GB-NYK' => '北约克郡',
                'GB-NTH' => '北安普敦郡',
                'GB-NTT' => '诺丁汉郡',
                'GB-OXF' => '牛津郡',
                'GB-SOM' => '萨默塞特郡',
                'GB-STS' => '斯塔福德郡',
                'GB-SFK' => '萨福克郡',
                'GB-SRY' => '萨里郡',
                'GB-WAR' => '沃里克郡',
                'GB-WSX' => '西萨塞克斯郡',
                'GB-WOR' => '伍斯特郡',
                'GB-LND' => '伦敦城',
                'GB-BDG' => '巴金-达格纳姆区',
                'GB-BNE' => '巴尼特区',
                'GB-BEX' => '贝克斯利区',
                'GB-BEN' => '布伦特区',
                'GB-BRY' => '布罗姆利区',
                'GB-CMD' => '卡姆登区',
                'GB-CRY' => '克罗伊登区',
                'GB-EAL' => '伊灵区',
                'GB-ENF' => '恩菲尔德区',
                'GB-GRE' => '格林尼治区',
                'GB-HCK' => '哈克尼区',
                'GB-HMF' => '哈默史密斯-富勒姆区',
                'GB-HRY' => '哈林盖区',
                'GB-HRW' => '哈罗区',
                'GB-HAV' => '黑弗灵区',
                'GB-HIL' => '希灵登区',
                'GB-HNS' => '豪恩斯洛区',
                'GB-ISL' => '伊斯灵顿区',
                'GB-KEC' => '肯辛顿-切尔西区',
                'GB-KTT' => '泰晤士河畔金斯顿区',
                'GB-LBH' => '兰贝斯区',
                'GB-LEW' => '刘易舍姆区',
                'GB-MRT' => '默顿区',
                'GB-NWM' => '纽汉区',
                'GB-RDB' => '雷德布里奇区',
                'GB-RIC' => '泰晤士河畔里士满区',
                'GB-SWK' => '萨瑟克区',
                'GB-STN' => '萨顿区',
                'GB-TWH' => '陶尔哈姆莱茨区',
                'GB-WFT' => '沃尔瑟姆森林区',
                'GB-WND' => '旺兹沃思区',
                'GB-WSM' => '威斯敏斯特市',
                'GB-BNS' => '巴恩斯利',
                'GB-BIR' => '伯明翰',
                'GB-BOL' => '博尔顿',
                'GB-BRD' => '布拉德福德',
                'GB-BUR' => '伯里',
                'GB-CLD' => '考尔德代尔',
                'GB-COV' => '考文垂',
                'GB-DNC' => '唐卡斯特',
                'GB-DUD' => '达德利',
                'GB-GAT' => '盖茨黑德',
                'GB-KIR' => '柯克利斯',
                'GB-KWL' => '诺斯利',
                'GB-LDS' => '利兹',
                'GB-LIV' => '利物浦',
                'GB-MAN' => '曼彻斯特',
                'GB-NET' => '纽卡斯尔',
                'GB-NTY' => '北泰恩赛德',
                'GB-OLD' => '奥尔德姆',
                'GB-RCH' => '罗奇代尔',
                'GB-ROT' => '罗瑟勒姆',
                'GB-SHN' => '圣海伦斯',
                'GB-SLF' => '索尔福德',
                'GB-SAW' => '桑德韦尔',
                'GB-SFT' => '塞夫顿',
                'GB-SHF' => '谢菲尔德',
                'GB-SOL' => '索利哈尔',
                'GB-STY' => '南泰恩赛德',
                'GB-SKP' => '斯托克波特',
                'GB-SND' => '桑德兰',
                'GB-TAM' => '泰姆赛德',
                'GB-TRF' => '特拉福德',
                'GB-WKF' => '韦克菲尔德',
                'GB-WLL' => '沃尔索尔',
                'GB-WGN' => '威根',
                'GB-WRL' => '威勒尔',
                'GB-WLV' => '伍尔弗汉普顿',
                'GB-BAS' => '巴斯和东北萨默塞特',
                'GB-BDF' => '贝德福德',
                'GB-BBD' => '布莱克本-达文',
                'GB-BPL' => '布莱克浦',
                'GB-BMH' => '伯恩茅斯',
                'GB-BRC' => '布拉克内尔森林',
                'GB-BNH' => '布莱顿-霍夫',
                'GB-BST' => '布里斯托尔',
                'GB-CBF' => '中贝德福德郡',
                'GB-CHE' => '柴郡东区',
                'GB-CHW' => '柴郡西区-切斯特',
                'GB-CON' => '康沃尔',
                'GB-DAL' => '达灵顿',
                'GB-DER' => '德比',
                'GB-DUR' => '达勒姆郡',
                'GB-ERY' => '东约克郡',
                'GB-HAL' => '哈尔顿',
                'GB-HPL' => '哈特尔浦',
                'GB-HEF' => '赫里福德郡',
                'GB-IOW' => '怀特岛',
                'GB-IOS' => '锡利群岛',
                'GB-KHL' => '赫尔河畔金斯顿',
                'GB-LCE' => '莱斯特',
                'GB-LUT' => '卢顿',
                'GB-MDW' => '梅德韦',
                'GB-MDB' => '米德尔斯堡',
                'GB-MIK' => '米尔顿凯恩斯',
                'GB-NEL' => '东北林肯郡',
                'GB-NLN' => '北林肯郡',
                'GB-NSM' => '北萨默塞特',
                'GB-NBL' => '诺森伯兰',
                'GB-NGM' => '诺丁汉',
                'GB-PTE' => '彼得伯勒',
                'GB-PLY' => '普利茅斯',
                'GB-POL' => '普尔',
                'GB-POR' => '朴次茅斯',
                'GB-RDG' => '雷丁',
                'GB-RCC' => '雷德卡-克利夫兰',
                'GB-RUT' => '拉特兰',
                'GB-SHR' => '什罗普郡',
                'GB-SLG' => '斯劳',
                'GB-SGC' => '南格洛斯特郡',
                'GB-STH' => '南安普敦',
                'GB-SOS' => '绍森德',
                'GB-STT' => '斯托克顿',
                'GB-STE' => '特伦特河畔斯托克',
                'GB-SWD' => '斯温登',
                'GB-TFW' => '特尔福德-雷金',
                'GB-THR' => '瑟罗克',
                'GB-TOB' => '托贝',
                'GB-WRT' => '沃灵顿',
                'GB-WBK' => '西伯克郡',
                'GB-WIL' => '威尔特郡',
                'GB-WNM' => '温莎-梅登黑德',
                'GB-WOK' => '沃金厄姆',
                'GB-YOR' => '约克',
                'GB-ANN' => '安特里姆-纽敦阿比',
                'GB-AND' => '阿兹-北唐',
                'GB-ABC' => '阿马-班布里奇-克雷加文',
                'GB-BFS' => '贝尔法斯特',
                'GB-CCG' => '铜锣海岸-峡谷',
                'GB-DRS' => '德里-斯特拉班',
                'GB-FMO' => '弗马纳-奥马',
                'GB-LBC' => '利斯本-卡斯尔雷',
                'GB-MEA' => '中东安特里姆',
                'GB-MUL' => '中阿尔斯特',
                'GB-NMD' => '纽里-莫恩-唐',
                'GB-ABE' => '阿伯丁市',
                'GB-ABD' => '阿伯丁郡',
                'GB-ANS' => '安格斯',
                'GB-AGB' => '阿盖尔-比特',
                'GB-CLK' => '克拉克曼南郡',
                'GB-DGY' => '邓弗里斯-加洛韦',
                'GB-DND' => '邓迪市',
                'GB-EAY' => '东艾尔郡',
                'GB-EDU' => '东邓巴顿郡',
                'GB-ELN' => '东洛锡安',
                'GB-ERW' => '东伦弗鲁郡',
                'GB-EDH' => '爱丁堡',
                'GB-ELS' => '外赫布里底群岛',
                'GB-FAL' => '福尔柯克',
                'GB-FIF' => '法夫',
                'GB-GLG' => '格拉斯哥市',
                'GB-HLD' => '高地',
                'GB-IVC' => '因弗克莱德',
                'GB-MLN' => '中洛锡安',
                'GB-MRY' => '马里',
                'GB-NAY' => '北艾尔郡',
                'GB-NLK' => '北拉纳克郡',
                'GB-ORK' => '奥克尼群岛',
                'GB-PKN' => '珀斯-金罗斯',
                'GB-RFW' => '伦弗鲁郡',
                'GB-SCB' => '苏格兰边区',
                'GB-ZET' => '设得兰群岛',
                'GB-SAY' => '南艾尔郡',
                'GB-SLK' => '南拉纳克郡',
                'GB-STG' => '斯特灵',
                'GB-WDU' => '西邓巴顿郡',
                'GB-WLN' => '西洛锡安',
                'GB-BGW' => '布莱奈格温特',
                'GB-BGE' => '布里真德',
                'GB-CAY' => '卡菲利',
                'GB-CRF' => '加的夫',
                'GB-CMN' => '卡马森郡',
                'GB-CGN' => '锡尔迪金',
                'GB-CWY' => '康威',
                'GB-DEN' => '登比郡',
                'GB-FLN' => '弗林特郡',
                'GB-GWN' => '圭内斯',
                'GB-AGY' => '安格尔西岛',
                'GB-MTY' => '梅瑟蒂德菲尔',
                'GB-MON' => '蒙茅斯郡',
                'GB-NTL' => '尼思-塔尔伯特港',
                'GB-NWP' => '纽波特',
                'GB-PEM' => '彭布罗克郡',
                'GB-POW' => '波伊斯',
                'GB-RCT' => '朗达卡嫩塔夫',
                'GB-SWA' => '斯旺西',
                'GB-TOF' => '托法恩',
                'GB-VGL' => '格拉摩根谷',
                'GB-WRX' => '雷克瑟姆',
            ],
            'CN' => [
                '11' => '北京市',
                '12' => '天津市',
                '13' => '河北省',
                '14' => '山西省',
                '15' => '内蒙古自治区',
                '21' => '辽宁省',
                '22' => '吉林省',
                '23' => '黑龙江省',
                '31' => '上海市',
                '32' => '江苏省',
                '33' => '浙江省',
                '34' => '安徽省',
                '35' => '福建省',
                '36' => '江西省',
                '37' => '山东省',
                '41' => '河南省',
                '42' => '湖北省',
                '43' => '湖南省',
                '44' => '广东省',
                '45' => '广西壮族自治区',
                '46' => '海南省',
                '50' => '重庆市',
                '51' => '四川省',
                '52' => '贵州省',
                '53' => '云南省',
                '54' => '西藏自治区',
                '61' => '陕西省',
                '62' => '甘肃省',
                '63' => '青海省',
                '64' => '宁夏回族自治区',
                '65' => '新疆维吾尔自治区',
                '71' => '台湾省',
                '81' => '香港特别行政区',
                '82' => '澳门特别行政区',
            ],
        ];
        $englishTranslations = [
            'CN' => [
                '11' => 'Beijing',
                '12' => 'Tianjin',
                '13' => 'Hebei',
                '14' => 'Shanxi',
                '15' => 'Inner Mongolia Autonomous Region',
                '21' => 'Liaoning',
                '22' => 'Jilin',
                '23' => 'Heilongjiang',
                '31' => 'Shanghai',
                '32' => 'Jiangsu',
                '33' => 'Zhejiang',
                '34' => 'Anhui',
                '35' => 'Fujian',
                '36' => 'Jiangxi',
                '37' => 'Shandong',
                '41' => 'Henan',
                '42' => 'Hubei',
                '43' => 'Hunan',
                '44' => 'Guangdong',
                '45' => 'Guangxi Zhuang Autonomous Region',
                '46' => 'Hainan',
                '50' => 'Chongqing',
                '51' => 'Sichuan',
                '52' => 'Guizhou',
                '53' => 'Yunnan',
                '54' => 'Tibet Autonomous Region',
                '61' => 'Shaanxi',
                '62' => 'Gansu',
                '63' => 'Qinghai',
                '64' => 'Ningxia Hui Autonomous Region',
                '65' => 'Xinjiang Uygur Autonomous Region',
                '71' => 'Taiwan',
                '81' => 'Hong Kong Special Administrative Region',
                '82' => 'Macao Special Administrative Region',
            ],
        ];

        foreach ($data[$countryCode] as $isoCode => $name) {
            $storageDate = (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT);
            $id = Uuid::randomBytes();
            $countryStateData = [
                'id' => $id,
                'country_id' => $countryId,
                'short_code' => $isoCode,
                'created_at' => $storageDate,
            ];
            $connection->insert('country_state', $countryStateData);
            $connection->insert('country_state_translation', [
                'language_id' => Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM),
                'country_state_id' => $id,
                'name' => $name,
                'created_at' => $storageDate,
            ]);

            if (isset($englishTranslations[$countryCode])) {
                $connection->insert('country_state_translation', [
                    'language_id' => Uuid::fromHexToBytes($this->getEnLanguageId()),
                    'country_state_id' => $id,
                    'name' => $name,
                    'created_at' => $storageDate,
                ]);
            }
        }
    }

    private function createLanguage(Connection $connection): void
    {
        $localeEn = Uuid::randomBytes();
        $localeZh = Uuid::randomBytes();
        $languageZh = Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        $languageEn = Uuid::fromHexToBytes($this->getEnLanguageId());

        // first locales
        $connection->insert('locale', ['id' => $localeEn, 'code' => 'en-GB', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);
        $connection->insert('locale', ['id' => $localeZh, 'code' => 'zh-CN', 'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)]);

        // second languages
        $connection->insert('language', [
            'id' => $languageEn,
            'name' => 'English',
            'locale_id' => $localeEn,
            'translation_code_id' => $localeEn,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        $connection->insert('language', [
            'id' => $languageZh,
            'name' => '中文',
            'locale_id' => $localeZh,
            'translation_code_id' => $localeZh,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        // third translations
        $connection->insert('locale_translation', [
            'locale_id' => $localeEn,
            'language_id' => $languageEn,
            'name' => 'English',
            'territory' => 'United Kingdom',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);
        $connection->insert('locale_translation', [
            'locale_id' => $localeEn,
            'language_id' => $languageZh,
            'name' => '中文',
            'territory' => '中国',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);
        $connection->insert('locale_translation', [
            'locale_id' => $localeZh,
            'language_id' => $languageEn,
            'name' => 'Chinese',
            'territory' => 'China',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);
        $connection->insert('locale_translation', [
            'locale_id' => $localeZh,
            'language_id' => $languageZh,
            'name' => '中文',
            'territory' => '中国',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);
    }

    private function getEnLanguageId(): string
    {
        if (!$this->enLanguageId) {
            $this->enLanguageId = Uuid::randomHex();
        }

        return $this->enLanguageId;
    }
}
