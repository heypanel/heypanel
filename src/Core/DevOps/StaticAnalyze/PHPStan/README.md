# HeyPanel Phpstan extension

HeyPanel specific extension for phpstan. This extension provides additional rules and improves type detection for phpstan in heypanel projects.

## Installation

Require the extension via composer:
```bash
composer require --dev heypanel/phpstan-extension
```

## Configuration

### auto-configure phpstan/extension-installer

When you use the [phpstan/extension-installer](https://github.com/phpstan/extension-installer) package, this extension will be configured and enabled automatically.

### manually configure in phpstan.neon config

Add the following lines to your `phpstan.neon` config file:

```neon
includes:
    - vendor/heypanel/phpstan-extension/extension.neon
    - vendor/heypanel/phpstan-extension/rules.neon
```

## What's included in this extension?

### Type system extension

The `extension.neon` file contains the phpstan configuration for heypanel specific [type extensions](https://phpstan.org/developing-extensions/type-specifying-extensions).

If you only want to use the type extensions without the additional rules, you can also include this file in your `phpstan.neon` config file manually:

```neon 
includes:
    - vendor/heypanel/phpstan-extension/extension.neon
```

### Rules

The `rules.neon` file contains the phpstan configuration for heypanel specific [rules](https://phpstan.org/developing-extensions/rules).
Those rules are opinionated and encoded best practices for heypanel projects and plugins. 

If you only want to use the rules without the type extensions, you can also include this file in your `phpstan.neon` config file manually:

```neon
includes:
    - vendor/heypanel/phpstan-extension/rules.neon
```

### Core specific rules

The `core-rules.neon` file contains the phpstan configuration for heypanel specific [rules](https://phpstan.org/developing-extensions/rules) intendend for heypanel core (& plugin) development.
As this is not suited for generic heypanel projects, this file is not included by default (when installed over the phpstan/extension-installer). If you want to use those rules, you can include this file in your `phpstan.neon` config file manually:

```neon
includes:
    - vendor/heypanel/phpstan-extension/core-rules.neon
```

## Customization

Instead of including the predefined configuration files, you can also create your own configuration file and include only the parts you want to use:

```neon
rules:
    - HeyPanel\Core\DevOps\StaticAnalyze\PHPStan\Rules\NoEnvironmentHelperInsideCompilerPassRule
    - HeyPanel\Core\DevOps\StaticAnalyze\PHPStan\Rules\NoSuperGlobalsInsideCompilerPassRule
```
