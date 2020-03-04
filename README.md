# BC Api Base

## Testing

`./vendor/bin/phpunit --configuration ./web/core --group bc_api --color --verbose --debug [FILE]`

If you have a full instal and after enabling the simpletest module: (Login seems to have a bug for some reason...) [Be careful of deprecations!!]
`php web/core/scripts/run-tests.sh --url [LOCAL SITE URL] --module bc_api_base --verbose --color`
