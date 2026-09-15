# BC Api Base

Base layer for building JSON API endpoints in Drupal — provides controller/response base classes, a plugin-based platform system for output transformation, and services for exposing files, images, audio, and video through a consistent API response shape.

## Requirements

- Drupal 10 or Drupal 11
- PHP 8.2 or higher

## Versions

### 3.x Branch

- **3.1.x**: Drupal 10/11 support (PHP 8.2+)

## Includes

- `ApiControllerBase` / `ApiControllerInterface` — base controller for building API endpoints, with parameter validation (`ApiParameterValidation`) and annotation-driven docs (`ApiDoc`/`ApiParam`/`ApiBaseDoc`)
- `CacheableJsonResponseTrait` — helper for returning cacheable JSON responses
- A "Platform" plugin system (`PlatformManager`, `PlatformInterface`, `PlatformBase`) that lets output be transformed per consuming platform (includes a `DefaultPlatform` and a `CinderPlatform`, marked experimental), via `ValueTransformationService`
- `AssetApiService` — aggregates file/image/audio/video exposure for API responses via its public `$file`, `$image`, `$audio`, and `$video` properties, backed by `FileApiService`, `ImageApiService`, `AudioApiService`, and `VideoApiService`
- `ApiSubscriber` event subscriber for API-related request/response handling
- `ApiCacheSettings` admin form for configuring the module's cache bin
- Depends on the `key_auth` module for authenticating API requests

### Submodules

- `bc_api_docs`: (Experimental) documentation functionality for the API
- `bc_api_example`: Example of how to set up an API Controller and Platform Plugin
- `bc_api_logger`: Logging functionality for the API base

## Not using Composer

If you are not using composer, you can delete all unneeded files.

- composer.json

## Using Composer

If you are using composer to manage Drupal modules, make sure you add custom
location for this module to be downloaded to. You must add the installer types
line as well as the location for the module.

```json
  ...
  "installer-types": ["custom-drupal-module"],
  "installer-paths": {
    "web/core": ["type:drupal-core"],
    "web/modules/contrib/{$name}": ["type:drupal-module"],
    "web/modules/custom/{$name}": ["type:custom-drupal-module"],
    "web/profiles/contrib/{$name}": ["type:drupal-profile"],
    "web/themes/contrib/{$name}": ["type:drupal-theme"],
    "drush/contrib/{$name}": ["type:drupal-drush"]
  },
  ...
```

## Testing

This module includes automated tests that run via GitHub Actions against Drupal 10.5.x-11.3.x (see `.github/drupal-ci.yml` for the exact PHP/MariaDB matrix).

### Test Plan

#### Automated Tests (GitHub Actions)

The CI pipeline runs the following for each Drupal version:

1. **PHPCS** - Drupal and DrupalPractice coding standards validation
2. **PHPStan** - Drupal-aware static analysis (deprecation/API checks, level 2)
3. **PHPUnit** - automated tests, with code coverage reporting

#### Current coverage

Unit tests cover query parameter validation (`QueryValidationTest`) and the cacheable JSON response trait (`CacheableJsonResponseTraitTest`). A functional test (`ResponseTest`) covers API response behavior. Kernel coverage for the asset/platform services is not yet written.

Note: prior to this release, `QueryValidationTests.php` and `ResponseTests.php` used the plural "Tests" filename suffix instead of PHPUnit's default "Test" suffix, which meant PHPUnit's directory-based discovery silently never ran either file in CI. Renamed to fix this -- their tests are now actually executed.

#### Manual Testing

`./vendor/bin/phpunit --configuration ./web/core --group bc_api --color --verbose --debug [FILE]`

If you have a full install and after enabling the simpletest module: (Login seems to have a bug for some reason...) [Be careful of deprecations!!]
`php web/core/scripts/run-tests.sh --url [LOCAL SITE URL] --module bc_api_base --verbose --color`

## Changelog

### 3.1.x

- Moved CI to a shared, config-driven orchestrator in `bluecadet/web-gh-actions` -- the test matrix now lives in `.github/drupal-ci.yml` instead of a hand-copied in-repo workflow file
- Added `phpstan.neon.dist` and a PHPStan analysis step to CI (this module didn't have one before)
- Added `composer.json` stubs to the three submodules so per-submodule coverage reporting works
- Fixed several postcss plugins that were silently relying on an old transitive dependency rather than being declared directly; updated `@bluecadet/drops` to `^1.2.1`
- Bumped `bluecadet/bc_drupal_package_manager` to `^1.1` and added `extra.bluecadet-package-manager` version metadata
- Working on D11 compatibility
- Drupal 11 compatibility
- Removing Drupal 9 compatibility
- Add `trace` param. Synonym of `debug`, but `debug` can cause errors on certain environments.

### 8.x-3.0.0

- Added Drupal 10 compatibility
- Code Style changes
- Marking API Docs module as (Experimental)

### 8.x-2.0.2

- Fixed Deprecated drupal_set_message() func

### 8.x-2.0.1

- Update readme
- Fix OnException Backwards compatibility with Drupal 8
- Update Drupal module versions appropriatly

### 8.x-2.0.0

- Prepare for Drupal 9 compatability
- Tweak Cinder Allowed Tags
- Updates for COmposer V2

<br>
<br>
<br>

## Proudly developed @ Bluecadet

<p style="background-color: white; padding: 20px">
  <a href="https://www.bluecadet.com/"><img style="max-width: 50%; min-width: 300px; background: white; padding: 20px;" src="https://www.bluecadet.com/wp-content/themes/bluecadet-2018/images/logo/logo-bluecadet-black.svg" alt="Bluecadet"></a>
</p>
