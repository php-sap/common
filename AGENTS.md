# AGENTS.md

## Project Overview

`php-sap/common` is a **PHP library** providing SAP RFC (Remote Function Call) abstractions
that are independent of the underlying PHP SAP extension (e.g. `saprfc` or `sapnwrfc`).
It defines abstract base classes, API type descriptors, configuration models, and exceptions
consumed by concrete SAP connector packages. Part of [PHP/SAP](https://php-sap.github.io).

Namespaces:
- `phpsap\classes\*` → `src/`
- `phpsap\exceptions\*` → `exceptions/`
- `tests\phpsap\classes\*` → `tests/`

## Ecosystem

[PHP/SAP](https://php-sap.github.io) is split across five focused repositories that build on
each other instead of one monolithic package:

| Repository                  | Role                                                                                                    | Depends on (`composer.json`)                                  |
|-----------------------------|---------------------------------------------------------------------------------------------------------|---------------------------------------------------------------|
| `php-sap/interfaces`        | Contract-only interfaces (`IApi`, `IConfiguration`, `IFunction`, exceptions). No concrete classes.      | —                                                             |
| `php-sap/datetime`          | SAP date/time format support on top of native `DateTime`/`DateInterval`.                                | —                                                             |
| `php-sap/common`            | Generic abstract classes, API/config value objects, and exceptions implementing `interfaces`.           | `interfaces`, `datetime`                                      |
| `php-sap/integration-tests` | Shared abstract PHPUnit test infrastructure and SAP module mocks reused by concrete connector packages. | `interfaces`, `common`, `datetime`                            |
| `php-sap/saprfc-kralik`     | Concrete adapter for Gregor Kralik's `ext-sapnwrfc` extension.                                          | `interfaces`, `common` (+ `integration-tests` for tests only) |

**→ You are here: `php-sap/common`** — the generic implementation of `interfaces`.

This package implements the interfaces generically; extension-specific glue (marshaling
parameters to a native SAP module) belongs in a concrete connector like `saprfc-kralik`,
not here.

## Architecture

### Trait Composition Pattern

All API and Config classes are assembled from fine-grained traits — no deep inheritance.
Every class uses `ConstructorTrait` which iterates allowed JSON keys and calls the
corresponding `set{Key}()` setter, enforcing all required keys at construction time.

Key API traits: `TypeTrait`, `NameTrait`, `DirectionTrait`, `OptionalTrait`,
`MembersTrait`, `CastPrimitivesTrait`, `ConstructorTrait`.

### API Type Hierarchy

`JsonSerializable` (base, `src/Util/`) ← all API and Config classes extend this.

| Class       | Traits                                          | Purpose                                  |
|-------------|-------------------------------------------------|------------------------------------------|
| `Value`     | Type, Name, Direction, Optional, CastPrimitives | Scalar SAP parameter                     |
| `Member`    | Type, Name, CastPrimitives                      | Field inside a Struct/Table              |
| `Struct`    | Type, Name, Direction, Optional, Members        | Named-field complex type                 |
| `Table`     | Type, Name, Direction, Optional, Members        | Row-based tabular data                   |
| `RemoteApi` | —                                               | Factory + container for all API elements |

### SAP Types

Valid types are constants in `TypeTrait`:
`boolean`, `integer`, `float`, `string`, `hexbin`, `date`, `time`,
`timestamp`, `week`, `struct`, `table`.

Date/time casting uses `php-sap/datetime` (`SapDateTime`, `SapDateInterval`).

### Configuration

- `ConfigTypeA` — direct connection: requires `ashost`, `sysnr`
- `ConfigTypeB` — load-balanced: requires `mshost`
- `JsonDecodeTrait::jsonDecode()` auto-detects which type to instantiate from JSON keys.
- Shared parameters (user, passwd, client, saprouter, trace, lang, dest, codepage)
  live in `Config/Traits/CommonTrait.php`.

### Exception Hierarchy

- `SapLogicException` extends `LogicException` — programming errors, code must be fixed.
- `SapRuntimeException` extends `RuntimeException` — recoverable runtime failures.

Both implement `ISapException` from `php-sap/interfaces`.

### AbstractFunction

`src/AbstractFunction.php` is the core base for SAP function execution:
- Caches API definitions in a static `$api` array keyed by function name.
- Subclasses implement `connect()`, `execute()`, `extractApi()`.
- Stores parameters via `JsonSerializable`'s `set()`/`get()` methods.

### Key Interfaces

All public contracts are defined in `php-sap/interfaces` (vendor dependency):
`IApi`, `IApiElement`, `IValue`, `IMember`, `IStruct`, `ITable`,
`IFunction`, `IConfiguration`, `IConfigTypeA`, `IConfigTypeB`, `ISapException`.
When adding new public methods, check this package for the matching interface first.

## Developer Workflows

All commands run through the `Makefile` via Docker, so the host machine does not need a
local PHP installation. Run `make help` for the full target list. Use PHP 8.1, 8.2, and
8.3 (matching the CI matrix in `.github/workflows/main.yml`) for anything
version-sensitive (PHPStan, PHP lint, tests). If you are behind a proxy, `install` and
`audit` already forward `HTTP_PROXY`/`HTTPS_PROXY`/`NO_PROXY`; pass
`CA_CERT_FILE=/path/to/ca.pem` to trust a corporate proxy root CA inside the container.

```bash
# Install/update dependencies for a given PHP version (set DEPENDENCIES_LOWEST=1 for
# --prefer-lowest, matching the CI "lowest" matrix job)
make install PHP_VERSION=8.1

# Run PHPUnit
make test PHP_VERSION=8.1

# Syntax-check every .php file in exceptions/, src/ and tests/, matches CI
make lint PHP_VERSION=8.1

# Run PHPStan
make analyze PHP_VERSION=8.1

# Auto-fix code style (run this before "sniff")
make beautify PHP_VERSION=8.1

# Check code style (uses phpcs.xml)
make sniff PHP_VERSION=8.1

# Check dependencies for known vulnerabilities
make audit

# Run composer validate --strict
make validate
```

**Always use these Makefile targets instead of inventing ad-hoc `docker run`/`composer`/
`php` commands.** If a task needs something the Makefile doesn't expose directly (e.g.
PHPUnit for a single test file/method), take the exact `docker run` invocation from the
matching Makefile target (image, `DOCKER_USER`, `DOCKER_MOUNT`, env forwarding) and only
append the extra arguments — don't build the command from scratch.

`phpstan/phpstan` and `squizlabs/php_codesniffer` are managed as Composer `require-dev`
dependencies (no separate download step needed, unlike `saprfc-kralik`'s `phpcs.phar`).
PHPStan runs at **level 9** (`phpstan.neon`); intentional type mismatches in tests are
suppressed with `@phpstan-ignore-next-line`.

## Conventions

- Test helpers live in `tests/helper/` (e.g. `AbstractFunctionInstance.php` — provides
  fake `extractApi()` and `invoke()` via static properties for controlled test scenarios).
- Tests mirror source layout: `tests/Api/`, `tests/Config/`, `tests/Util/`.
- PHPUnit 9, bootstrap: `vendor/autoload.php`, strict coverage enforced via `phpunit.xml`.

## Safe Change Strategy for Agents

- Before adding a public method, check `php-sap/interfaces` for the matching contract first
  — this package must stay a faithful, generic implementation of those interfaces.
- Before adding a new SAP type, extend `TypeTrait`'s constants and the corresponding
  `CastPrimitivesTrait` logic together; don't add one without the other.
- Extension-specific glue (e.g. marshaling parameters for a native SAP module) does not
  belong here — that's the job of concrete connector packages like `saprfc-kralik`.
- Keep new code PHPStan level 9 clean; only suppress with `@phpstan-ignore-next-line` for
  intentional test-only type mismatches, matching the existing pattern.
- Write documentation, comments, and new code in English to match the repository style.
- Always run QA/build commands through the `Makefile` targets, not self-invented `docker run`
  commands. For one-off variants (a single test, a single file), base the invocation on the
  relevant Makefile target and only append the extra arguments.

