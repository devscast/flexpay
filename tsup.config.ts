import { defineConfig } from "tsup";

export default defineConfig({
  entry: ["src/index.ts"],
  clean: true,
  format: ["cjs", "esm"],
  dts: {
    // tsup injects baseUrl internally; TypeScript 6 deprecates that option.
    compilerOptions: { ignoreDeprecations: "6.0" },
  },
});
