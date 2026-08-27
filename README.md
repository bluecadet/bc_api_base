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
- Asset API services for exposing files, images, audio, and video in API responses: `AssetApiService` (aggregator, exposes `getFile()`/`getImage()`/`getAudio()`/`getVideo()`), backed by `FileApiService`, `ImageApiService`, `AudioApiService`, and `VideoApiService`
- `ApiSubscriber` event subscriber for API-related request/response handling
- `ApiCacheSettings` admin form for configuring the module's cache bin
- Depends on the `key_auth` module for authenticating API requests

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

This module includes automated tests that run via GitHub Actions against Drupal 10.4.x, 11.0.x, and 11.1.x (see `.github/workflows/drupal-tests-and-standards.yml` for the exact PHP/MariaDB matrix).

### Test Plan

#### Automated Tests (GitHub Actions)

The CI pipeline runs the following for each Drupal version:

1. **PHPCS** - Drupal and DrupalPractice coding standards validation
2. **Drupal-Check** - static analysis for deprecated API usage
3. **PHPUnit** - automated tests (unit and functional)

#### Current coverage

Unit tests cover query parameter validation (`QueryValidationTests`) and the cacheable JSON response trait (`CacheableJsonResponseTraitTest`). A functional test (`ResponseTests`) covers API response behavior.

#### Manual Testing

`./vendor/bin/phpunit --configuration ./web/core --group bc_api --color --verbose --debug [FILE]`

If you have a full install and after enabling the simpletest module: (Login seems to have a bug for some reason...) [Be careful of deprecations!!]
`php web/core/scripts/run-tests.sh --url [LOCAL SITE URL] --module bc_api_base --verbose --color`

## Changelog

- Working on D11 compatibility
- **BREAKING:** `AssetApiService` sub-service properties (`$file`, `$image`, `$audio`, `$video`) are now `protected`. Use the new getter methods `getFile()`, `getImage()`, `getAudio()`, `getVideo()` instead of accessing properties directly.

### 8.x-3.1.x

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
