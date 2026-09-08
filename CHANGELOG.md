# Changelog

All notable changes to `@ngandu-dev/flexpay` are documented in this file. This project follows
[Semantic Versioning](https://semver.org/).

## 2.0.1

### Patch Changes

- Remove Commitlint and its transitive dependencies, configuration, and commit-message hook while preserving pre-commit validation. Refresh compatible dependencies and the Bun lockfile.

  Upgrade to Zod 4.5.4, Vitest 5, and TypeScript 6.0.3. Explicitly load Node.js types for the updated compiler; retain the TypeScript compiler API required by tsup declaration generation and accommodate its internally generated deprecated compiler option.

## 2.0.0

### Changed

- Renamed the package to `@ngandu-dev/flexpay`.
- Reject malformed status codes and non-finite transaction amounts instead of coercing them into
  valid response values.
- Preserve the request-to-response relationship in `Client.pay()` and expose safer callback and
  success-check types.
- Export public schemas, status, currency, and environment types from the package root.
- Add explicit ESM, CommonJS, and declaration export conditions and publish Zod as a runtime
  dependency.
- Update usage examples so card requests and transaction checks are valid TypeScript.

### Removed

- Removed support for the former package coordinate.

## 1.1.0

### Minor Changes

- removed pay overload

## 1.0.2

### Patch Changes

- fix audit issues

## 1.0.1

### Patch Changes

- fix: minify production dist
