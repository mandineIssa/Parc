<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Gestion Parc Informatique') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */
                @layer theme {
                    :root, :host {
                        --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                        --font-serif: ui-serif, Georgia, Cambria, "Times New Roman", Times, serif;
                        --font-mono: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
                        --color-blue-50: oklch(.97 .014 254.604);
                        --color-blue-100: oklch(.932 .032 255.585);
                        --color-blue-200: oklch(.882 .059 254.128);
                        --color-blue-300: oklch(.809 .105 251.813);
                        --color-blue-400: oklch(.707 .165 254.624);
                        --color-blue-500: oklch(.623 .214 259.815);
                        --color-blue-600: oklch(.546 .245 262.881);
                        --color-blue-700: oklch(.488 .243 264.376);
                        --color-blue-800: oklch(.424 .199 265.638);
                        --color-blue-900: oklch(.379 .146 265.522);
                        --color-blue-950: oklch(.282 .091 267.935);
                        --color-green-500: oklch(.723 .219 149.579);
                        --color-green-600: oklch(.627 .194 149.214);
                        --color-gray-50: oklch(.985 .002 247.839);
                        --color-gray-100: oklch(.967 .003 264.542);
                        --color-gray-200: oklch(.928 .006 264.531);
                        --color-gray-300: oklch(.872 .01 258.338);
                        --color-gray-400: oklch(.707 .022 261.325);
                        --color-gray-500: oklch(.551 .027 264.364);
                        --color-gray-600: oklch(.446 .03 256.802);
                        --color-gray-700: oklch(.373 .034 259.733);
                        --color-gray-800: oklch(.278 .033 256.848);
                        --color-gray-900: oklch(.21 .034 264.665);
                        --color-gray-950: oklch(.13 .028 261.692);
                        --color-black: #000;
                        --color-white: #fff;
                        --spacing: .25rem;
                        --breakpoint-sm: 40rem;
                        --breakpoint-md: 48rem;
                        --breakpoint-lg: 64rem;
                        --breakpoint-xl: 80rem;
                        --breakpoint-2xl: 96rem;
                        --text-xs: .75rem;
                        --text-xs--line-height: calc(1/.75);
                        --text-sm: .875rem;
                        --text-sm--line-height: calc(1.25/.875);
                        --text-base: 1rem;
                        --text-base--line-height: 1.5;
                        --text-lg: 1.125rem;
                        --text-lg--line-height: calc(1.75/1.125);
                        --text-xl: 1.25rem;
                        --text-xl--line-height: calc(1.75/1.25);
                        --text-2xl: 1.5rem;
                        --text-2xl--line-height: calc(2/1.5);
                        --text-3xl: 1.875rem;
                        --text-3xl--line-height: 1.2;
                        --text-4xl: 2.25rem;
                        --text-4xl--line-height: calc(2.5/2.25);
                        --font-weight-medium: 500;
                        --font-weight-semibold: 600;
                        --font-weight-bold: 700;
                        --radius-sm: .25rem;
                        --radius-lg: .5rem;
                        --radius-xl: .75rem;
                        --shadow-sm: 0 1px 3px 0 #0000001a, 0 1px 2px -1px #0000001a;
                        --shadow-md: 0 4px 6px -1px #0000001a, 0 2px 4px -2px #0000001a;
                        --shadow-lg: 0 10px 15px -3px #0000001a, 0 4px 6px -4px #0000001a;
                        --default-transition-duration: .15s;
                        --default-transition-timing-function: cubic-bezier(.4,0,.2,1);
                    }
                }
                
                @layer base {
                    *, :after, :before, ::backdrop {
                        box-sizing: border-box;
                        border: 0 solid;
                        margin: 0;
                        padding: 0;
                    }
                    
                    html, :host {
                        font-family: var(--font-sans);
                        line-height: 1.5;
                    }
                    
                    body {
                        line-height: inherit;
                    }
                    
                    h1, h2, h3, h4, h5, h6 {
                        font-size: inherit;
                        font-weight: inherit;
                    }
                }
                
                @layer utilities {
                    .absolute { position: absolute; }
                    .relative { position: relative; }
                    .inset-0 { inset: 0; }
                    .top-0 { top: 0; }
                    .bottom-0 { bottom: 0; }
                    .left-0 { left: 0; }
                    .right-0 { right: 0; }
                    .z-10 { z-index: 10; }
                    .z-20 { z-index: 20; }
                    
                    .flex { display: flex; }
                    .hidden { display: none; }
                    .block { display: block; }
                    .inline-flex { display: inline-flex; }
                    .flex-col { flex-direction: column; }
                    .flex-row { flex-direction: row; }
                    .flex-col-reverse { flex-direction: column-reverse; }
                    .flex-wrap { flex-wrap: wrap; }
                    .items-center { align-items: center; }
                    .items-start { align-items: flex-start; }
                    .items-end { align-items: flex-end; }
                    .justify-center { justify-content: center; }
                    .justify-between { justify-content: space-between; }
                    .justify-end { justify-content: flex-end; }
                    .flex-1 { flex: 1; }
                    .flex-shrink-0 { flex-shrink: 0; }
                    .flex-grow { flex-grow: 1; }
                    
                    .w-full { width: 100%; }
                    .w-auto { width: auto; }
                    .w-6 { width: 1.5rem; }
                    .w-8 { width: 2rem; }
                    .w-12 { width: 3rem; }
                    .w-16 { width: 4rem; }
                    .w-64 { width: 16rem; }
                    .w-80 { width: 20rem; }
                    .w-96 { width: 24rem; }
                    .w-\[448px\] { width: 448px; }
                    .max-w-\[335px\] { max-width: 335px; }
                    .max-w-sm { max-width: 24rem; }
                    .max-w-md { max-width: 28rem; }
                    .max-w-lg { max-width: 32rem; }
                    .max-w-xl { max-width: 36rem; }
                    .max-w-2xl { max-width: 42rem; }
                    .max-w-3xl { max-width: 48rem; }
                    .max-w-4xl { max-width: 56rem; }
                    .max-w-5xl { max-width: 64rem; }
                    .max-w-6xl { max-width: 72rem; }
                    .max-w-7xl { max-width: 80rem; }
                    .max-w-none { max-width: none; }
                    
                    .h-6 { height: 1.5rem; }
                    .h-8 { height: 2rem; }
                    .h-12 { height: 3rem; }
                    .h-16 { height: 4rem; }
                    .h-20 { height: 5rem; }
                    .h-24 { height: 6rem; }
                    .h-32 { height: 8rem; }
                    .h-48 { height: 12rem; }
                    .h-64 { height: 16rem; }
                    .h-96 { height: 24rem; }
                    .h-auto { height: auto; }
                    .h-full { height: 100%; }
                    .h-screen { height: 100vh; }
                    .min-h-screen { min-height: 100vh; }
                    
                    .p-2 { padding: 0.5rem; }
                    .p-3 { padding: 0.75rem; }
                    .p-4 { padding: 1rem; }
                    .p-5 { padding: 1.25rem; }
                    .p-6 { padding: 1.5rem; }
                    .p-8 { padding: 2rem; }
                    .p-12 { padding: 3rem; }
                    .p-16 { padding: 4rem; }
                    .p-20 { padding: 5rem; }
                    .px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
                    .px-4 { padding-left: 1rem; padding-right: 1rem; }
                    .px-5 { padding-left: 1.25rem; padding-right: 1.25rem; }
                    .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
                    .px-8 { padding-left: 2rem; padding-right: 2rem; }
                    .py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
                    .py-1\.5 { padding-top: 0.375rem; padding-bottom: 0.375rem; }
                    .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
                    .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
                    .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
                    .py-6 { padding-top: 1.5rem; padding-bottom: 1.5rem; }
                    .py-8 { padding-top: 2rem; padding-bottom: 2rem; }
                    .py-12 { padding-top: 3rem; padding-bottom: 3rem; }
                    .py-16 { padding-top: 4rem; padding-bottom: 4rem; }
                    .pt-4 { padding-top: 1rem; }
                    .pt-6 { padding-top: 1.5rem; }
                    .pt-8 { padding-top: 2rem; }
                    .pt-12 { padding-top: 3rem; }
                    .pt-16 { padding-top: 4rem; }
                    .pb-4 { padding-bottom: 1rem; }
                    .pb-6 { padding-bottom: 1.5rem; }
                    .pb-8 { padding-bottom: 2rem; }
                    .pb-12 { padding-bottom: 3rem; }
                    .pb-16 { padding-bottom: 4rem; }
                    .pl-4 { padding-left: 1rem; }
                    .pl-6 { padding-left: 1.5rem; }
                    .pl-8 { padding-left: 2rem; }
                    .pr-4 { padding-right: 1rem; }
                    .pr-6 { padding-right: 1.5rem; }
                    .pr-8 { padding-right: 2rem; }
                    
                    .m-0 { margin: 0; }
                    .m-4 { margin: 1rem; }
                    .m-auto { margin: auto; }
                    .mx-0 { margin-left: 0; margin-right: 0; }
                    .mx-auto { margin-left: auto; margin-right: auto; }
                    .mx-2 { margin-left: 0.5rem; margin-right: 0.5rem; }
                    .mx-4 { margin-left: 1rem; margin-right: 1rem; }
                    .mx-6 { margin-left: 1.5rem; margin-right: 1.5rem; }
                    .mx-8 { margin-left: 2rem; margin-right: 2rem; }
                    .my-0 { margin-top: 0; margin-bottom: 0; }
                    .my-2 { margin-top: 0.5rem; margin-bottom: 0.5rem; }
                    .my-4 { margin-top: 1rem; margin-bottom: 1rem; }
                    .my-6 { margin-top: 1.5rem; margin-bottom: 1.5rem; }
                    .my-8 { margin-top: 2rem; margin-bottom: 2rem; }
                    .my-12 { margin-top: 3rem; margin-bottom: 3rem; }
                    .mt-0 { margin-top: 0; }
                    .mt-1 { margin-top: 0.25rem; }
                    .mt-2 { margin-top: 0.5rem; }
                    .mt-3 { margin-top: 0.75rem; }
                    .mt-4 { margin-top: 1rem; }
                    .mt-6 { margin-top: 1.5rem; }
                    .mt-8 { margin-top: 2rem; }
                    .mt-12 { margin-top: 3rem; }
                    .mt-16 { margin-top: 4rem; }
                    .mt-24 { margin-top: 6rem; }
                    .mb-0 { margin-bottom: 0; }
                    .mb-1 { margin-bottom: 0.25rem; }
                    .mb-2 { margin-bottom: 0.5rem; }
                    .mb-3 { margin-bottom: 0.75rem; }
                    .mb-4 { margin-bottom: 1rem; }
                    .mb-6 { margin-bottom: 1.5rem; }
                    .mb-8 { margin-bottom: 2rem; }
                    .mb-12 { margin-bottom: 3rem; }
                    .mb-16 { margin-bottom: 4rem; }
                    .mb-24 { margin-bottom: 6rem; }
                    .ml-0 { margin-left: 0; }
                    .ml-1 { margin-left: 0.25rem; }
                    .ml-2 { margin-left: 0.5rem; }
                    .ml-3 { margin-left: 0.75rem; }
                    .ml-4 { margin-left: 1rem; }
                    .ml-6 { margin-left: 1.5rem; }
                    .ml-8 { margin-left: 2rem; }
                    .ml-12 { margin-left: 3rem; }
                    .ml-auto { margin-left: auto; }
                    .mr-0 { margin-right: 0; }
                    .mr-1 { margin-right: 0.25rem; }
                    .mr-2 { margin-right: 0.5rem; }
                    .mr-3 { margin-right: 0.75rem; }
                    .mr-4 { margin-right: 1rem; }
                    .mr-6 { margin-right: 1.5rem; }
                    .mr-8 { margin-right: 2rem; }
                    .mr-12 { margin-right: 3rem; }
                    .mr-auto { margin-right: auto; }
                    .-mt-4 { margin-top: -1rem; }
                    .-mt-8 { margin-top: -2rem; }
                    .-mt-12 { margin-top: -3rem; }
                    .-ml-2 { margin-left: -0.5rem; }
                    .-ml-4 { margin-left: -1rem; }
                    .-ml-8 { margin-left: -2rem; }
                    .-mr-2 { margin-right: -0.5rem; }
                    .-mr-4 { margin-right: -1rem; }
                    .-mr-8 { margin-right: -2rem; }
                    
                    .space-y-2 > * + * { margin-top: 0.5rem; }
                    .space-y-3 > * + * { margin-top: 0.75rem; }
                    .space-y-4 > * + * { margin-top: 1rem; }
                    .space-y-6 > * + * { margin-top: 1.5rem; }
                    .space-y-8 > * + * { margin-top: 2rem; }
                    .space-x-2 > * + * { margin-left: 0.5rem; }
                    .space-x-3 > * + * { margin-left: 0.75rem; }
                    .space-x-4 > * + * { margin-left: 1rem; }
                    .space-x-6 > * + * { margin-left: 1.5rem; }
                    .space-x-8 > * + * { margin-left: 2rem; }
                    
                    .gap-2 { gap: 0.5rem; }
                    .gap-3 { gap: 0.75rem; }
                    .gap-4 { gap: 1rem; }
                    .gap-6 { gap: 1.5rem; }
                    .gap-8 { gap: 2rem; }
                    
                    .overflow-hidden { overflow: hidden; }
                    .overflow-auto { overflow: auto; }
                    .overflow-x-auto { overflow-x: auto; }
                    .overflow-y-auto { overflow-y: auto; }
                    .overflow-x-hidden { overflow-x: hidden; }
                    .overflow-y-hidden { overflow-y: hidden; }
                    
                    .rounded { border-radius: 0.25rem; }
                    .rounded-sm { border-radius: var(--radius-sm); }
                    .rounded-md { border-radius: 0.375rem; }
                    .rounded-lg { border-radius: var(--radius-lg); }
                    .rounded-xl { border-radius: var(--radius-xl); }
                    .rounded-2xl { border-radius: 1rem; }
                    .rounded-3xl { border-radius: 1.5rem; }
                    .rounded-full { border-radius: 9999px; }
                    .rounded-t-lg { border-top-left-radius: var(--radius-lg); border-top-right-radius: var(--radius-lg); }
                    .rounded-b-lg { border-bottom-left-radius: var(--radius-lg); border-bottom-right-radius: var(--radius-lg); }
                    .rounded-t-xl { border-top-left-radius: var(--radius-xl); border-top-right-radius: var(--radius-xl); }
                    .rounded-b-xl { border-bottom-left-radius: var(--radius-xl); border-bottom-right-radius: var(--radius-xl); }
                    .rounded-l-lg { border-top-left-radius: var(--radius-lg); border-bottom-left-radius: var(--radius-lg); }
                    .rounded-r-lg { border-top-right-radius: var(--radius-lg); border-bottom-right-radius: var(--radius-lg); }
                    .rounded-bl-lg { border-bottom-left-radius: var(--radius-lg); }
                    .rounded-br-lg { border-bottom-right-radius: var(--radius-lg); }
                    .rounded-tl-lg { border-top-left-radius: var(--radius-lg); }
                    .rounded-tr-lg { border-top-right-radius: var(--radius-lg); }
                    
                    .border { border-width: 1px; }
                    .border-0 { border-width: 0; }
                    .border-2 { border-width: 2px; }
                    .border-4 { border-width: 4px; }
                    .border-t { border-top-width: 1px; }
                    .border-b { border-bottom-width: 1px; }
                    .border-l { border-left-width: 1px; }
                    .border-r { border-right-width: 1px; }
                    .border-solid { border-style: solid; }
                    .border-dashed { border-style: dashed; }
                    .border-dotted { border-style: dotted; }
                    .border-none { border-style: none; }
                    .border-transparent { border-color: transparent; }
                    .border-white { border-color: var(--color-white); }
                    .border-black { border-color: var(--color-black); }
                    .border-gray-100 { border-color: var(--color-gray-100); }
                    .border-gray-200 { border-color: var(--color-gray-200); }
                    .border-gray-300 { border-color: var(--color-gray-300); }
                    .border-gray-400 { border-color: var(--color-gray-400); }
                    .border-gray-500 { border-color: var(--color-gray-500); }
                    .border-gray-600 { border-color: var(--color-gray-600); }
                    .border-gray-700 { border-color: var(--color-gray-700); }
                    .border-gray-800 { border-color: var(--color-gray-800); }
                    .border-blue-100 { border-color: var(--color-blue-100); }
                    .border-blue-200 { border-color: var(--color-blue-200); }
                    .border-blue-300 { border-color: var(--color-blue-300); }
                    .border-blue-400 { border-color: var(--color-blue-400); }
                    .border-blue-500 { border-color: var(--color-blue-500); }
                    .border-blue-600 { border-color: var(--color-blue-600); }
                    .border-blue-700 { border-color: var(--color-blue-700); }
                    .border-blue-800 { border-color: var(--color-blue-800); }
                    .border-green-500 { border-color: var(--color-green-500); }
                    .border-green-600 { border-color: var(--color-green-600); }
                    
                    .bg-transparent { background-color: transparent; }
                    .bg-white { background-color: var(--color-white); }
                    .bg-black { background-color: var(--color-black); }
                    .bg-gray-50 { background-color: var(--color-gray-50); }
                    .bg-gray-100 { background-color: var(--color-gray-100); }
                    .bg-gray-200 { background-color: var(--color-gray-200); }
                    .bg-gray-300 { background-color: var(--color-gray-300); }
                    .bg-gray-400 { background-color: var(--color-gray-400); }
                    .bg-gray-500 { background-color: var(--color-gray-500); }
                    .bg-gray-600 { background-color: var(--color-gray-600); }
                    .bg-gray-700 { background-color: var(--color-gray-700); }
                    .bg-gray-800 { background-color: var(--color-gray-800); }
                    .bg-gray-900 { background-color: var(--color-gray-900); }
                    .bg-blue-50 { background-color: var(--color-blue-50); }
                    .bg-blue-100 { background-color: var(--color-blue-100); }
                    .bg-blue-200 { background-color: var(--color-blue-200); }
                    .bg-blue-300 { background-color: var(--color-blue-300); }
                    .bg-blue-400 { background-color: var(--color-blue-400); }
                    .bg-blue-500 { background-color: var(--color-blue-500); }
                    .bg-blue-600 { background-color: var(--color-blue-600); }
                    .bg-blue-700 { background-color: var(--color-blue-700); }
                    .bg-blue-800 { background-color: var(--color-blue-800); }
                    .bg-blue-900 { background-color: var(--color-blue-900); }
                    .bg-green-50 { background-color: oklch(.982 .018 155.826); }
                    .bg-green-100 { background-color: oklch(.962 .044 156.743); }
                    .bg-green-500 { background-color: var(--color-green-500); }
                    .bg-green-600 { background-color: var(--color-green-600); }
                    .bg-gradient-to-r { background-image: linear-gradient(to right, var(--tw-gradient-stops)); }
                    .bg-gradient-to-br { background-image: linear-gradient(to bottom right, var(--tw-gradient-stops)); }
                    .from-blue-500 { --tw-gradient-from: var(--color-blue-500); }
                    .to-blue-600 { --tw-gradient-to: var(--color-blue-600); }
                    .from-blue-600 { --tw-gradient-from: var(--color-blue-600); }
                    .to-blue-700 { --tw-gradient-to: oklch(.488 .243 264.376); }
                    
                    .text-xs { font-size: var(--text-xs); line-height: var(--text-xs--line-height); }
                    .text-sm { font-size: var(--text-sm); line-height: var(--text-sm--line-height); }
                    .text-base { font-size: var(--text-base); line-height: var(--text-base--line-height); }
                    .text-lg { font-size: var(--text-lg); line-height: var(--text-lg--line-height); }
                    .text-xl { font-size: var(--text-xl); line-height: var(--text-xl--line-height); }
                    .text-2xl { font-size: var(--text-2xl); line-height: var(--text-2xl--line-height); }
                    .text-3xl { font-size: var(--text-3xl); line-height: var(--text-3xl--line-height); }
                    .text-4xl { font-size: var(--text-4xl); line-height: var(--text-4xl--line-height); }
                    .text-5xl { font-size: 3rem; line-height: 1; }
                    .text-6xl { font-size: 3.75rem; line-height: 1; }
                    .font-light { font-weight: 300; }
                    .font-normal { font-weight: 400; }
                    .font-medium { font-weight: var(--font-weight-medium); }
                    .font-semibold { font-weight: var(--font-weight-semibold); }
                    .font-bold { font-weight: var(--font-weight-bold); }
                    .leading-tight { line-height: 1.25; }
                    .leading-normal { line-height: 1.5; }
                    .leading-relaxed { line-height: 1.625; }
                    .leading-loose { line-height: 2; }
                    
                    .text-white { color: var(--color-white); }
                    .text-black { color: var(--color-black); }
                    .text-gray-50 { color: var(--color-gray-50); }
                    .text-gray-100 { color: var(--color-gray-100); }
                    .text-gray-200 { color: var(--color-gray-200); }
                    .text-gray-300 { color: var(--color-gray-300); }
                    .text-gray-400 { color: var(--color-gray-400); }
                    .text-gray-500 { color: var(--color-gray-500); }
                    .text-gray-600 { color: var(--color-gray-600); }
                    .text-gray-700 { color: var(--color-gray-700); }
                    .text-gray-800 { color: var(--color-gray-800); }
                    .text-gray-900 { color: var(--color-gray-900); }
                    .text-blue-50 { color: var(--color-blue-50); }
                    .text-blue-100 { color: var(--color-blue-100); }
                    .text-blue-200 { color: var(--color-blue-200); }
                    .text-blue-300 { color: var(--color-blue-300); }
                    .text-blue-400 { color: var(--color-blue-400); }
                    .text-blue-500 { color: var(--color-blue-500); }
                    .text-blue-600 { color: var(--color-blue-600); }
                    .text-blue-700 { color: var(--color-blue-700); }
                    .text-blue-800 { color: var(--color-blue-800); }
                    .text-blue-900 { color: var(--color-blue-900); }
                    .text-green-500 { color: var(--color-green-500); }
                    .text-green-600 { color: var(--color-green-600); }
                    
                    .text-center { text-align: center; }
                    .text-left { text-align: left; }
                    .text-right { text-align: right; }
                    .text-justify { text-align: justify; }
                    
                    .uppercase { text-transform: uppercase; }
                    .lowercase { text-transform: lowercase; }
                    .capitalize { text-transform: capitalize; }
                    
                    .underline { text-decoration-line: underline; }
                    .line-through { text-decoration-line: line-through; }
                    .no-underline { text-decoration-line: none; }
                    .underline-offset-2 { text-underline-offset: 2px; }
                    .underline-offset-4 { text-underline-offset: 4px; }
                    
                    .tracking-tight { letter-spacing: -0.025em; }
                    .tracking-normal { letter-spacing: 0em; }
                    .tracking-wide { letter-spacing: 0.025em; }
                    .tracking-wider { letter-spacing: 0.05em; }
                    .tracking-widest { letter-spacing: 0.1em; }
                    
                    .opacity-0 { opacity: 0; }
                    .opacity-25 { opacity: 0.25; }
                    .opacity-50 { opacity: 0.5; }
                    .opacity-75 { opacity: 0.75; }
                    .opacity-100 { opacity: 1; }
                    
                    .shadow-sm { box-shadow: var(--shadow-sm); }
                    .shadow-md { box-shadow: var(--shadow-md); }
                    .shadow-lg { box-shadow: var(--shadow-lg); }
                    .shadow-xl { box-shadow: 0 20px 25px -5px #0000001a, 0 8px 10px -6px #0000001a; }
                    .shadow-2xl { box-shadow: 0 25px 50px -12px #00000040; }
                    .shadow-inner { box-shadow: inset 0 2px 4px 0 #0000000d; }
                    .shadow-none { box-shadow: none; }
                    
                    .transition { transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter; transition-timing-function: var(--default-transition-timing-function); transition-duration: var(--default-transition-duration); }
                    .transition-all { transition-property: all; transition-timing-function: var(--default-transition-timing-function); transition-duration: var(--default-transition-duration); }
                    .transition-colors { transition-property: color, background-color, border-color, text-decoration-color, fill, stroke; transition-timing-function: var(--default-transition-timing-function); transition-duration: var(--default-transition-duration); }
                    .transition-opacity { transition-property: opacity; transition-timing-function: var(--default-transition-timing-function); transition-duration: var(--default-transition-duration); }
                    .transition-transform { transition-property: transform; transition-timing-function: var(--default-transition-timing-function); transition-duration: var(--default-transition-duration); }
                    .duration-75 { transition-duration: 75ms; }
                    .duration-100 { transition-duration: 100ms; }
                    .duration-150 { transition-duration: 150ms; }
                    .duration-200 { transition-duration: 200ms; }
                    .duration-300 { transition-duration: 300ms; }
                    .duration-500 { transition-duration: 500ms; }
                    .duration-700 { transition-duration: 700ms; }
                    .duration-1000 { transition-duration: 1000ms; }
                    .ease-in { transition-timing-function: cubic-bezier(0.4, 0, 1, 1); }
                    .ease-out { transition-timing-function: cubic-bezier(0, 0, 0.2, 1); }
                    .ease-in-out { transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); }
                    .delay-75 { transition-delay: 75ms; }
                    .delay-100 { transition-delay: 100ms; }
                    .delay-150 { transition-delay: 150ms; }
                    .delay-200 { transition-delay: 200ms; }
                    .delay-300 { transition-delay: 300ms; }
                    .delay-500 { transition-delay: 500ms; }
                    .delay-700 { transition-delay: 700ms; }
                    .delay-1000 { transition-delay: 1000ms; }
                    
                    .transform { transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y)); }
                    .translate-y-0 { --tw-translate-y: 0px; }
                    .translate-y-1 { --tw-translate-y: 0.25rem; }
                    .translate-y-2 { --tw-translate-y: 0.5rem; }
                    .translate-y-4 { --tw-translate-y: 1rem; }
                    .translate-y-6 { --tw-translate-y: 1.5rem; }
                    .-translate-y-1 { --tw-translate-y: -0.25rem; }
                    .-translate-y-2 { --tw-translate-y: -0.5rem; }
                    .-translate-y-4 { --tw-translate-y: -1rem; }
                    .-translate-y-6 { --tw-translate-y: -1.5rem; }
                    .translate-x-0 { --tw-translate-x: 0px; }
                    .translate-x-1 { --tw-translate-x: 0.25rem; }
                    .translate-x-2 { --tw-translate-x: 0.5rem; }
                    .translate-x-4 { --tw-translate-x: 1rem; }
                    .translate-x-6 { --tw-translate-x: 1.5rem; }
                    .-translate-x-1 { --tw-translate-x: -0.25rem; }
                    .-translate-x-2 { --tw-translate-x: -0.5rem; }
                    .-translate-x-4 { --tw-translate-x: -1rem; }
                    .-translate-x-6 { --tw-translate-x: -1.5rem; }
                    .scale-95 { --tw-scale-x: .95; --tw-scale-y: .95; }
                    .scale-100 { --tw-scale-x: 1; --tw-scale-y: 1; }
                    .scale-105 { --tw-scale-x: 1.05; --tw-scale-y: 1.05; }
                    .scale-110 { --tw-scale-x: 1.1; --tw-scale-y: 1.1; }
                    .scale-125 { --tw-scale-x: 1.25; --tw-scale-y: 1.25; }
                    .rotate-0 { --tw-rotate: 0deg; }
                    .rotate-45 { --tw-rotate: 45deg; }
                    .rotate-90 { --tw-rotate: 90deg; }
                    .rotate-180 { --tw-rotate: 180deg; }
                    .-rotate-45 { --tw-rotate: -45deg; }
                    .-rotate-90 { --tw-rotate: -90deg; }
                    .-rotate-180 { --tw-rotate: -180deg; }
                    
                    .hover\:bg-blue-600:hover { background-color: var(--color-blue-600); }
                    .hover\:bg-blue-700:hover { background-color: var(--color-blue-700); }
                    .hover\:bg-gray-50:hover { background-color: var(--color-gray-50); }
                    .hover\:bg-gray-100:hover { background-color: var(--color-gray-100); }
                    .hover\:text-blue-600:hover { color: var(--color-blue-600); }
                    .hover\:text-blue-700:hover { color: var(--color-blue-700); }
                    .hover\:text-gray-900:hover { color: var(--color-gray-900); }
                    .hover\:scale-105:hover { --tw-scale-x: 1.05; --tw-scale-y: 1.05; }
                    .hover\:scale-110:hover { --tw-scale-x: 1.1; --tw-scale-y: 1.1; }
                    .hover\:shadow-lg:hover { box-shadow: var(--shadow-lg); }
                    .hover\:shadow-xl:hover { box-shadow: 0 20px 25px -5px #0000001a, 0 8px 10px -6px #0000001a; }
                    .hover\:shadow-2xl:hover { box-shadow: 0 25px 50px -12px #00000040; }
                    .hover\:border-blue-300:hover { border-color: var(--color-blue-300); }
                    .hover\:border-blue-500:hover { border-color: var(--color-blue-500); }
                    .hover\:border-gray-300:hover { border-color: var(--color-gray-300); }
                    .hover\:translate-y-1:hover { --tw-translate-y: 0.25rem; }
                    .hover\:-translate-y-1:hover { --tw-translate-y: -0.25rem; }
                    
                    .focus\:outline-none:focus { outline: 2px solid transparent; outline-offset: 2px; }
                    .focus\:ring-2:focus { --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color); --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color); box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000); }
                    .focus\:ring-blue-500:focus { --tw-ring-color: var(--color-blue-500); }
                    .focus\:ring-gray-500:focus { --tw-ring-color: var(--color-gray-500); }
                    .focus\:border-blue-500:focus { border-color: var(--color-blue-500); }
                    .focus\:border-gray-500:focus { border-color: var(--color-gray-500); }
                    
                    .active\:scale-95:active { --tw-scale-x: .95; --tw-scale-y: .95; }
                    .active\:bg-blue-700:active { background-color: var(--color-blue-700); }
                    
                    .disabled\:opacity-50:disabled { opacity: 0.5; }
                    .disabled\:cursor-not-allowed:disabled { cursor: not-allowed; }
                    
                    .group:hover .group-hover\:scale-105 { --tw-scale-x: 1.05; --tw-scale-y: 1.05; }
                    .group:hover .group-hover\:text-blue-600 { color: var(--color-blue-600); }
                    .group:hover .group-hover\:bg-blue-50 { background-color: var(--color-blue-50); }
                    
                    @media (min-width: 640px) {
                        .sm\:flex { display: flex; }
                        .sm\:hidden { display: none; }
                        .sm\:text-sm { font-size: var(--text-sm); }
                        .sm\:text-lg { font-size: var(--text-lg); }
                        .sm\:text-xl { font-size: var(--text-xl); }
                        .sm\:text-2xl { font-size: var(--text-2xl); }
                        .sm\:text-3xl { font-size: var(--text-3xl); }
                        .sm\:text-4xl { font-size: var(--text-4xl); }
                        .sm\:px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
                        .sm\:px-8 { padding-left: 2rem; padding-right: 2rem; }
                        .sm\:py-8 { padding-top: 2rem; padding-bottom: 2rem; }
                        .sm\:py-12 { padding-top: 3rem; padding-bottom: 3rem; }
                        .sm\:py-16 { padding-top: 4rem; padding-bottom: 4rem; }
                        .sm\:pt-8 { padding-top: 2rem; }
                        .sm\:pt-12 { padding-top: 3rem; }
                        .sm\:pt-16 { padding-top: 4rem; }
                        .sm\:pb-8 { padding-bottom: 2rem; }
                        .sm\:pb-12 { padding-bottom: 3rem; }
                        .sm\:pb-16 { padding-bottom: 4rem; }
                    }
                    
                    @media (min-width: 768px) {
                        .md\:flex { display: flex; }
                        .md\:hidden { display: none; }
                        .md\:block { display: block; }
                        .md\:grid { display: grid; }
                        .md\:flex-row { flex-direction: row; }
                        .md\:flex-col { flex-direction: column; }
                        .md\:items-center { align-items: center; }
                        .md\:justify-between { justify-content: space-between; }
                        .md\:space-x-4 > * + * { margin-left: 1rem; }
                        .md\:space-y-0 > * + * { margin-top: 0; }
                        .md\:w-1\/2 { width: 50%; }
                        .md\:w-1\/3 { width: 33.333333%; }
                        .md\:w-2\/3 { width: 66.666667%; }
                        .md\:w-1\/4 { width: 25%; }
                        .md\:w-3\/4 { width: 75%; }
                        .md\:w-auto { width: auto; }
                        .md\:max-w-md { max-width: 28rem; }
                        .md\:max-w-lg { max-width: 32rem; }
                        .md\:max-w-xl { max-width: 36rem; }
                        .md\:max-w-2xl { max-width: 42rem; }
                        .md\:max-w-3xl { max-width: 48rem; }
                        .md\:max-w-4xl { max-width: 56rem; }
                        .md\:max-w-5xl { max-width: 64rem; }
                        .md\:max-w-6xl { max-width: 72rem; }
                        .md\:max-w-7xl { max-width: 80rem; }
                        .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                        .md\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
                        .md\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
                        .md\:gap-6 { gap: 1.5rem; }
                        .md\:gap-8 { gap: 2rem; }
                        .md\:p-8 { padding: 2rem; }
                        .md\:p-12 { padding: 3rem; }
                        .md\:p-16 { padding: 4rem; }
                        .md\:px-8 { padding-left: 2rem; padding-right: 2rem; }
                        .md\:px-12 { padding-left: 3rem; padding-right: 3rem; }
                        .md\:px-16 { padding-left: 4rem; padding-right: 4rem; }
                        .md\:py-8 { padding-top: 2rem; padding-bottom: 2rem; }
                        .md\:py-12 { padding-top: 3rem; padding-bottom: 3rem; }
                        .md\:py-16 { padding-top: 4rem; padding-bottom: 4rem; }
                        .md\:py-24 { padding-top: 6rem; padding-bottom: 6rem; }
                        .md\:text-left { text-align: left; }
                        .md\:text-center { text-align: center; }
                        .md\:text-right { text-align: right; }
                        .md\:text-sm { font-size: var(--text-sm); }
                        .md\:text-base { font-size: var(--text-base); }
                        .md\:text-lg { font-size: var(--text-lg); }
                        .md\:text-xl { font-size: var(--text-xl); }
                        .md\:text-2xl { font-size: var(--text-2xl); }
                        .md\:text-3xl { font-size: var(--text-3xl); }
                        .md\:text-4xl { font-size: var(--text-4xl); }
                        .md\:text-5xl { font-size: 3rem; }
                        .md\:text-6xl { font-size: 3.75rem; }
                    }
                    
                    @media (min-width: 1024px) {
                        .lg\:flex { display: flex; }
                        .lg\:hidden { display: none; }
                        .lg\:block { display: block; }
                        .lg\:flex-row { flex-direction: row; }
                        .lg\:flex-col { flex-direction: column; }
                        .lg\:items-center { align-items: center; }
                        .lg\:justify-center { justify-content: center; }
                        .lg\:justify-between { justify-content: space-between; }
                        .lg\:justify-end { justify-content: flex-end; }
                        .lg\:space-x-6 > * + * { margin-left: 1.5rem; }
                        .lg\:space-y-0 > * + * { margin-top: 0; }
                        .lg\:w-1\/2 { width: 50%; }
                        .lg\:w-1\/3 { width: 33.333333%; }
                        .lg\:w-2\/3 { width: 66.666667%; }
                        .lg\:w-1\/4 { width: 25%; }
                        .lg\:w-3\/4 { width: 75%; }
                        .lg\:w-\[438px\] { width: 438px; }
                        .lg\:w-auto { width: auto; }
                        .lg\:max-w-4xl { max-width: var(--container-4xl); }
                        .lg\:max-w-5xl { max-width: 64rem; }
                        .lg\:max-w-6xl { max-width: 72rem; }
                        .lg\:max-w-7xl { max-width: 80rem; }
                        .lg\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                        .lg\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
                        .lg\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
                        .lg\:grid-cols-5 { grid-template-columns: repeat(5, minmax(0, 1fr)); }
                        .lg\:grid-cols-6 { grid-template-columns: repeat(6, minmax(0, 1fr)); }
                        .lg\:gap-8 { gap: 2rem; }
                        .lg\:gap-12 { gap: 3rem; }
                        .lg\:p-8 { padding: 2rem; }
                        .lg\:p-12 { padding: 3rem; }
                        .lg\:p-16 { padding: 4rem; }
                        .lg\:p-20 { padding: 5rem; }
                        .lg\:px-8 { padding-left: 2rem; padding-right: 2rem; }
                        .lg\:px-12 { padding-left: 3rem; padding-right: 3rem; }
                        .lg\:px-16 { padding-left: 4rem; padding-right: 4rem; }
                        .lg\:px-24 { padding-left: 6rem; padding-right: 6rem; }
                        .lg\:py-8 { padding-top: 2rem; padding-bottom: 2rem; }
                        .lg\:py-12 { padding-top: 3rem; padding-bottom: 3rem; }
                        .lg\:py-16 { padding-top: 4rem; padding-bottom: 4rem; }
                        .lg\:py-24 { padding-top: 6rem; padding-bottom: 6rem; }
                        .lg\:py-32 { padding-top: 8rem; padding-bottom: 8rem; }
                        .lg\:pt-8 { padding-top: 2rem; }
                        .lg\:pt-12 { padding-top: 3rem; }
                        .lg\:pt-16 { padding-top: 4rem; }
                        .lg\:pt-24 { padding-top: 6rem; }
                        .lg\:pt-32 { padding-top: 8rem; }
                        .lg\:pb-8 { padding-bottom: 2rem; }
                        .lg\:pb-12 { padding-bottom: 3rem; }
                        .lg\:pb-16 { padding-bottom: 4rem; }
                        .lg\:pb-24 { padding-bottom: 6rem; }
                        .lg\:pb-32 { padding-bottom: 8rem; }
                        .lg\:text-left { text-align: left; }
                        .lg\:text-center { text-align: center; }
                        .lg\:text-right { text-align: right; }
                        .lg\:text-sm { font-size: var(--text-sm); }
                        .lg\:text-base { font-size: var(--text-base); }
                        .lg\:text-lg { font-size: var(--text-lg); }
                        .lg\:text-xl { font-size: var(--text-xl); }
                        .lg\:text-2xl { font-size: var(--text-2xl); }
                        .lg\:text-3xl { font-size: var(--text-3xl); }
                        .lg\:text-4xl { font-size: var(--text-4xl); }
                        .lg\:text-5xl { font-size: 3rem; }
                        .lg\:text-6xl { font-size: 3.75rem; }
                        .lg\:-mt-\[6\.6rem\] { margin-top: -6.6rem; }
                        .lg\:mb-0 { margin-bottom: 0; }
                        .lg\:mb-6 { margin-bottom: 1.5rem; }
                        .lg\:-ml-px { margin-left: -1px; }
                        .lg\:ml-0 { margin-left: 0; }
                        .lg\:rounded-t-none { border-top-left-radius: 0; border-top-right-radius: 0; }
                        .lg\:rounded-tl-lg { border-top-left-radius: var(--radius-lg); }
                        .lg\:rounded-r-lg { border-top-right-radius: var(--radius-lg); border-bottom-right-radius: var(--radius-lg); }
                        .lg\:rounded-br-none { border-bottom-right-radius: 0; }
                    }
                    
                    @media (prefers-color-scheme: dark) {
                        .dark\:bg-gray-900 { background-color: var(--color-gray-900); }
                        .dark\:bg-gray-800 { background-color: var(--color-gray-800); }
                        .dark\:bg-gray-700 { background-color: var(--color-gray-700); }
                        .dark\:text-white { color: var(--color-white); }
                        .dark\:text-gray-100 { color: var(--color-gray-100); }
                        .dark\:text-gray-200 { color: var(--color-gray-200); }
                        .dark\:text-gray-300 { color: var(--color-gray-300); }
                        .dark\:text-gray-400 { color: var(--color-gray-400); }
                        .dark\:border-gray-700 { border-color: var(--color-gray-700); }
                        .dark\:border-gray-600 { border-color: var(--color-gray-600); }
                        .dark\:hover\:bg-gray-800:hover { background-color: var(--color-gray-800); }
                        .dark\:hover\:text-gray-200:hover { color: var(--color-gray-200); }
                        .dark\:hover\:border-gray-600:hover { border-color: var(--color-gray-600); }
                    }
                    
                    @starting-style {
                        .starting\:translate-y-4 { --tw-translate-y: 1rem; }
                        .starting\:translate-y-6 { --tw-translate-y: 1.5rem; }
                        .starting\:opacity-0 { opacity: 0; }
                    }
                }
            </style>
        @endif

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen">
        
        <!-- Navigation -->
 <!-- Navigation -->
        <nav class="bg-white dark:bg-gray-800 shadow-lg sticky top-0 z-50">
            <div class="w-full px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                     <div class="flex-shrink-0 flex items-center">
    <img src="{{ asset('images/Cofina1.png') }}"
         alt="Cofina Logo"
         class="h-10 sm:h-12 w-auto mr-2 sm:mr-3">

