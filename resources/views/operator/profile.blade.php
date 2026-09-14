@extends('layouts.operator')

@section('title', 'Settings')
@section('page_title', 'Settings')

@push('head')
<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #bfcaba; border-radius: 10px; }

        .tab-panel { display: none; }
        .tab-panel.active { display: block; }
</style>
@endpush

@section('header_actions')
            <button class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors relative" data-icon="notifications">
                    notifications
                    <span class="absolute top-0 right-0 w-2 h-2 bg-tertiary rounded-full"></span>
                </button>
@endsection

@section('content')


            <!-- Status messages -->
            @if(session('status'))
                <div class="mb-6 p-4 rounded-xl bg-primary-fixed/30 border border-primary-container/30 flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary text-sm mt-0.5">check_circle</span>
                    <p class="text-on-surface text-body-sm font-body-sm">{{ session('status') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-error-container border border-error/20 flex items-start gap-3">
                    <span class="material-symbols-outlined text-error text-sm mt-0.5">error</span>
                    <div>
                        @foreach($errors->all() as $error)
                            <p class="text-error text-body-sm font-body-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Profile Header Card -->
            <div class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/15 shadow-sm mb-6 flex flex-col md:flex-row items-start md:items-center gap-5">
                <div class="relative flex-shrink-0">
                    <div class="h-20 w-20 rounded-2xl bg-primary-container flex items-center justify-center overflow-hidden border-2 border-primary/20">
                        @if($operator->logo_path)
                            <img src="{{ asset('storage/' . $operator->logo_path) }}" alt="{{ $operator->company_name }}" class="w-full h-full object-cover" />
                        @else
                            <span class="material-symbols-outlined text-on-primary-fixed text-4xl">business</span>
                        @endif
                    </div>
                    <label for="logo-upload" class="absolute -bottom-1 -right-1 bg-primary text-white p-1.5 rounded-lg shadow-lg hover:brightness-110 transition-all cursor-pointer">
                        <span class="material-symbols-outlined text-sm">edit</span>
                    </label>
                </div>
                <div class="flex-grow">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-headline-md text-headline-md text-on-surface">{{ $operator->company_name }}</h3>
                        @if($operator->is_verified)
                            <span class="bg-primary-fixed/40 text-on-primary-fixed-variant text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">verified</span> Verified
                            </span>
                        @else
                            <span class="bg-secondary-container/30 text-on-secondary-container text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full">
                                Pending Verification
                            </span>
                        @endif
                    </div>
                    <p class="text-on-surface-variant text-body-sm font-body-sm mt-1">{{ $operator->email }}</p>
                    @if($operator->description)
                        <p class="text-on-surface-variant text-body-sm mt-2 max-w-2xl">{{ $operator->description }}</p>
                    @endif
                </div>
            </div>

            <!-- Operational Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-surface-container-lowest rounded-xl p-4 border border-outline-variant/15 shadow-sm">
                    <p class="text-label-caps text-label-caps uppercase text-outline mb-2">Total Trips</p>
                    <p class="text-headline-md text-headline-md text-on-surface">{{ number_format($total_trips) }}</p>
                    <p class="text-body-sm text-body-sm text-on-surface-variant mt-1">All time</p>
                </div>
                <div class="bg-surface-container-lowest rounded-xl p-4 border border-outline-variant/15 shadow-sm">
                    <p class="text-label-caps text-label-caps uppercase text-outline mb-2">Active Fleet</p>
                    <p class="text-headline-md text-headline-md text-primary">{{ $active_buses }}</p>
                    <p class="text-body-sm text-body-sm text-on-surface-variant mt-1">Buses available</p>
                </div>
                <div class="bg-surface-container-lowest rounded-xl p-4 border border-outline-variant/15 shadow-sm">
                    <p class="text-label-caps text-label-caps uppercase text-outline mb-2">Avg. Occupancy</p>
                    <p class="text-headline-md text-headline-md text-secondary">{{ $avg_occupancy }}%</p>
                    <p class="text-body-sm text-body-sm text-on-surface-variant mt-1">Current</p>
                </div>
                <div class="bg-surface-container-lowest rounded-xl p-4 border border-outline-variant/15 shadow-sm">
                    <p class="text-label-caps text-label-caps uppercase text-outline mb-2">On-Time Rate</p>
                    <p class="text-headline-md text-headline-md text-primary-container">{{ $on_time_rate }}%</p>
                    <p class="text-body-sm text-body-sm text-on-surface-variant mt-1">Last 30 days</p>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex items-center gap-2 bg-surface-container-lowest border border-outline-variant/15 rounded-xl p-1 mb-6 w-fit overflow-x-auto">
                <button onclick="setTab('details', this)" class="tab-btn px-4 py-2 rounded-lg text-body-sm font-bold bg-primary text-on-primary transition-all whitespace-nowrap">
                    Account Details
                </button>
                <button onclick="setTab('fleet', this)" class="tab-btn px-4 py-2 rounded-lg text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-all whitespace-nowrap">
                    Fleet Information
                </button>
                <button onclick="setTab('payment', this)" class="tab-btn px-4 py-2 rounded-lg text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-all whitespace-nowrap">
                    Payment Methods
                </button>
                <button onclick="setTab('billing', this)" class="tab-btn px-4 py-2 rounded-lg text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-all whitespace-nowrap">
                    Billing History
                </button>
                <button onclick="setTab('password', this)" class="tab-btn px-4 py-2 rounded-lg text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-all whitespace-nowrap">
                    Change Password
                </button>
            </div>

            <!-- ==================== TAB: Account Details ==================== -->
            <div id="tab-details" class="tab-panel active">
                <form action="{{ route('operator.profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/15 shadow-sm">
                    @csrf
                    @method('PUT')

                    <!-- Hidden logo upload input -->
                    <input type="file" id="logo-upload" name="logo" accept="image/*" class="hidden" onchange="this.form.submit()" />

                    <h4 class="font-headline-sm text-headline-sm text-on-surface mb-5">Business Information</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Company Name</label>
                            <input type="text" name="company_name" value="{{ old('company_name', $operator->company_name) }}" required
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>

                        <div>
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $operator->email) }}" required
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>

                        <div>
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Phone Number</label>
                            <input type="text" name="phone_number" value="{{ old('phone_number', $operator->phone_number) }}" required
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>

                        <div>
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">TPIN</label>
                            <input type="text" name="tpin" value="{{ old('tpin', $operator->tpin) }}"
                                placeholder="Optional"
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>

                        <div>
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Business Registration Number</label>
                            <input type="text" name="business_registration_number" value="{{ old('business_registration_number', $operator->business_registration_number) }}"
                                placeholder="Optional"
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>

                        <div>
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Business Registration Date</label>
                            <input type="date" name="business_registration_date" value="{{ old('business_registration_date', $operator->business_registration_date?->format('Y-m-d')) }}"
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>

                        <div>
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Business Type</label>
                            <input type="text" name="business_type" value="{{ old('business_type', $operator->business_type) }}"
                                placeholder="e.g. Interstate Transport"
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>

                        <div>
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Website</label>
                            <input type="url" name="website" value="{{ old('website', $operator->website) }}"
                                placeholder="https://example.com"
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Business Address</label>
                            <input type="text" name="address" value="{{ old('address', $operator->address) }}"
                                placeholder="Optional"
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Business Description</label>
                            <textarea name="description" rows="3" placeholder="Tell passengers about your company..."
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors">{{ old('description', $operator->description) }}</textarea>
                        </div>
                    </div>

                    <hr class="my-6 border-outline-variant/20" />

                    <h4 class="font-headline-sm text-headline-sm text-on-surface mb-5">Contact Person</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Contact Person Name</label>
                            <input type="text" name="contact_person_name" value="{{ old('contact_person_name', $operator->contact_person_name) }}"
                                placeholder="Optional"
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>

                        <div>
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Title / Role</label>
                            <input type="text" name="contact_person_title" value="{{ old('contact_person_title', $operator->contact_person_title) }}"
                                placeholder="e.g. Fleet Director"
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="bg-primary text-on-primary font-bold text-body-sm px-6 py-3 rounded-xl hover:brightness-110 active:scale-95 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">save</span>
                            Save Changes
                        </button>
                    </div>
                </form>

                <!-- Account Verification Status -->
                <div class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/15 shadow-sm mt-6">
                    <h4 class="font-headline-sm text-headline-sm text-on-surface mb-5">Account Verification Status</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-xl">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined {{ $operator->is_verified ? 'text-primary' : 'text-outline' }}" style="font-variation-settings: 'FILL' 1;">verified</span>
                                <div>
                                    <p class="font-bold text-body-sm text-on-surface">Business License</p>
                                    <p class="text-body-sm text-on-surface-variant">
                                        @if($operator->business_license_verified_at)
                                            Verified on {{ $operator->business_license_verified_at->format('d F Y') }}
                                        @elseif($operator->is_verified)
                                            Verified on {{ $operator->verified_at?->format('d F Y') ?? 'N/A' }}
                                        @else
                                            Not yet submitted
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full {{ $operator->is_verified ? 'bg-primary/10 text-primary' : 'bg-outline-variant/30 text-outline' }}">
                                {{ $operator->is_verified ? 'Verified' : 'Pending' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-xl">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined {{ $operator->tax_id_verified_at ? 'text-primary' : 'text-outline' }}" style="font-variation-settings: 'FILL' 1;">verified</span>
                                <div>
                                    <p class="font-bold text-body-sm text-on-surface">Tax ID (TPIN)</p>
                                    <p class="text-body-sm text-on-surface-variant">
                                        @if($operator->tax_id_verified_at)
                                            Verified on {{ $operator->tax_id_verified_at->format('d F Y') }}
                                        @elseif($operator->tpin)
                                            {{ $operator->tpin }} — Awaiting verification
                                        @else
                                            Not provided
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full {{ $operator->tax_id_verified_at ? 'bg-primary/10 text-primary' : ($operator->tpin ? 'bg-secondary/10 text-secondary' : 'bg-outline-variant/30 text-outline') }}">
                                {{ $operator->tax_id_verified_at ? 'Verified' : ($operator->tpin ? 'Pending' : 'N/A') }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-xl">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined {{ $operator->insurance_expiry_date && $operator->insurance_expiry_date->isFuture() ? 'text-primary' : 'text-secondary' }}" style="font-variation-settings: 'FILL' 1;">shield</span>
                                <div>
                                    <p class="font-bold text-body-sm text-on-surface">Insurance Certificate</p>
                                    <p class="text-body-sm text-on-surface-variant">
                                        @if($operator->insurance_expiry_date)
                                            @if($operator->insurance_expiry_date->isFuture())
                                                Valid until {{ $operator->insurance_expiry_date->format('d F Y') }}
                                            @else
                                                Expired on {{ $operator->insurance_expiry_date->format('d F Y') }}
                                            @endif
                                        @else
                                            Not provided
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full {{ $operator->insurance_expiry_date && $operator->insurance_expiry_date->isFuture() ? 'bg-primary/10 text-primary' : 'bg-tertiary-container/10 text-tertiary' }}">
                                {{ $operator->insurance_expiry_date && $operator->insurance_expiry_date->isFuture() ? 'Valid' : ($operator->insurance_expiry_date ? 'Expired' : 'N/A') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="bg-surface-container-lowest rounded-2xl p-6 border border-tertiary-container/30 shadow-sm mt-6">
                    <h4 class="font-headline-sm text-headline-sm text-tertiary mb-2">Danger Zone</h4>
                    <p class="text-body-sm text-on-surface-variant mb-5">Irreversible actions on your operator account.</p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button" onclick="alert('This feature is not yet available. Please contact support.')"
                            class="px-6 py-3 border-2 border-tertiary text-tertiary font-bold text-body-sm rounded-xl hover:bg-tertiary/5 transition-colors">
                            Suspend Account
                        </button>
                        <button type="button" onclick="alert('This feature is not yet available. Please contact support.')"
                            class="px-6 py-3 border-2 border-error text-error font-bold text-body-sm rounded-xl hover:bg-error/5 transition-colors">
                            Delete Account
                        </button>
                    </div>
                </div>
            </div>

            <!-- ==================== TAB: Fleet Information ==================== -->
            <div id="tab-fleet" class="tab-panel">
                <div class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/15 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <h4 class="font-headline-sm text-headline-sm text-on-surface">Fleet Overview</h4>
                        <a href="{{ route('operator.buses.index') }}" class="text-primary font-bold text-body-sm flex items-center gap-1 hover:underline">
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            Manage Fleet
                        </a>
                    </div>
                    <p class="text-body-sm text-on-surface-variant mb-4">
                        You have <strong class="text-on-surface">{{ $active_buses }}</strong> active buses in your fleet.
                        Visit the Fleet section to manage bus details, maintenance schedules, and seat configurations.
                    </p>
                    <div class="bg-surface-container-low rounded-xl p-4 flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">directions_bus</span>
                        <div>
                            <p class="font-bold text-body-sm text-on-surface">Total Buses</p>
                            <p class="text-headline-md text-headline-md text-primary">{{ $active_buses }} <span class="text-body-sm text-on-surface-variant">active</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== TAB: Payment Methods ==================== -->
            <div id="tab-payment" class="tab-panel">
                <div class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/15 shadow-sm">
                    <h4 class="font-headline-sm text-headline-sm text-on-surface mb-5">Payment Methods</h4>
                    <p class="text-body-sm text-on-surface-variant mb-6">
                        Configure how you receive payouts from bookings. This section will allow you to link mobile money accounts, bank accounts, or other payment channels.
                    </p>

                    <div class="space-y-4">
                        <!-- Mobile Money -->
                        <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-xl">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary">smartphone</span>
                                <div>
                                    <p class="font-bold text-body-sm text-on-surface">Mobile Money</p>
                                    <p class="text-body-sm text-on-surface-variant">Not configured</p>
                                </div>
                            </div>
                            <button class="text-primary font-bold text-body-sm hover:underline">Configure</button>
                        </div>

                        <!-- Bank Account -->
                        <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-xl">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary">account_balance</span>
                                <div>
                                    <p class="font-bold text-body-sm text-on-surface">Bank Account</p>
                                    <p class="text-body-sm text-on-surface-variant">Not configured</p>
                                </div>
                            </div>
                            <button class="text-primary font-bold text-body-sm hover:underline">Configure</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== TAB: Billing History ==================== -->
            <div id="tab-billing" class="tab-panel">
                <div class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/15 shadow-sm">
                    <h4 class="font-headline-sm text-headline-sm text-on-surface mb-5">Billing History</h4>
                    <p class="text-body-sm text-on-surface-variant mb-6">
                        View your transaction history, invoices, and payout records.
                    </p>

                    <div class="bg-surface-container-low rounded-xl p-8 text-center">
                        <span class="material-symbols-outlined text-4xl text-outline mb-3">receipt_long</span>
                        <p class="font-bold text-body-sm text-on-surface">No billing records yet</p>
                        <p class="text-body-sm text-on-surface-variant mt-1">Billing history will appear once you start receiving payouts.</p>
                    </div>
                </div>
            </div>

            <!-- ==================== TAB: Change Password ==================== -->
            <div id="tab-password" class="tab-panel">
                <form action="{{ route('operator.profile.password') }}" method="POST" class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/15 shadow-sm max-w-lg">
                    @csrf
                    @method('PUT')

                    <h4 class="font-headline-sm text-headline-sm text-on-surface mb-5">Change Password</h4>

                    <div class="flex flex-col gap-5">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Current Password</label>
                            <input type="password" name="current_password" required
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>

                        <div>
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">New Password</label>
                            <input type="password" name="password" required
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>

                        <div>
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Confirm New Password</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="bg-primary text-on-primary font-bold text-body-sm px-6 py-3 rounded-xl hover:brightness-110 active:scale-95 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">lock_reset</span>
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
@endsection

@push('scripts')
<script>
        function setTab(tab, btn) {
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            document.getElementById('tab-' + tab).classList.add('active');

            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('bg-primary', 'text-on-primary', 'font-bold');
                b.classList.add('text-on-surface-variant', 'font-medium');
            });
            btn.classList.add('bg-primary', 'text-on-primary', 'font-bold');
            btn.classList.remove('text-on-surface-variant', 'font-medium');
        }

        // If validation errors exist on password fields, auto-switch to that tab
        @if($errors->has('current_password') || $errors->has('password'))
            document.addEventListener('DOMContentLoaded', () => {
                const btns = document.querySelectorAll('.tab-btn');
                if (btns.length >= 5) btns[4].click();
            });
        @endif
    </script>
@endpush
