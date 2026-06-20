---
description: 
globs: 
alwaysApply: true
---
---

# GitHub Copilot Architecture & Coding Guidelines (Stack 2026)

This document outlines the architectural patterns, coding standards, and project-specific rules for our modern stack. Copilot must strictly adhere to these guidelines when generating backend or frontend code.

> **Last Updated:** June 2026 | **Status:** Reviewed & Enhanced
> - ✅ File validation rules corrected with FormRequest patterns
> - ✅ Tailwind v4 CSS-first approach documented
> - ✅ RG mask format standardized to 12 characters (XX.XXX.XXX-D)
> - ✅ Complete mask function reference added
> - ✅ Backend error handling & form validation documented
> - ✅ Debugging best practices & anti-patterns added

---

## 1. Project Stack & Ecosystem Context

### Backend
- **Core:** Laravel 12.x (PHP 8.2+)
- **Integration:** Inertia.js Laravel Adapter (^3.1)
- **Utilities:** Ziggy (^2.6) for routing, Awobaz Compoships (^3.0) for advanced relationships, Owen-IT Laravel Auditing (^14.0), Stevebauman Location (^7.6).

### Frontend
- **Core:** Vue 3.5+ (Composition API with `<script setup>`)
- **Integration:** @inertiajs/vue3 (^3.4.0)
- **Bundler & Styles:** Vite 8.x + Tailwind CSS v4.0 (using `@tailwindcss/vite`)
- **UI Components:** Floating Vue (^5.2.2) for tooltips/dropdowns, FontAwesome Free (^7.2.0) for iconography.
- **Dates:** Moment.js (^2.30.1)

---

## 2. Core Architectural Rules

### Backend (Laravel 12)
1. **Controllers:** Always return Inertia responses (`Inertia::render('PageName', $data)`). Do not return standard Blade views or raw JSON unless building an API endpoint.
2. **Global Share:** Global constants, configuration parameters, and permissions (e.g., `SISTEMA_LIMITE_UPLOAD`) must be injected through the Inertia Middleware (`HandleInertiaRequests.php`) inside the `share()` method.
3. **File Handling:**
   - **Validation:** Always use Form Request classes (`app/Http/Requests`) with explicit file validation rules. Example: `'file' => ['required', 'file', 'mimes:pdf,jpg,png', 'max:10240']` (size in KB). Always validate MIME types and dimensions for images using `dimensions:min_width=100,min_height=100`.
   - **Integrity Check:** After upload, verify integrity with `$request->file(...)->isValid()` and verify stored file exists with `Storage::disk('public')->exists()`.
   - **Storage:** Store files using Laravel's file storage abstraction targeting the `'public'` disk. Never store directly in `public/` folder without going through the storage API.
   - **Filename Generation:** Generate secure, collision-free filenames using: `date('Y_m_d_H_i_s') . '_' . Str::uuid() . '.' . pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION)`. Always call `strtolower()` on the extension.

### Frontend (Vue 3.5+ & Tailwind v4)
1. **Component Paradigm:** Always implement `<script setup>` with ESM imports. Avoid Options API or legacy syntax.
2. **Form Management:** Use Inertia's `useForm` hook exclusively for handling local forms, reactivity, submission tracking, error handling, and file binary packing.
3. **Tailwind CSS v4 Conventions:**
   - **CSS-First Approach:** Tailwind v4 requires `@import 'tailwindcss';` in the main CSS file (`resources/css/app.css`). Configuration is declared via `@theme` directive, NOT via `tailwind.config.js`.
   - **Semantic Token System:** All color classes use CSS variables defined in `@theme`. Reference them with kebab-case (e.g., `bg-layout-painel`, `border-comum`, `text-texto-claro`, `text-primary`, `hover:bg-primary-hover`). Never hardcode color values; always use semantic tokens from `@theme` for consistency.
   - **Dynamic Theme Variables:** The project uses runtime CSS variables (`--cor-marca-rgb`, `--cor-fundo-rgb`, `--cor-painel-rgb`, `--cor-texto-claro`, `--cor-texto-escuro`) that change based on user theme selection (light/dark mode and color variant). When writing Tailwind classes, always use the token wrappers, not raw colors.
   - **Opacity Syntax:** Use v4 slash opacity seamlessly (e.g., `text-texto-claro/60` for 60% opacity, `bg-red-500/5` for light red). Avoid old syntax like `opacity-60`.
   - **@source Directive:** The CSS already defines `@source` paths that scan Vue components and Blade files. No additional config is needed; just use standard Tailwind classes in templates and they will be detected.
