# FlexPay TypeScript

![npm](https://img.shields.io/npm/v/@ngandu-dev/flexpay?style=flat-square)
![npm](https://img.shields.io/npm/dt/@ngandu-dev/flexpay?style=flat-square)
[![Quality](https://github.com/ngandu-dev/flexpay/actions/workflows/quality.yml/badge.svg?branch=main)](https://github.com/ngandu-dev/flexpay/actions/workflows/quality.yml)
[![Tests](https://github.com/ngandu-dev/flexpay/actions/workflows/test.yml/badge.svg?branch=main)](https://github.com/ngandu-dev/flexpay/actions/workflows/test.yml)
![GitHub](https://img.shields.io/github/license/ngandu-dev/flexpay?style=flat-square)

For privacy reasons, FlexPay's original documentation cannot be shared without written permission.
For credentials and implementation details, contact [FlexPay](https://flexpay.cd).

## Features

- Typed mobile-money and card payment requests
- Transaction status checks and callback validation
- Development and production API environments
- ESM and CommonJS distributions with TypeScript declarations

## Requirements

- Node.js 24 or newer

## Installation

Add the TypeScript client to your application's dependencies:

```bash
npm install @ngandu-dev/flexpay
```

## Quick start

### Authentication

- **Step 1**. Contact FlexPay to get a Merchant Account
  You will receive a Merchant Form to complete in order to provide your business details and preferred Cash out Wallet or Banking Details.
- **Step 2**. Once the paperwork is completed, you will be issued with Live and Sandbox Accounts (Merchant Code and Authorization token)

Then use these credentials to authenticate your client

```ts
import { Client as Flexpay } from "@ngandu-dev/flexpay";

const flexpay = new Flexpay("merchant_code", "token", "dev"); // use "prod" for production
```

## Usage

### Create a Payment Request

```typescript
import type { CardRequest, MobileRequest } from "@ngandu-dev/flexpay";

const mobile = {
  amount: 10, // 10 USD
  currency: "USD",
  phone: "243999999999",
  reference: "mobile_order_12345",
  description: "your_transaction_description",
  callbackUrl: "https://example.com/flexpay/callback",
} satisfies MobileRequest;

const card = {
  amount: 10, // 10 USD
  currency: "USD",
  reference: "card_order_12345",
  description: "your_transaction_description",
  callbackUrl: "https://example.com/flexpay/callback",
  approveUrl: "https://example.com/flexpay/approved",
  cancelUrl: "https://example.com/flexpay/cancelled",
  declineUrl: "https://example.com/flexpay/declined",
  homeUrl: "https://example.com",
} satisfies CardRequest;
```

> **Note**: we highly recommend your `callbacks` urls to be unique for each transaction.

### Mobile Payment
Once called, Flexpay will send a payment request to the user's mobile money account, and the user will have to confirm the payment on their phone.
after that the payment will be processed and the callback url will be called with the transaction details.

```typescript
const mobileResponse = await flexpay.pay(mobile);
```

### Visa Card Payment
You can set up card payment via VPOS features, which is typically used for online payments.
it's a gateway that allows you to accept payments from your customers using their credit cards.

```typescript
const cardResponse = await flexpay.pay(card);
// redirect to cardResponse.url to complete the payment
```

#### **handling callback (callbackUrl, approveUrl, cancelUrl, declineUrl)**
Flexpay will send a POST request to the defined callbackUrl and the response will contain the transaction details.
you can use the following code to validate the incoming payload.

```typescript
const webhook = flexpay.handleCallback(req.body);
flexpay.isSuccessful(webhook); // true or false
```

### Check Transaction state
You don't trust webhook ? you can always check the transaction state by providing the order number.

```typescript
if (!mobileResponse.orderNumber) {
  throw new Error("FlexPay did not return an order number");
}

const tx = await flexpay.check(mobileResponse.orderNumber);
flexpay.isSuccessful(tx); // true or false
```

## Migrating to `@ngandu-dev/flexpay`

Replace the dependency and every import with `@ngandu-dev/flexpay`. Version 2 is a clean package
move and does not provide an alias for the old scope.

## Development

Install dependencies with `bun install`, then run `bun run quality` before opening a pull request.
See [CONTRIBUTING.md](CONTRIBUTING.md) for the complete contribution workflow.

## Testing

Run `bun run test` for the test suite or `bun run test:coverage` for a coverage report.

## Contributing

Contributions are welcome. Please read [CONTRIBUTING.md](CONTRIBUTING.md) and follow our
[Code of Conduct](CODE_OF_CONDUCT.md).

## Security

Please report vulnerabilities privately as described in [SECURITY.md](SECURITY.md).

## License

Released under the [MIT License](LICENSE).

## Contributors

<a href="https://github.com/ngandu-dev/flexpay/graphs/contributors" title="Show all contributors">
  <img src="https://contrib.rocks/image?repo=ngandu-dev/flexpay" alt="Contributors" />
</a>
