# auxmoney OpentracingBundle - HttplugBundle

[![test](https://github.com/auxmoney/OpentracingBundle-HttplugBundle/workflows/test/badge.svg)](https://github.com/auxmoney/OpentracingBundle-HttplugBundle/actions?query=workflow%3Atest)
![GitHub release (latest SemVer)](https://img.shields.io/github/v/release/auxmoney/OpentracingBundle-HttplugBundle)
![Coveralls github](https://img.shields.io/coveralls/github/auxmoney/OpentracingBundle-HttplugBundle)
![Codacy Badge](https://app.codacy.com/project/badge/Grade/7c97ba1b79f34f27a3a520a525d95da9)
![Code Climate maintainability](https://img.shields.io/codeclimate/maintainability/auxmoney/OpentracingBundle-HttplugBundle)
![Scrutinizer code quality (GitHub/Bitbucket)](https://img.shields.io/scrutinizer/quality/g/auxmoney/OpentracingBundle-HttplugBundle)
![GitHub](https://img.shields.io/github/license/auxmoney/OpentracingBundle-HttplugBundle)

This bundle adds automatic header injection for [php-http/httplug-bundle](https://github.com/php-http/HttplugBundle) clients to the [OpentracingBundle](https://github.com/auxmoney/OpentracingBundle-core).

## Installation

### Prerequisites

This bundle is only an additional plugin and should not be installed independently. See 
[OpentracingBundle](https://github.com/auxmoney/OpentracingBundle-core#installation) and [HttplugBundle](https://github.com/php-http/HttplugBundle) first prior to install this package.

### Require dependencies

After you have installed the OpentracingBundle:

* require the dependencies:

```bash
    composer req auxmoney/opentracing-bundle-php-http-httplug-bundle
```

### Enable the bundle

If you are using [Symfony Flex](https://github.com/symfony/flex), you are all set!

If you are not using it, you need to manually enable the bundle:

* add bundle to your application:

```php
    # Symfony 3: AppKernel.php
    $bundles[] = new Auxmoney\OpentracingHttplugBundle\OpentracingHttplugBundle();
```

```php
    # Symfony 4+: bundles.php
    Auxmoney\OpentracingHttplugBundle\OpentracingHttplugBundle::class => ['all' => true],
```

## Configuration

No configuration is necessary, the provided compiler pass will decorate `PluginClientFactory` in order to add the `OpentracingPlugin` (where headers injection happens) to all existing Httplug clients.

## Usage

When sending a request to other systems, the tracing headers are automatically injected into the requests, thus enabling the full power of distributed tracing.

## Development

Be sure to run

```bash
    composer run-script quality
```

every time before you push code changes. The tools run by this script are also run in the CI pipeline.
