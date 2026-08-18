# @devscast/flexpay

## 1.1.0

### Minor Changes

- removed pay overload

## Unreleased

### Patch Changes

- reject malformed status codes and non-finite transaction amounts instead of coercing them into valid response values
- preserve the request-to-response relationship in `Client.pay()` and expose safer callback and success-check types
- export public schemas, status, currency, and environment types from the package root
- add explicit ESM, CommonJS, and declaration export conditions and publish Zod as a runtime dependency
- update usage examples so card requests and transaction checks are valid TypeScript

## 1.0.2

### Patch Changes

- fix audit issues

## 1.0.1

### Patch Changes

- fix: minify production dist