4. **Data Fetching & Shared Data:**
   - Inside the `<template>`, fetch global shared Inertia props via `$page.props.<variable>`.
   - Inside the `<script setup>`, import `usePage` from `@inertiajs/vue3` and safely extract properties inside a Vue `computed` property to preserve full reactivity.
5. **Routing:** Utilize the `route()` helper natively inside templates and scripts powered by Ziggy.

---

## 3. Specific Coding Blueprints

### Form File Upload with Drag & Drop
When generating file uploaders, create a native custom container supporting click triggers, dragging files over boundaries, and drop capturing:
- Use a local `ref` flag (e.g., `const limpandoArrastar = ref(false)`) to toggle border highlights reactively during `@dragover`, `@dragleave`, and `@drop`.
- Intercept files before mapping to `useForm`. Validate file list counts (max 10) and size thresholds computed directly from the shared backend prop `page.props.SISTEMA_LIMITE_UPLOAD`.
- Keep the real input element completely hidden using Tailwind's `sr-only` class, triggering it programmatically using template references (`ref="inputArquivo"` -> `$refs.inputArquivo.click()`).

### Input Masking (e.g., RG Brazilian Mask)
When dealing with document typing masks:
- **RG Format:** The standard Brazilian RG format is `XX.XXX.XXX-D` (12 characters total) where the last character `D` is a single check digit that can be numeric or alphabetic (e.g., 'X' or 'V' in some states).
- **Implementation Details:** 
  - Remove all non-alphanumeric characters using `/[^a-zA-Z0-9]/g`.
  - Force uppercase on input using `toUpperCase()`.
  - Restrict the first 8 positions to digits only; accept letters only in the final check-digit position.
  - Apply progressive masking: `00.000.000-A` (2 digits + dot + 3 digits + dot + 3 digits + hyphen + 1 check digit).
  - Always limit final formatted output to exactly 12 characters (not 14).
- **Callback Pattern:** All mask functions follow the pattern: `aplicarMascaraRG(valor, callback)` where callback receives the formatted string. Example: `@input="sistemajs.aplicarMascaraRG($event.target.value, (val) => { form.rg = val; $event.target.value = val; })"`.
- **Frontend Storage:** Store the unformatted (raw numeric) version in your form state if backend validation requires it; display the formatted version in the input field for UX.
- **Available Masks:** The project includes pre-built mask functions in `resources/js/sistema.js`:
  - `aplicarMascaraCPF()` - CPF format: `000.000.000-00` (14 chars)
  - `aplicarMascaraCNPJ()` - CNPJ format: `00.000.000/0000-00` (18 chars)
  - `aplicarMascaraRG()` - RG format: `00.000.000-0` (12 chars)
  - `aplicarMascaraCEP()` - ZIP code: `00.000-000` (10 chars)
  - `aplicarMascaraTelefone()` - Phone: `(00) 00000-0000` or `(00) 0000-0000`
  - `aplicarMascaraData()` - Date: `DD/MM/YYYY`
  - `aplicarMascaraMonetaria()` - Currency: `R$ X.XXX,XX`
  - `aplicarMascaraCartao()` - Credit card: `0000 0000 0000 0000`
  - `aplicarMascaraIE()` - State registration: `000.000.000.000`
  - `aplicarMascaraIM()` - Municipal registration: `000.000.000-0`

---

## 4. Backend Form Request Validation

