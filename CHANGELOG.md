# CHANGELOG

This changelog references the relevant changes (bug and security fixes) done

### 2.0.0 [unreleased]
- Added: `isSuccessful` to `Transaction` class
- Breaking Change: `pay` supports both `vpos` and `mobile` request
- Added: `vpos` and `mobile` method to `Client`
- Added: `VposResponse`, `Request`, `VposRequest`, `MobileRequest` class
- Fixed: allow `$message` to be an empty string in `CheckResponse`
- Fixed: `token` and `merchant` are now SensitiveParameters
- Added: Support for `check`, `vpos`, `mobile` urls in `Environment`,
- Removed: `getPaymentBaseUrl` method in `Environment`

### 1.0.1
- Fixed: allow `$message` to be an empty string in `PaymentResponse`

### 1.0.0
- Initial release
