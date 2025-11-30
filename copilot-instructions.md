Copilot / Contributor Instructions

Goal
- Keep the repository small and maintainable. Implement features in small commits. Keep public APIs and DB migrations backwards-compatible where possible.

Coding conventions
- Follow existing project style (PSR-12 / Yii2 conventions).
- Use descriptive method and variable names. Avoid one-letter names.
- Do not modify unrelated files in a PR.

Branches & commits
- Work on feature branches: `feature/<short-description>`.
- Use atomic commits and clear commit messages.

Database
- Add migrations to `migrations/` for any schema change. Name migrations with the timestamp prefix.
- Use transactions for multi-row writes.

Security & Safety
- Escape output in views and use parameter binding in queries.
- Use prepared statements and ActiveRecord; never build SQL by concatenation.
- Validate user input at model rules and controller level.

Testing
- Add unit tests to `tests/unit/` and functional/acceptance tests to corresponding folders.
- Run existing tests before opening PRs.

PR checklist
- Code compiles / runs without errors.
- Tests pass locally.
- Migrations included where necessary.
- Docs updated: `CUSTOM_INSTRUCTIONS.md` or per-feature README.

Copilot-specific hints (for AI-assisted code generation)
- Prioritize small, well-tested changes.
- When generating code, prefer using existing models/controllers to follow project patterns.
- Ask for clarification if a change touches the database schema or authentication flows.

If you want, I can create a sample PR implementing one of the recommended improvements (RBAC, image uploads, inventory locking, tests).