<!--     <span class="text-lg sm:text-xl font-bold">
        Gestion<span class="text-blue-600">Parc</span>
    </span> -->
</div>

                        
                        <div class="hidden lg:ml-8 lg:flex lg:space-x-8">
                            <a href="{{ url('/') }}" class="border-blue-500 text-gray-900 dark:text-gray-100 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Accueil
                            </a>
                            @auth
                            <a href="{{ route('equipment.index') }}" class="border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Équipements
                            </a>
                            <a href="{{ route('equipment.create') }}" class="border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Nouvel équipement
                            </a>
                            <a href="#" class="border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Logiciels
                            </a>
                            <a href="#" class="border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Rapports
                            </a>
                            @endauth
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        @auth
                        <div class="relative mr-2 hidden xl:block">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <i class="fas fa-search text-gray-400"></i>
                            </span>
                            <input type="text" class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 w-48 xl:w-64" placeholder="Rechercher...">
                        </div>
                        
                        <a href="{{ route('equipment.create') }}" class="hidden sm:flex bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded-lg transition duration-300 items-center text-sm">
                            <i class="fas fa-plus mr-1 sm:mr-2"></i>
                            <span class="hidden md:inline">Nouvel équipement</span>
                            <span class="md:hidden">Nouveau</span>
                        </a>
                        
                        <div class="relative">
                            <img class="h-8 w-8 rounded-full border-2 border-white shadow cursor-pointer" src="https://ui-avatars.com/api/?name={{ auth()->user()->name ?? 'Admin' }}&background=2563eb&color=fff" alt="Profile">
                            <span class="absolute bottom-0 right-0 block h-2 w-2 rounded-full bg-green-400 ring-2 ring-white"></span>
                        </div>
                        @else
                        <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-300 text-sm">
                            Connexion
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>


        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            
            <!-- Hero Section -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl shadow-xl overflow-hidden mb-10">
                <div class="px-8 py-12 md:py-16">
                    <div class="max-w-3xl">
                        <h1 class="text-4xl md:text-5xl font-bold text-white mb-6">
                            Let's get started
                        </h1>
                        <p class="text-xl text-blue-100 mb-8">
                            GestionParc a un écosystème incroyablement riche pour la gestion de votre parc informatique.
                        </p>
                        <p class="text-lg text-blue-100 mb-10">
                            Nous vous suggérons de commencer par les ressources suivantes :
                        </p>
                        
                        <div class="flex flex-col md:flex-row md:space-x-6 space-y-4 md:space-y-0">
                            <a href="#" class="inline-flex items-center justify-center px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition duration-300">
                                <i class="fas fa-book-open mr-3"></i>
                                Lire la documentation
                            </a>
                            <a href="#" class="inline-flex items-center justify-center px-6 py-3 bg-blue-800 text-white font-semibold rounded-lg hover:bg-blue-900 transition duration-300">
                                <i class="fas fa-play-circle mr-3"></i>
                                Tutoriels vidéo
                            </a>
                           <!--  <a href="{{ route('equipment.create') }}" class="inline-flex items-center justify-center px-6 py-3 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition duration-300">
                                <i class="fas fa-rocket mr-3"></i>
                                Déployer maintenant
                            </a> -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                @auth
                    @php
                        $stats = [
                            'total_equipment' => \App\Models\Equipment::count(),
                            'in_stock' => \App\Models\Equipment::where('statut', 'stock')->count(),
                            'in_maintenance' => \App\Models\Equipment::where('statut', 'maintenance')->count(),
                            'total_software' => \App\Models\Software::count() ?? 0,
                        ];
                    @endphp
                    
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Équipements totaux</p>
                                <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $stats['total_equipment'] }}</p>
                            </div>
                            <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full">
                                <i class="fas fa-desktop text-blue-600 dark:text-blue-300 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">En stock</p>
                                <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $stats['in_stock'] }}</p>
                            </div>
                            <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full">
                                <i class="fas fa-warehouse text-green-600 dark:text-green-300 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Logiciels</p>
                                <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $stats['total_software'] }}</p>
                            </div>
                            <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-full">
                                <i class="fas fa-code text-purple-600 dark:text-purple-300 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">En maintenance</p>
                                <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $stats['in_maintenance'] }}</p>
                            </div>
                            <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-full">
                                <i class="fas fa-tools text-yellow-600 dark:text-yellow-300 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Stats for non-authenticated users -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Solution complète</p>
                                <p class="text-3xl font-bold text-gray-800 dark:text-white">4 types</p>
                            </div>
                            <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full">
                                <i class="fas fa-cogs text-blue-600 dark:text-blue-300 text-2xl"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Informatique, Réseau, Électronique, Logiciel</p>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Gestion centralisée</p>
                                <p class="text-3xl font-bold text-gray-800 dark:text-white">100%</p>
                            </div>
                            <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full">
                                <i class="fas fa-database text-green-600 dark:text-green-300 text-2xl"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Tous vos équipements au même endroit</p>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Facile à utiliser</p>
                                <p class="text-3xl font-bold text-gray-800 dark:text-white">Intuitif</p>
                            </div>
                            <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-full">
                                <i class="fas fa-mouse-pointer text-purple-600 dark:text-purple-300 text-2xl"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Interface moderne et responsive</p>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition duration-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Sécurité</p>
                                <p class="text-3xl font-bold text-gray-800 dark:text-white">Maximale</p>
                            </div>
                            <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-full">
                                <i class="fas fa-shield-alt text-yellow-600 dark:text-yellow-300 text-2xl"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Protection de vos données</p>
                    </div>
                @endauth
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left Column - Quick Actions -->
                <div class="lg:col-span-2">
                    @auth
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Actions rapides</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <a href="{{ route('equipment.create') }}" class="bg-blue-50 dark:bg-gray-700 border border-blue-200 dark:border-gray-600 rounded-xl p-5 hover:bg-blue-100 dark:hover:bg-gray-600 transition duration-300 flex items-center hover:scale-105 transform">
                                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-lg mr-4">
                                    <i class="fas fa-plus-circle text-blue-600 dark:text-blue-300 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 dark:text-white">Ajouter un équipement</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Créer un nouvel équipement matériel</p>
                                </div>
                            </a>
                            
                            <a href="#" class="bg-green-50 dark:bg-gray-700 border border-green-200 dark:border-gray-600 rounded-xl p-5 hover:bg-green-100 dark:hover:bg-gray-600 transition duration-300 flex items-center hover:scale-105 transform">
                                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-lg mr-4">
                                    <i class="fas fa-laptop-code text-green-600 dark:text-green-300 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 dark:text-white">Enregistrer un logiciel</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Ajouter une nouvelle licence logicielle</p>
                                </div>
                            </a>
                            
                            <a href="#" class="bg-purple-50 dark:bg-gray-700 border border-purple-200 dark:border-gray-600 rounded-xl p-5 hover:bg-purple-100 dark:hover:bg-gray-600 transition duration-300 flex items-center hover:scale-105 transform">
                                <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-lg mr-4">
                                    <i class="fas fa-chart-bar text-purple-600 dark:text-purple-300 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 dark:text-white">Générer un rapport</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Créer un rapport d'inventaire</p>
                                </div>
                            </a>
                            
                            <a href="#" class="bg-yellow-50 dark:bg-gray-700 border border-yellow-200 dark:border-gray-600 rounded-xl p-5 hover:bg-yellow-100 dark:hover:bg-gray-600 transition duration-300 flex items-center hover:scale-105 transform">
                                <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-lg mr-4">
                                    <i class="fas fa-sync-alt text-yellow-600 dark:text-yellow-300 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 dark:text-white">Suivi maintenance</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Planifier les interventions</p>
                                </div>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Recent Equipment -->
                    @php
                        $recent_equipment = \App\Models\Equipment::with(['categorie', 'agence'])
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp
                    
                    @if($recent_equipment->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Équipements récents</h2>
                            <a href="{{ route('equipment.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">Voir tout →</a>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left text-gray-500 dark:text-gray-400 text-sm border-b dark:border-gray-700">
                                        <th class="pb-3">Nom</th>
                                        <th class="pb-3">Type</th>
                                        <th class="pb-3">Série</th>
                                        <th class="pb-3">Statut</th>
                                        <th class="pb-3">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recent_equipment as $equipment)
                                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="py-4">
                                            <div class="flex items-center">
                                                @switch($equipment->type)
                                                    @case('Informatique')
                                                        <div class="bg-blue-100 dark:bg-blue-900 p-2 rounded-lg mr-3">
                                                            <i class="fas fa-laptop text-blue-600 dark:text-blue-300"></i>
                                                        </div>
                                                        @break
                                                    @case('Réseau')
                                                        <div class="bg-green-100 dark:bg-green-900 p-2 rounded-lg mr-3">
                                                            <i class="fas fa-network-wired text-green-600 dark:text-green-300"></i>
                                                        </div>
                                                        @break
                                                    @case('Électronique')
                                                        <div class="bg-purple-100 dark:bg-purple-900 p-2 rounded-lg mr-3">
                                                            <i class="fas fa-video text-purple-600 dark:text-purple-300"></i>
                                                        </div>
                                                        @break
                                                    @default
                                                        <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded-lg mr-3">
                                                            <i class="fas fa-desktop text-gray-600 dark:text-gray-300"></i>
                                                        </div>
                                                @endswitch
                                                <div>
                                                    <p class="font-medium text-gray-800 dark:text-white">{{ $equipment->nom }}</p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $equipment->type }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4">
                                            @php
                                                $category = $equipment->categorie ? $equipment->categorie->nom : 'Non classé';
                                            @endphp
                                            <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 rounded-full text-xs font-medium">{{ $category }}</span>
                                        </td>
                                        <td class="py-4 text-gray-600 dark:text-gray-400">{{ $equipment->numero_serie }}</td>
                                        <td class="py-4">
                                            @switch($equipment->statut)
                                                @case('stock')
                                                    <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 rounded-full text-xs font-medium">En stock</span>
                                                    @break
                                                @case('parc')
                                                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 rounded-full text-xs font-medium">En service</span>
                                                    @break
                                                @case('maintenance')
                                                    <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300 rounded-full text-xs font-medium">Maintenance</span>
                                                    @break
                                                @default
                                                    <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded-full text-xs font-medium">{{ $equipment->statut }}</span>
                                            @endswitch
                                        </td>
                                        <td class="py-4 text-gray-500 dark:text-gray-400">{{ $equipment->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    @else
                    <!-- Welcome content for non-authenticated users -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 mb-8">
                        <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-4">Bienvenue dans GestionParc</h2>
                        <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">
                            Une solution complète pour gérer votre parc informatique. Suivez, gérez et optimisez tous vos équipements en un seul endroit.
                        </p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                            <div class="border-l-4 border-blue-500 pl-4">
                                <h3 class="font-bold text-lg text-gray-800 dark:text-white mb-2">Gestion centralisée</h3>
                                <p class="text-gray-600 dark:text-gray-400">Tous vos équipements informatiques, réseau et électroniques au même endroit.</p>
                            </div>
                            
                            <div class="border-l-4 border-green-500 pl-4">
                                <h3 class="font-bold text-lg text-gray-800 dark:text-white mb-2">Suivi en temps réel</h3>
                                <p class="text-gray-600 dark:text-gray-400">Suivez l'état de vos équipements et planifiez les maintenances.</p>
                            </div>
                            
                            <div class="border-l-4 border-purple-500 pl-4">
                                <h3 class="font-bold text-lg text-gray-800 dark:text-white mb-2">Rapports détaillés</h3>
                                <p class="text-gray-600 dark:text-gray-400">Générez des rapports complets sur votre parc informatique.</p>
                            </div>
                            
                            <div class="border-l-4 border-yellow-500 pl-4">
                                <h3 class="font-bold text-lg text-gray-800 dark:text-white mb-2">Interface intuitive</h3>
                                <p class="text-gray-600 dark:text-gray-400">Design moderne et facile à utiliser pour tous les utilisateurs.</p>
                            </div>
                        </div>
                    </div>
                    @endauth
                </div>
                
                <!-- Right Column - Resources & Activity -->
                <div>
                    <!-- Getting Started Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Pour commencer</h2>
                        
                        <div class="space-y-4">
                            <a href="#" class="flex items-center p-4 bg-blue-50 dark:bg-gray-700 rounded-lg hover:bg-blue-100 dark:hover:bg-gray-600 transition duration-300">
                                <div class="mr-4">
                                    <i class="fas fa-book text-blue-600 dark:text-blue-400 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-800 dark:text-white">Documentation complète</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Guide d'utilisation détaillé</p>
                                </div>
                            </a>
                            
                            <a href="#" class="flex items-center p-4 bg-purple-50 dark:bg-gray-700 rounded-lg hover:bg-purple-100 dark:hover:bg-gray-600 transition duration-300">
                                <div class="mr-4">
                                    <i class="fas fa-play-circle text-purple-600 dark:text-purple-400 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-800 dark:text-white">Tutoriels vidéo</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Apprenez avec des vidéos</p>
                                </div>
                            </a>
                            
                        <!--     <a href="{{ route('equipment.create') }}" class="flex items-center p-4 bg-green-50 dark:bg-gray-700 rounded-lg hover:bg-green-100 dark:hover:bg-gray-600 transition duration-300">
                                <div class="mr-4">
                                    <i class="fas fa-rocket text-green-600 dark:text-green-400 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-800 dark:text-white">Déployer maintenant</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Ajoutez votre premier équipement</p>
                                </div>
                            </a> -->
                        </div>
                    </div>
                    
                    @auth
                    <!-- Quick Links -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                        <h2 class="text-2xl font-bold mb-4">Liens rapides</h2>
                        <div class="space-y-3">
                            <a href="{{ route('equipment.create') }}" class="flex items-center hover:bg-blue-700 p-2 rounded-lg transition duration-300">
                                <i class="fas fa-plus mr-3"></i>
                                Ajouter un équipement
                            </a>
                            <a href="{{ route('equipment.index') }}" class="flex items-center hover:bg-blue-700 p-2 rounded-lg transition duration-300">
                                <i class="fas fa-list mr-3"></i>
                                Voir tous les équipements
                            </a>
                            <a href="#" class="flex items-center hover:bg-blue-700 p-2 rounded-lg transition duration-300">
                                <i class="fas fa-file-export mr-3"></i>
                                Exporter les données
                            </a>
                            <a href="#" class="flex items-center hover:bg-blue-700 p-2 rounded-lg transition duration-300">
                                <i class="fas fa-cog mr-3"></i>
                                Paramètres système
                            </a>
                        </div>
                    </div>
                    @else
                    <!-- Auth Links -->
                    <!-- <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Connectez-vous</h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            Accédez à toutes les fonctionnalités de GestionParc en vous connectant à votre compte.
                        </p>
                        
                        <div class="space-y-4">
                            <a href="{{ route('login') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg transition duration-300 flex items-center justify-center">
                                <i class="fas fa-sign-in-alt mr-3"></i>
                                Se connecter
                            </a>
                        
                        </div>
                    </div> -->
                    @endauth
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 dark:bg-gray-900 text-white py-8 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-4 md:mb-0">
                        <div class="flex items-center">
                            <i class="fas fa-server text-blue-400 text-2xl mr-3"></i>
                            <span class="text-xl font-bold">Gestion<span class="text-blue-400">Parc</span></span>
                        </div>
                        <p class="text-gray-400 mt-2">Gestion professionnelle de parc informatique</p>
                    </div>
                    
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                            <i class="fab fa-github text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                            <i class="fab fa-linkedin text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition duration-300">
                            <i class="fab fa-discord text-xl"></i>
                        </a>
                    </div>
                </div>
                
                <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; 2026 GestionParc. Tous droits réservés. | 
                        <a href="#" class="hover:text-white transition duration-300">Confidentialité</a> | 
                        <a href="#" class="hover:text-white transition duration-300">Conditions</a>
                    </p>
                </div>
            </div>
        </footer>

        <!-- JavaScript -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Animation pour les cartes
                const cards = document.querySelectorAll('.hover\\:scale-105');
                
                cards.forEach(card => {
                    card.addEventListener('mouseenter', function() {
                        this.style.transform = 'scale(1.05)';
                    });
                    
                    card.addEventListener('mouseleave', function() {
                        this.style.transform = 'scale(1)';
                    });
                });
                
                // Dark mode toggle (simplified)
                const darkModeToggle = document.createElement('button');
                darkModeToggle.className = 'fixed bottom-4 right-4 bg-gray-800 text-white p-3 rounded-full shadow-lg hover:bg-gray-700 transition duration-300 z-50';
                darkModeToggle.innerHTML = '<i class="fas fa-moon"></i>';
                darkModeToggle.title = 'Basculer mode sombre';
                document.body.appendChild(darkModeToggle);
                
                darkModeToggle.addEventListener('click', function() {
                    document.documentElement.classList.toggle('dark');
                    const icon = this.querySelector('i');
                    if (document.documentElement.classList.contains('dark')) {
                        icon.className = 'fas fa-sun';
                        this.title = 'Basculer mode clair';
                    } else {
                        icon.className = 'fas fa-moon';
                        this.title = 'Basculer mode sombre';
                    }
                });
                
                // Auto-check dark mode preference
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.classList.add('dark');
                    darkModeToggle.querySelector('i').className = 'fas fa-sun';
                    darkModeToggle.title = 'Basculer mode clair';
                }
            });
        </script>
    </body>
</html>