When creating file uploads or complex form submissions, always use Form Request classes in `app/Http/Requests`:

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'document' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx',
                'max:10240',  // 10MB in KB
                'dimensions:min_width=100,min_height=100'  // For images
            ],
            'rg' => ['required', 'string', 'regex:/^\d{2}\.\d{3}\.\d{3}-[\dXxVv]$/'],
            'cpf' => ['required', 'string', 'regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/'],
        ];
    }

    public function messages()
    {
        return [
            'document.mimes' => 'Document must be a PDF or image file.',
            'rg.regex' => 'RG format must be XX.XXX.XXX-D',
        ];
    }
}
```

Then in controller:

```php
public function store(StoreDocumentRequest $request)
{
    $file = $request->file('document');
    $filename = date('Y_m_d_H_i_s') . '_' . Str::uuid() . '.' . strtolower($file->extension());
    
    Storage::disk('public')->putFileAs('documents', $file, $filename);
    
    return redirect()->back()->with('success', 'Document uploaded successfully.');
}
```

---

## 5. Error Handling & User Feedback

- **Backend to Frontend:** Use Inertia's automatic error handling. Validation errors are automatically passed to the frontend in the form state (`form.errors`).
- **Global Errors:** For critical errors (authentication, server errors), share error messages through the Inertia middleware and access via `$page.props.errors` or `$page.props.message`.
- **Form Errors Display:** In Vue components, display errors directly from the form state:
  ```vue
  <input v-model="form.rg" />
  <span v-if="form.errors.rg" class="text-red-500 text-sm">{{ form.errors.rg }}</span>
  ```
- **Loading State:** Use Inertia's `form.processing` to disable buttons and show spinners during submission:
  ```vue
  <button :disabled="form.processing">Upload</button>
  ```

---

## 6. Environment & Configuration

- **Backend Configuration:** Key app settings are in `config/app.php`. Custom settings should be added here and injected via the Inertia middleware's `share()` method.
- **Frontend Environment:** The `.env` file contains `VITE_*` variables that are exposed to frontend. Access them via `import.meta.env.VITE_YOUR_VAR`.
- **Global Props:** Always inject frequently-used backend constants (upload limits, API endpoints, permissions) through Inertia's shared props to avoid hardcoding in components.

---

## 7. Debugging & Development Best Practices

- **Backend Debugging:** Use `Log::info()` or `Log::error()` from `Illuminate\Support\Facades\Log`. Check logs in `storage/logs/`. Use `dd()` only in local development, never in production.
- **Frontend Debugging:** Use Vue DevTools browser extension to inspect component state, Inertia props, and reactivity. Open browser DevTools console to inspect JavaScript errors.
- **Development Server:** Run `npm run dev` alongside `php artisan serve`. The Vite dev server provides fast HMR (Hot Module Replacement) for instant CSS and component updates.
- **Composable Pattern:** For shared Vue logic across components, create composables in `resources/js/composables/` using `<script setup>` and export functions/refs that components consume.
- **API Routes:** For AJAX endpoints (non-Inertia responses), define them in `routes/api.php` and return JSON with consistent structure: `{ 'success': true, 'data': {...}, 'message': '...' }`.

---

## 8. Common Patterns to Avoid

- ❌ Do NOT mix Options API with `<script setup>` in the same component.
- ❌ Do NOT store sensitive data (passwords, tokens) in localStorage without encryption.
- ❌ Do NOT bypass Laravel's file storage API; always use `Storage::` facade.
- ❌ Do NOT hardcode color values in Tailwind classes; always use semantic tokens from `@theme`.
- ❌ Do NOT return Blade views from controllers that should be Inertia pages; always use `Inertia::render()`.
- ❌ Do NOT validate files only on the frontend; always validate on the backend with Form Request classes.
- ❌ Do NOT use `v-show` for sensitive UI; prefer `v-if` to prevent DOM exposure of private content.

---

## 9. Quick Reference: Tech Stack Dependencies

- **PHP:** 8.2+ with ext-json, ext-mbstring
- **Laravel:** 12.x with Inertia Adapter 3.1+
- **Node.js:** 18+ or 20+ (compatible with Vite 8)
- **Vue:** 3.5+ with Composition API
- **Tailwind CSS:** 4.0+ with v4 CSS-first syntax
- **Vite:** 8.x as bundler
- **Database:** See `config/database.php` for connection setup (likely SQLite for local dev, MySQL/Postgres for production)