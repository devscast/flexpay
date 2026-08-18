import { describe, expect, expectTypeOf, it } from "vitest";

import type {
  CardRequest,
  CardResponse,
  CheckResponse,
  Currency,
  EnvironmentType,
  MobileRequest,
  MobileResponse,
  PayoutRequest,
  PayoutResponse,
  Transaction,
} from "../index";
import {
  CardRequestSchema,
  CardResponseSchema,
  CheckResponseSchema,
  Client,
  CurrencySchema,
  Environment,
  MobileRequestSchema,
  MobileResponseSchema,
  PayoutRequestSchema,
  PayoutResponseSchema,
  Status,
  StatusSchema,
  TransactionSchema,
} from "../index";

describe("public API", () => {
  it("exports the runtime API from the package root", () => {
    expect(Client).toBeTypeOf("function");
    expect(Environment).toBeTypeOf("function");
    expect(Status.SUCCESS).toBe(0);
    expect(StatusSchema.parse("0")).toBe(Status.SUCCESS);
    expect(CurrencySchema.parse("CDF")).toBe("CDF");

    expect([
      CardRequestSchema,
      CardResponseSchema,
      CheckResponseSchema,
      MobileRequestSchema,
      MobileResponseSchema,
      PayoutRequestSchema,
      PayoutResponseSchema,
      TransactionSchema,
    ]).toHaveLength(8);
  });

  it("exports the public TypeScript vocabulary", () => {
    expectTypeOf<Currency>().toEqualTypeOf<"USD" | "CDF">();
    expectTypeOf<EnvironmentType>().toEqualTypeOf<"prod" | "dev">();

    expectTypeOf<CardRequest>().toBeObject();
    expectTypeOf<CardResponse>().toBeObject();
    expectTypeOf<CheckResponse>().toBeObject();
    expectTypeOf<MobileRequest>().toBeObject();
    expectTypeOf<MobileResponse>().toBeObject();
    expectTypeOf<MobileResponse["providerReference"]>().toEqualTypeOf<string | null>();
    expectTypeOf<PayoutRequest>().toBeObject();
    expectTypeOf<PayoutResponse>().toBeObject();
    expectTypeOf<Transaction>().toBeObject();
  });
});
