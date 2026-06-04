<!-- TODO: This view should extend layouts.app when a layout is available -->
<!-- TODO: Loop through 'buses' from Buses table to display fleet information -->

<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Operator Profile - BookMyBus Zambia</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "on-secondary": "#ffffff",
                    "on-secondary-container": "#632f00",
                    "on-secondary-fixed": "#301400",
                    "inverse-surface": "#2f3133",
                    "on-primary-container": "#b1ffb1",
                    "secondary-container": "#ff8921",
                    "primary-fixed-dim": "#7edb83",
                    "inverse-primary": "#7edb83",
                    "background": "#f9f9fc",
                    "inverse-on-surface": "#f0f0f3",
                    "tertiary-container": "#d1200f",
                    "secondary-fixed": "#ffdcc6",
                    "surface-container-lowest": "#ffffff",
                    "outline-variant": "#bfcaba",
                    "on-tertiary": "#ffffff",
                    "surface": "#f9f9fc",
                    "on-primary": "#ffffff",
                    "primary-fixed": "#99f89d",
                    "surface-variant": "#e2e2e5",
                    "secondary-fixed-dim": "#ffb784",
                    "on-primary-fixed": "#002106",
                    "surface-bright": "#f9f9fc",
                    "on-error-container": "#93000a",
                    "surface-dim": "#dadadc",
                    "outline": "#6f7a6c",
                    "on-secondary-fixed-variant": "#713700",
                    "primary": "#00601f",
                    "on-primary-fixed-variant": "#00531a",
                    "surface-container-low": "#f3f3f6",
                    "on-surface-variant": "#3f493e",
                    "surface-container-high": "#e8e8ea",
                    "on-surface": "#1a1c1e",
                    "primary-container": "#197b30",
                    "error-container": "#ffdad6",
                    "tertiary-fixed": "#ffdad4",
                    "on-tertiary-fixed": "#400100",
                    "tertiary-fixed-dim": "#ffb4a7",
                    "surface-container-highest": "#e2e2e5",
                    "on-tertiary-container": "#ffe7e3",
                    "on-tertiary-fixed-variant": "#920600",
                    "surface-tint": "#006e25",
                    "error": "#ba1a1a",
                    "surface-container": "#eeeef0",
                    "secondary": "#954a00",
                    "tertiary": "#a80800",
                    "on-error": "#ffffff",
                    "on-background": "#1a1c1e"
            },
            "borderRadius": {
                    "DEFAULT": "0.125rem",
                    "lg": "0.25rem",
                    "xl": "0.5rem",
                    "full": "0.75rem"
            },
            "fontFamily": {
                    "headline": ["Manrope"],
                    "body": ["Inter"],
                    "label": ["Inter"]
            }
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-headline { font-family: 'Manrope', sans-serif; }
    </style>
</head>
<body class="bg-surface text-on-surface">
<!-- SideNavBar -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-zinc-50 dark:bg-zinc-950 border-r border-zinc-200/50 dark:border-zinc-800/50 flex flex-col py-6 z-40">
<div class="font-manrope font-black text-lg text-green-900 dark:text-green-100 px-6 mb-8">
            BookMyBus Zambia
        </div>
<nav class="flex-1 space-y-1">
<div class="text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 mx-2 px-4 py-3 rounded-lg cursor-pointer transition-transform hover:translate-x-1 flex items-center gap-3 text-sm font-medium">
<span class="material-symbols-outlined">dashboard</span>
                Dashboard
            </div>
<div class="text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 mx-2 px-4 py-3 rounded-lg cursor-pointer transition-transform hover:translate-x-1 flex items-center gap-3 text-sm font-medium">
<span class="material-symbols-outlined">directions_bus</span>
                Manage Trips
            </div>
<div class="text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 mx-2 px-4 py-3 rounded-lg cursor-pointer transition-transform hover:translate-x-1 flex items-center gap-3 text-sm font-medium">
<span class="material-symbols-outlined">airline_seat_recline_extra</span>
                Seat Maps
            </div>
