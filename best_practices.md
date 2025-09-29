# 📘 Project Best Practices

## 1. Project Purpose
A React + TypeScript + Vite single-page application starter focused on fast DX, strict TypeScript, Tailwind CSS v4, and opinionated linting/formatting. The repository provides a foundation to build a modern SPA with a clean structure (pages/components/hooks/utils), path aliases, and guardrails via ESLint, Prettier, Husky, and Commitlint.

## 2. Project Structure
- Root
  - `index.html` – Vite entry HTML (mounts `#root`)
  - `vite.config.ts` – Vite plugins and path alias (`@ -> src`)
  - `eslint.config.js` – ESLint setup (TS, React Hooks, Prettier, unused imports)
  - `.prettierrc` – Formatting rules (integrates tailwind class sorting)
  - `tsconfig*.json` – TypeScript config with strict mode and path aliases
  - `.husky/` – Git hooks (`commit-msg`, `pre-commit`, `pre-push`)
  - `commitlint.config.js` – Conventional commits enforcement
  - `package.json` – Scripts and dependencies (Yarn 4)
- `public/` – Static assets served as-is
- `src/` – Application source
  - `main.tsx` – App bootstrap with `StrictMode`
  - `App.tsx` – Top-level application component
  - `index.css` – Tailwind v4 setup with CSS variables, themes and base styles
  - `assets/` – Static assets
  - `components/`
    - `ui/` – Reusable primitives (e.g., `button.tsx` using cva variants)
    - `customUi/` – Higher-level design system or app-specific UI wrappers
    - `navbar/` – Navigation components (e.g., `navItem.tsx`)
  - `pages/` – Routeable pages with optional `sections/` subfolders
    - Example: `pages/pageA/index.tsx`, `pages/pageA/sections/sample.tsx`
  - `hooks/` – Custom React hooks (e.g., `useUser.ts`)
  - `apis/` – API modules (e.g., `user.ts`) to encapsulate network calls
  - `libs/` – Infrastructure libraries (e.g., `axios.ts` for an HTTP client instance)
  - `lib/` – General-purpose library code (e.g., `utils.ts` with `cn`)
  - `utils/` – Generic utilities (`functions.ts`, `helpers.ts`, `upload.ts`)
  - `constants/` – Constants such as routes and nav items
  - `data/` – Static/sample data sources
  - `types/` – Shared TypeScript types

Notes:
- Use `@/` alias for imports from `src` (configured in tsconfig and Vite).
- Prefer co-locating page-specific components under `pages/<page>/sections/`.
- Keep naming consistent: consider standardizing on `lib/` (singular) or `libs/` (plural). Today both exist; prefer one (recommended: `lib/`).

## 3. Test Strategy
Current state: No tests are present. Adopt the following strategy when adding tests:
- Frameworks
  - Unit tests: Vitest
  - React component tests: @testing-library/react + @testing-library/jest-dom
  - Mocking network: MSW (Mock Service Worker) for integration-like tests
- Organization
  - Co-locate tests next to source files: `*.test.ts` / `*.test.tsx`
  - or use `src/__tests__/` for cross-cutting tests
- Naming conventions
  - Files: `component-name.test.tsx`, `util-name.test.ts`
- Philosophy
  - Unit tests for utilities, hooks, and pure logic
  - Component tests for user interactions and rendering
  - Integration tests for page flows and API boundaries (via MSW)
  - Target ≥80% coverage on changed code paths; write tests for bug fixes
- Mocking guidelines
  - Prefer MSW to mock HTTP at the boundary rather than mocking internal modules
  - Use `vi.mock` sparingly; mock only true externalities (network, time, random)
- Example setup (guidance)
  - Configure Vitest in `vite.config.ts` or a dedicated `vitest.config.ts`
  - Use `jsdom` test environment for DOM components

## 4. Code Style
- TypeScript
  - `strict: true`; avoid `any`; prefer `unknown` + type narrowing
  - Define shared types in `src/types` and export from index when useful
  - Keep module boundaries clear: `apis/` for I/O code; `utils/` for pure functions
- React
  - Function components with hooks; keep components pure and small
  - Use `asChild` pattern with Radix `Slot` only when you must forward semantics
  - Co-locate page sections under `pages/<page>/sections`
- Imports
  - Use `@/` alias; avoid deep relative paths (`../../..`)
  - Remove unused imports/vars (enforced via eslint-plugin-unused-imports)
