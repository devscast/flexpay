# Contributing

Thank you for contributing to this Ngandu project.

## Before starting

1. Search existing issues and pull requests.
2. Open an issue before beginning a large or breaking change.
3. Keep each pull request focused on one concern.

## Development

Use the runtime and package-manager versions declared by the repository. Install dependencies from
the lockfile:

```shell
bun install --frozen-lockfile
```

The standard commands are:

```shell
bun run format
bun run lint
bun run typecheck
bun run test
bun run build
bun run check:package
bun run quality
```

`format` changes files. Run it before `quality`, which performs the non-modifying checks used by
continuous integration.

## Changes and tests

- Add or update tests for observable behavior changes.
- Update documentation and examples when the public API changes.
- Add a Changeset for package changes.
- Document breaking changes and include migration instructions.
- Do not commit generated build artifacts.

## Commits

Use Conventional Commits, such as `feat:`, `fix:`, `docs:`, `test:`, `refactor:`, `build:`,
and `chore:`. Add `!` or a `BREAKING CHANGE:` footer when appropriate.

## Pull requests

Complete the pull request template, ensure every required check passes, and respond to review
feedback with focused commits.

By participating, you agree to follow the [Code of Conduct](CODE_OF_CONDUCT.md).