<div class="text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 mx-2 px-4 py-3 rounded-lg cursor-pointer transition-transform hover:translate-x-1 flex items-center gap-3 text-sm font-medium">
<span class="material-symbols-outlined">payments</span>
                Revenue
            </div>
<div class="bg-green-50 dark:bg-green-900/20 text-green-900 dark:text-green-100 rounded-lg mx-2 px-4 py-3 cursor-pointer transition-transform flex items-center gap-3 text-sm font-medium">
<span class="material-symbols-outlined">person</span>
                Profile
            </div>
</nav>
<div class="border-t border-zinc-200/50 dark:border-zinc-800/50 pt-4 mt-auto">
<div class="flex items-center px-6 mb-6">
<div class="w-8 h-8 rounded-full bg-surface-container-highest overflow-hidden mr-3">
<img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDYdD0MuvKteWZO8udxva74oGZX_HVmlL44CSe4Zywhn-w565oAJpB-yYK5frtToRdptj26IeKtmxnK63U3_teckn8XhS5t6A_DLT6eP2Q1BXBgjoy2SohJj0gEV1BBFj-YvUzMDrXMovoOgXzByi7pSVzn-fHS1EJWG2oZfjZ8TJng7jM3Y-0GbRqS8EhFPl-J3PA4pO_t9kjIRmh0a77iah4KoXcNJPHxsV4NT7MJgl0DLwutXXCXKNYVMF65iR6vXGvTtLicM3A8"/>
</div>
<div>
<p class="text-xs font-bold text-on-surface">Bus Operator</p>
<p class="text-[10px] text-zinc-500">Zambia Transit Hub</p>
</div>
</div>
<div class="text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 mx-2 px-4 py-2 cursor-pointer transition-transform hover:translate-x-1 flex items-center gap-3 text-xs font-medium">
<span class="material-symbols-outlined">settings</span>
                Settings
            </div>
<div class="text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 mx-2 px-4 py-2 cursor-pointer transition-transform hover:translate-x-1 flex items-center gap-3 text-xs font-medium">
<span class="material-symbols-outlined">logout</span>
                Logout
            </div>
</div>
</aside>

<!-- Main Content -->
<main class="ml-64 min-h-screen p-8">
<!-- Header -->
<header class="mb-10">
<span class="text-secondary font-headline font-bold tracking-widest text-[10px] uppercase">Account Management</span>
<h1 class="text-4xl font-headline font-extrabold text-on-surface tracking-tight mt-1">Operator Profile</h1>
</header>

<!-- Profile Header Card -->
<div class="bg-surface-container-lowest rounded-2xl shadow-sm p-8 mb-8 border border-outline-variant/15">
<div class="flex flex-col md:flex-row gap-8 items-start md:items-center">
<div class="relative">
<div class="w-24 h-24 rounded-2xl bg-surface-container-highest overflow-hidden border-4 border-primary">
<img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDYdD0MuvKteWZO8udxva74oGZX_HVmlL44CSe4Zywhn-w565oAJpB-yYK5frtToRdptj26IeKtmxnK63U3_teckn8XhS5t6A_DLT6eP2Q1BXBgjoy2SohJj0gEV1BBFj-YvUzMDrXMovoOgXzByi7pSVzn-fHS1EJWG2oZfjZ8TJng7jM3Y-0GbRqS8EhFPl-J3PA4pO_t9kjIRmh0a77iah4KoXcNJPHxsV4NT7MJgl0DLwutXXCXKNYVMF65iR6vXGvTtLicM3A8"/>
</div>
<button class="absolute bottom-0 right-0 bg-primary text-white p-2 rounded-lg shadow-lg hover:brightness-110 transition-all">
<span class="material-symbols-outlined text-sm">edit</span>
</button>
</div>
<div class="flex-1">
<h2 class="text-3xl font-headline font-extrabold text-on-surface mb-2">Zambia Transit Hub</h2>
<div class="flex items-center gap-4 mb-4">
<div class="flex items-center gap-1">
<span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="font-bold text-on-surface">4.8</span>
<span class="text-xs text-zinc-500">(324 reviews)</span>
</div>
<span class="inline-block px-3 py-1 bg-secondary-container/20 text-secondary rounded-full text-xs font-bold">Verified Operator</span>
</div>
<p class="text-on-surface-variant mb-4 max-w-2xl">Premium bus operator serving Zambia's major routes with luxury comfort and reliable service since 2018.</p>
<div class="flex flex-wrap gap-3">
<button class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg font-bold text-sm hover:brightness-105 transition-all">
<span class="material-symbols-outlined text-sm">edit</span>
                Edit Profile
            </button>
