# Plan: Resolving Blade/Vue Syntax Conflict in debug.blade.php

## 1. Problem Statement

A PHP syntax error (`PHP2014: Syntax error: unexpected token '{'`) was identified in the file [`delivery_admin_backend/resources/views/new-orders-shadcn/debug.blade.php`](delivery_admin_backend/resources/views/new-orders-shadcn/debug.blade.php:1) on line 96, column 49.

The error occurs on this line:

```html
96 | :class="['filter-pill', { 'filter-pill-active': filter.active }]"
```

This file is also using Vue.js-style double curly braces for interpolation (e.g., `{{ filter.label }}` on line 99), which can also conflict with Blade's syntax.

## 2. Analysis of the Conflict

- **Blade vs. Vue.js:** The file [`debug.blade.php`](delivery_admin_backend/resources/views/new-orders-shadcn/debug.blade.php:1) is processed by Laravel's Blade templating engine on the server _before_ it is sent to the client's browser where Vue.js would execute.
- **Blade's Interpretation:** The Blade engine (or the underlying PHP linter) is misinterpreting the curly braces `{}` used for a JavaScript object within the Vue `:class` binding as part of a Blade directive or PHP code block. This causes a PHP syntax error.
- **Vue Interpolation:** Similarly, Blade will attempt to parse `{{ vueVariable }}` as PHP output, which will either fail if `vueVariable` is not a defined PHP variable or consume the syntax, preventing Vue from rendering it.

## 3. Parsing Flow and Issue Visualization

```mermaid
graph TD
    A[Browser requests page with debug.blade.php] --> B{Blade Engine on Server};
    B -- Processes .blade.php file --> C{Interprets Blade syntax e.g. @if, {{ \$phpVar }}};
    C -- Current Problem --> D[Sees Vue's { 'obj':key } or {{ vueVar }} as Blade syntax --> PHP Syntax Error];
    C -- Proposed Solution --> E[Blade's @verbatim tells it to ignore a section];
    E -- Outputs Vue template block as raw HTML/JS --> F[Generated HTML + Vue template sent to Browser];
    F --> G{Vue.js on Client};
    G -- Parses Vue template (v-for, :class, {{ vueVar }}) --> H[Renders dynamic content correctly];
    D --> I[Page may fail or have JS errors];
```

## 4. Proposed Solution: Use `@verbatim`

To prevent Blade from attempting to parse Vue.js-specific syntax, we will use Blade's `@verbatim` and `@endverbatim` directives. These directives instruct Blade to output the enclosed content exactly as it is, without any Blade processing.

The Vue-specific block in the provided code snippet starts with the `div` containing the `v-for` directive (line 93) and extends to its corresponding closing `</div>` (line 101).

## 5. Proposed Code Modification

The following diff shows the changes to be applied to [`delivery_admin_backend/resources/views/new-orders-shadcn/debug.blade.php`](delivery_admin_backend/resources/views/new-orders-shadcn/debug.blade.php:1):

```diff
--- a/delivery_admin_backend/resources/views/new-orders-shadcn/debug.blade.php
+++ b/delivery_admin_backend/resources/views/new-orders-shadcn/debug.blade.php
@@ -90,16 +90,18 @@
              <div class="card">
                  <h2>Filter Pills</h2>
                  <div class="filter-row">
+@verbatim
                      <div
                          v-for="filter in filters"
                          :key="filter.name"
                          :class="['filter-pill', { 'filter-pill-active': filter.active }]"
                          @click="toggleFilter(filter)"
                      >
                          <span>{{ filter.label }}</span>
                          <span>▼</span>
                      </div>
+@endverbatim
                  </div>

                  <h2>Orders Table</h2>
```

## 6. Next Steps After Plan Approval

1.  Switch to a mode that allows file modifications (e.g., "Code" mode).
2.  Apply the proposed code changes to [`delivery_admin_backend/resources/views/new-orders-shadcn/debug.blade.php`](delivery_admin_backend/resources/views/new-orders-shadcn/debug.blade.php:1).
3.  Verify that the syntax error is resolved.
4.  Re-evaluate the original "CSRF token mismatch" error to see if this file was related or if further debugging is needed for that issue.
