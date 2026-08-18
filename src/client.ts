import type * as z from "zod";

import { Environment, type EnvironmentType } from "./environment";
import { AccountException, ClientException, NetworkException } from "./exception";
import {
  type CardRequest,
  CardRequestSchema,
  type CardResponse,
  CardResponseSchema,
  type CheckResponse,
  CheckResponseSchema,
  type Credential,
  CredentialSchema,
  type MobileRequest,
  MobileRequestSchema,
  type MobileResponse,
  MobileResponseSchema,
  type PayoutRequest,
  PayoutRequestSchema,
  type PayoutResponse,
  PayoutResponseSchema,
  Status,
  Type,
} from "./schemas";

export class Client {
  private readonly credential: Credential;
  private readonly env: Environment;

  constructor(merchant: string, token: string, env: EnvironmentType = "dev") {
    this.credential = CredentialSchema.parse({ merchant, token });
    this.env = new Environment(env);
  }

  async mobile(request: MobileRequest): Promise<MobileResponse> {
    const body = {
      ...MobileRequestSchema.parse(request),
      merchant: this.credential.merchant,
      type: Type.MOBILE,
    };
    const data = await this.requestJson("POST", this.env.getMobilePaymentUrl(), body);

    return this.parseWith(MobileResponseSchema, data);
  }

  async card(request: CardRequest): Promise<CardResponse> {
    const body = {
      ...CardRequestSchema.parse(request),
      authorization: `Bearer ${this.credential.token}`,
      merchant: this.credential.merchant,
    };
    const data = await this.requestJson("POST", this.env.getCardPaymentUrl(), body);

    return this.parseWith(CardResponseSchema, data);
  }

  pay(request: MobileRequest): Promise<MobileResponse>;
  pay(request: CardRequest): Promise<CardResponse>;
  async pay(request: MobileRequest | CardRequest): Promise<MobileResponse | CardResponse> {
    if ("phone" in request && typeof request.phone === "string") return this.mobile(request);
    if ("homeUrl" in request && typeof request.homeUrl === "string") return this.card(request);
    throw new Error("Unsupported request shape");
  }

  async check(orderNumber: string): Promise<CheckResponse> {
    const data = await this.requestJson("GET", this.env.getCheckStatusUrl(orderNumber));

    return this.parseWith(CheckResponseSchema, data);
  }

  async payout(request: PayoutRequest): Promise<PayoutResponse> {
    const body = {
      ...PayoutRequestSchema.parse(request),
      merchant: this.credential.merchant,
      type: Type.MOBILE,
    };
    const data = await this.requestJson("POST", this.env.getPayoutUrl(), body);

    return this.parseWith(PayoutResponseSchema, data);
  }

  handleCallback(data: unknown): MobileResponse {
    return this.parseWith(MobileResponseSchema, data);
  }

  isSuccessful<T extends { code: Status }>(response: T): response is T & { code: Status.SUCCESS } {
    return response.code === Status.SUCCESS;
  }

  private async requestJson(
    method: "GET" | "POST",
    url: string,
    jsonBody?: unknown,
  ): Promise<unknown> {
    const headers: Record<string, string> = {
      Accept: "application/json",
      // Parity with PHP client (auth_bearer); also most FlexPay endpoints expect Bearer
      Authorization: `Bearer ${this.credential.token}`,
      "Content-Type": "application/json",
    };

    const maxRetries = 3;
    const baseDelayMs = 500;

    for (let attempt = 0; attempt <= maxRetries; attempt++) {
      try {
        const response = await fetch(url, {
          body: method === "POST" ? JSON.stringify(jsonBody ?? {}) : undefined,
          headers,
          method,
        });

        const contentType = response.headers.get("content-type") || "";
        const isJson = contentType.includes("application/json");
        let payload: unknown = {};
        if (isJson) {
          try {
            payload = await response.json();
          } catch {
            payload = {};
          }
        }

        if (!response.ok) {
          const errorPayload =
            typeof payload === "object" && payload !== null
              ? (payload as Record<string, unknown>)
              : {};
          // Mimic PHP error mapping via NetworkException::create
          const message = typeof errorPayload.message === "string" ? errorPayload.message : "";
          const type = typeof errorPayload.error === "string" ? errorPayload.error : "unknown";
          const status = response.status;

          // Throw mapped subclasses (Account/Client/Server/Network)
          throw NetworkException.create(message, type, status);
        }

        return payload;
      } catch (err: unknown) {
        // Network errors or thrown mapped exceptions
        // If it's already one of our mapped exceptions, don't retry on 4xx.
        if (
          err instanceof AccountException ||
          err instanceof ClientException ||
          (err instanceof NetworkException &&
            typeof err.status === "number" &&
            err.status >= 400 &&
            err.status < 500)
        ) {
          // No retry on 4xx equivalents
          throw err;
        }

        // Retry on network/5xx errors
        if (attempt < maxRetries) {
          const delay = baseDelayMs * 2 ** attempt; // 500, 1000, 2000
          await new Promise((r) => setTimeout(r, delay));
          continue;
        }

        // Final failure: if it's a native fetch error, wrap it
        if (!(err instanceof NetworkException)) {
          throw new NetworkException(err instanceof Error ? err.message : "Network error");
        }
        throw err;
      }
    }

    throw new NetworkException("Unexpected request flow");
  }

  private parseWith<S extends z.ZodTypeAny>(schema: S, data: unknown): z.infer<S> {
    return schema.parse(data);
  }
}