<button class="flex items-center gap-2 px-4 py-2 bg-surface-container-high text-on-surface rounded-lg font-bold text-sm hover:bg-surface-container-highest transition-all">
<span class="material-symbols-outlined text-sm">visibility</span>
                View Public Profile
            </button>
</div>
</div>
</div>
</main>

<!-- Tabs Section -->
<div class="ml-64 px-8">
<div class="flex gap-8 border-b border-outline-variant/15 mb-8">
<button class="px-4 py-3 font-bold text-sm border-b-2 border-primary text-primary">Account Details</button>
<button class="px-4 py-3 font-bold text-sm text-zinc-600 hover:text-on-surface transition-colors">Fleet Information</button>
<button class="px-4 py-3 font-bold text-sm text-zinc-600 hover:text-on-surface transition-colors">Payment Methods</button>
<button class="px-4 py-3 font-bold text-sm text-zinc-600 hover:text-on-surface transition-colors">Billing History</button>
</div>
</div>

<!-- Account Details Tab -->
<div class="ml-64 px-8 space-y-8">
<!-- Business Information -->
<section class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-outline-variant/15">
<h3 class="text-xl font-headline font-bold text-on-surface mb-6">Business Information</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div>
<label class="text-xs font-bold uppercase text-zinc-500 block mb-2">Business Name</label>
<p class="text-on-surface font-semibold">Zambia Transit Hub</p>
</div>
<div>
<label class="text-xs font-bold uppercase text-zinc-500 block mb-2">Business Registration Number</label>
<p class="text-on-surface font-semibold">ZM-2018-BUS-0847</p>
</div>
<div>
<label class="text-xs font-bold uppercase text-zinc-500 block mb-2">Date Registered</label>
<p class="text-on-surface font-semibold">15 March 2018</p>
</div>
<div>
<label class="text-xs font-bold uppercase text-zinc-500 block mb-2">Business Type</label>
<p class="text-on-surface font-semibold">Interstate Transport</p>
</div>
<div class="md:col-span-2">
<label class="text-xs font-bold uppercase text-zinc-500 block mb-2">Business Address</label>
<p class="text-on-surface font-semibold">156 Cairo Road, Lusaka, Zambia</p>
</div>
</div>
</section>

<!-- Contact Information -->
<section class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-outline-variant/15">
<div class="flex justify-between items-center mb-6">
<h3 class="text-xl font-headline font-bold text-on-surface">Contact Information</h3>
<button class="text-primary font-bold text-sm flex items-center gap-1 hover:underline">
<span class="material-symbols-outlined text-sm">edit</span>
                Edit
            </button>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div>
<label class="text-xs font-bold uppercase text-zinc-500 block mb-2">Primary Contact Name</label>
<p class="text-on-surface font-semibold">Michael Banda</p>
</div>
<div>
<label class="text-xs font-bold uppercase text-zinc-500 block mb-2">Title</label>
<p class="text-on-surface font-semibold">Fleet Director</p>
</div>
<div>
<label class="text-xs font-bold uppercase text-zinc-500 block mb-2">Email</label>
<p class="text-on-surface font-semibold">michael.banda@zamtransit.com</p>
</div>
<div>
<label class="text-xs font-bold uppercase text-zinc-500 block mb-2">Phone Number</label>
<p class="text-on-surface font-semibold">+260 211 123 456</p>
</div>
</div>
</section>

