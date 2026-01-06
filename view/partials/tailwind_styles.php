@theme {
  --color-bg-body: #F9F5F2;
  --color-bg-card: #ffffff;
  --color-text-primary: #2d3748;
  --color-text-secondary: #718096;
  --color-accent-primary: #3b82f6;
  --color-accent-hover: #2563eb;
  --color-danger: #ef4444;
  --color-success: #10b981;
  --color-border-color: #e2e8f0;
  
  --radius-sm: 6px;
  --radius-md: 12px;
  
  --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  --shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  
  --font-family-work-sans: "Work Sans", sans-serif;
}

/* Base Styles */
body {
    font-family: var(--font-family-work-sans);
    background-color: var(--color-bg-body);
    color: var(--color-text-primary);
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
}

/* Layout */
section {
    @apply max-w-[1200px] mx-auto p-8 my-8 bg-bg-card rounded-md shadow-sm;
}

/* Typography */
h1, h2, h3 {
    @apply text-text-primary font-bold mb-4 tracking-tighter;
}

h1 { @apply text-3xl; }
h2 { @apply text-2xl; }
h3 { @apply text-xl; }
p { @apply text-text-secondary mb-4; }

/* Buttons */
.btn, .btn-primary, .btn-danger, .btn-add {
    @apply inline-block px-5 py-2.5 rounded-sm font-medium text-center cursor-pointer transition-all duration-200 border-none text-base no-underline;
}

.btn-primary, .btn-add {
    @apply bg-accent-primary text-white;
}

.btn-primary:hover, .btn-add:hover {
    @apply bg-accent-hover shadow-md -translate-y-px;
}

.btn-danger {
    @apply bg-bg-card text-danger border border-border-color;
}

.btn-danger:hover {
    @apply bg-red-50 border-danger;
}
.product-card:hover {
    @apply -translate-y-1 shadow-hover border-transparent;
}

.product-image {
    @apply w-full h-60 object-contain bg-white p-4 border-b border-border-color;
}

.product-price {
    @apply text-xl font-bold text-text-primary mt-4;
}

/* Forms */
.form-group {
    @apply mb-6;
}

label {
    @apply block mb-2 font-medium text-text-primary;
}

.form-control {
    @apply w-full p-3 border border-border-color rounded-sm text-base transition-colors duration-200 bg-white;
}

.form-control:focus {
    @apply outline-none border-accent-primary ring-4 ring-blue-500/10;
}

/* Tables */
.table {
    @apply w-full border-separate border-spacing-0 bg-bg-card rounded-md overflow-hidden shadow-sm mt-6 border-2 border-black;
}

.table th {
    @apply bg-slate-50 text-text-secondary font-semibold uppercase text-xs tracking-wide p-4 border-b border-border-color;
}

.table td {
    @apply p-4 border-b border-border-color text-text-primary align-middle;
}

.table tr:last-child td {
    @apply border-b-0;
}

/* Utilities */
.alert {
    @apply p-4 rounded-sm mb-6 border-l-4 font-medium;
}

.alert-success {
    @apply bg-green-50 text-success border-success;
}

.error {
    @apply text-danger text-sm mt-2;
}

img.diagram {
    @apply rounded-md shadow-md mx-auto my-8;
}

/* Hero / Mega Menu */
.hero-container {
    @apply max-w-[1200px] mx-auto flex h-[60vh] min-h-[400px] relative;
}

.hero-carousel {
    @apply flex-1 p-8 flex items-center justify-center bg-white;
}

#mega-menu-overlay {
    @apply hidden absolute top-0 left-[250px] w-[calc(100%-250px)] h-full bg-white border-l border-gray-200 z-[100] p-8 overflow-y-auto shadow-sm;
}