- Formatting & Linting
  - Prettier controls formatting (tailwind class sorting via plugin)
  - ESLint: React Hooks rules, Prettier as error, unused imports removed
  - Run `yarn validate` before pushing or rely on hooks
- Naming
  - Components and hooks: PascalCase for components, `useXxx` for hooks
  - Utility modules/types/constants: kebab-case or snake_case filenames are acceptable; be consistent within a folder
  - Export style: prefer named exports for libraries; default export for pages/components may be acceptable when ergonomic
- Styling (Tailwind v4)
  - Use `cn` from `src/lib/utils.ts` to merge conditional classes
  - Leverage design tokens in `index.css` (`--color-*`, `--radius-*`, screens)
- Error handling
  - At the API boundary, normalize errors and return typed results (e.g., `Result<T, E>`) or throw typed errors; avoid leaking raw errors to UI
  - Surface friendly UI states (loading, empty, error) in components

## 5. Common Patterns
- Utilities
  - `cn(...classes)` helper combining `clsx` and `tailwind-merge`
- UI Variants
  - `class-variance-authority` (cva) to define style variants (see `components/ui/button.tsx`)
- Composition
  - Radix `Slot` with `asChild` for polymorphic components
- Configuration
  - Path alias `@` defined in `tsconfig` and `vite.config.ts`
- Constants-first routing
  - Plan routes in `constants/routes.constant.ts`; build nav items from constants
- Environment configuration
  - Use `import.meta.env` for Vite envs; prefix custom variables with `VITE_`

## 6. Do's and Don'ts
- Do
  - Use `@/` imports and maintain a clear folder ownership (apis/hooks/pages)
  - Keep components small, typed, and pure; extract logic into hooks/utils
  - Use cva variants for reusable UI primitives
  - Keep naming consistent; choose `lib/` or `libs/` and stick to it
  - Write tests for utilities and user-facing behaviors when features are added
  - Use conventional commits (enforced by commitlint) and let Husky run checks
- Don't
  - Mix responsibilities (e.g., API calls in UI components)
  - Commit unused code or leave unused imports/vars (CI will fail)
  - Hardcode URLs, tokens, or secrets; use env variables
  - Introduce axios/fetch wrappers without adding the required dependency/config

## 7. Tools & Dependencies
- Runtime
  - React 19, React DOM 19
  - React Router (core) 7 – note: for DOM apps, prefer `react-router-dom` bindings
  - Tailwind CSS 4, `tw-animate-css`
  - `class-variance-authority`, `clsx`, `tailwind-merge` for styling patterns
  - `lucide-react` for icons
- Tooling
  - Vite 6 + `@vitejs/plugin-react`
  - ESLint 9 (TS, React Hooks, Prettier, unused imports)
  - Prettier 3 (+ tailwindcss plugin)
  - Husky + lint-staged + Commitlint
- Scripts
  - `yarn dev` – run locally
  - `yarn build` – type-check then build
  - `yarn preview` – preview production build
  - `yarn format` / `yarn format:write` – check/format with Prettier
  - `yarn lint` / `yarn lint:fix` – lint with ESLint
  - `yarn validate` – format + lint + production build
- Notes on dependencies
  - `libs/axios.ts` exists but axios is not installed. Either:
    - Install `axios` and maintain a typed instance with interceptors, or
    - Remove the file and use `fetch` with a small typed wrapper
  - React Router DOM bindings are not present. If routing is added, install `react-router-dom` and configure in `main.tsx`.

## 8. Other Notes
- For generated code:
  - Use the `@/` alias and existing utilities (e.g., `cn`)
  - Follow Tailwind class ordering (Prettier plugin will enforce)
  - Keep file extensions: `.tsx` for React components, `.ts` for modules
  - Co-locate page-specific components under `pages/<page>/sections`
  - Maintain strict typing; avoid `any`
- Routing
  - Introduce routing via `react-router-dom` and centralize route paths in `constants/routes.constant.ts`
- API Layer
  - Centralize HTTP in `apis/` and a single client in `lib(s)/axios.ts` or a typed `fetch` wrapper; handle errors uniformly
- Consistency cleanups (recommended)
  - Choose either `lib/` or `libs/` and migrate
  - Standardize component filenames (prefer `PascalCase.tsx`) and export patterns
  - Fill currently empty placeholders (e.g., `pages/*/index.tsx`, `apis/user.ts`) when features are implemented