<!-- Operational Statistics -->
<section class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-outline-variant/15">
<h3 class="text-xl font-headline font-bold text-on-surface mb-6">Operational Statistics</h3>
<div class="grid grid-cols-2 md:grid-cols-4 gap-6">
<div class="bg-surface-container-low rounded-xl p-4">
<p class="text-xs uppercase tracking-[0.2em] font-bold text-zinc-500 mb-3">Total Trips Operated</p>
<p class="text-3xl font-headline font-extrabold text-on-surface">1,847</p>
<p class="text-[10px] text-zinc-500 mt-2">All time</p>
</div>
<div class="bg-surface-container-low rounded-xl p-4">
<p class="text-xs uppercase tracking-[0.2em] font-bold text-zinc-500 mb-3">Active Fleet</p>
<p class="text-3xl font-headline font-extrabold text-primary">35</p>
<p class="text-[10px] text-zinc-500 mt-2">Buses available</p>
</div>
<div class="bg-surface-container-low rounded-xl p-4">
<p class="text-xs uppercase tracking-[0.2em] font-bold text-zinc-500 mb-3">Avg. Occupancy</p>
<p class="text-3xl font-headline font-extrabold text-secondary">82%</p>
<p class="text-[10px] text-zinc-500 mt-2">Current</p>
</div>
<div class="bg-surface-container-low rounded-xl p-4">
<p class="text-xs uppercase tracking-[0.2em] font-bold text-zinc-500 mb-3">On-Time Rate</p>
<p class="text-3xl font-headline font-extrabold text-primary-container">94%</p>
<p class="text-[10px] text-zinc-500 mt-2">Last 30 days</p>
</div>
</div>
</section>

<!-- Account Verification -->
<section class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-outline-variant/15 mb-8">
<h3 class="text-xl font-headline font-bold text-on-surface mb-6">Account Verification Status</h3>
<div class="space-y-4">
<div class="flex items-center justify-between p-4 bg-surface-container-low rounded-lg">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">verified</span>
<div>
<p class="font-bold text-on-surface">Business License Verified</p>
<p class="text-xs text-zinc-500">Verified on 24 August 2024</p>
</div>
</div>
<span class="px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full">Verified</span>
</div>
<div class="flex items-center justify-between p-4 bg-surface-container-low rounded-lg">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">verified</span>
<div>
<p class="font-bold text-on-surface">Tax ID Verified</p>
<p class="text-xs text-zinc-500">Verified on 15 August 2024</p>
</div>
</div>
<span class="px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full">Verified</span>
</div>
<div class="flex items-center justify-between p-4 bg-surface-container-low rounded-lg">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">schedule</span>
<div>
<p class="font-bold text-on-surface">Insurance Certificate</p>
<p class="text-xs text-zinc-500">Expires on 15 December 2025</p>
</div>
</div>
<span class="px-3 py-1 bg-secondary/10 text-secondary text-xs font-bold rounded-full">Valid</span>
</div>
</div>
</section>

<!-- Danger Zone -->
<section class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-tertiary-container/30 mb-20">
<h3 class="text-xl font-headline font-bold text-tertiary mb-4">Danger Zone</h3>
<p class="text-on-surface-variant text-sm mb-6">Irreversible actions on your operator account.</p>
<div class="flex flex-col sm:flex-row gap-4">
<button class="px-6 py-3 border-2 border-tertiary text-tertiary font-bold rounded-lg hover:bg-tertiary/5 transition-colors">
                Suspend Account
            </button>
<button class="px-6 py-3 border-2 border-error text-error font-bold rounded-lg hover:bg-error/5 transition-colors">
                Delete Account
            </button>
</div>
</section>
</div>

</body></html>