@extends('layouts.dashboard')

@section('content')
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">
            សូមស្វាគមន៍មកកាន់ទំព័រ <span class="text-indigo-700">ដើម</span>
        </h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
            Overview of your application
        </p>
    </div>

    <div class="p-6 bg-neutral-50 dark:bg-neutral-950 min-h-screen text-neutral-900 dark:text-white">

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            <div class="bg-white dark:bg-neutral-900 p-4 rounded-xl border dark:border-white/5 shadow-sm">
                <span class="text-[10px] font-bold uppercase opacity-50">Revenue Today</span>
                <div class="text-xl font-black text-emerald-500" id="d-revenue">$0.00</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-4 rounded-xl border dark:border-white/5 shadow-sm">
                <span class="text-[10px] font-bold uppercase opacity-50">Occupancy</span>
                <div class="text-xl font-black text-indigo-500" id="d-occupancy">0%</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-4 rounded-xl border dark:border-white/5 shadow-sm">
                <span class="text-[10px] font-bold uppercase opacity-50">Available Rooms</span>
                <div class="text-xl font-black text-blue-500" id="d-available">0</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-4 rounded-xl border dark:border-white/5 shadow-sm">
                <span class="text-[10px] font-bold uppercase opacity-50">Rooms Sold</span>
                <div class="text-xl font-black text-white" id="d-sold">0</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-4 rounded-xl border dark:border-white/5 shadow-sm">
                <span class="text-[10px] font-bold uppercase opacity-50">Today Check-In</span>
                <div class="text-xl font-black text-amber-500" id="d-checkin">0</div>
            </div>
            <div class="bg-white dark:bg-neutral-900 p-4 rounded-xl border dark:border-white/5 shadow-sm">
                <span class="text-[10px] font-bold uppercase opacity-50">Today Check-Out</span>
                <div class="text-xl font-black text-rose-500" id="d-checkout">0</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-3 bg-white dark:bg-neutral-900 p-6 rounded-2xl border dark:border-white/5 shadow-sm">
                <h2 class="text-sm font-bold uppercase mb-6 opacity-70">30-Day Revenue Trend</h2>
                <div class="h-[350px]">
                    <canvas id="historyChart"></canvas>
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-indigo-600 p-6 rounded-2xl text-white shadow-lg shadow-indigo-500/20">
                    <span class="text-xs uppercase font-bold opacity-80">Lifetime Revenue</span>
                    <div class="text-3xl font-black mt-2" id="h-total-rev">$0</div>
                </div>
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border dark:border-white/5">
                    <span class="text-xs uppercase font-bold opacity-50">Total Bookings</span>
                    <div class="text-2xl font-black mt-1" id="h-total-bookings">0</div>
                </div>
                <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl border dark:border-white/5">
                    <span class="text-xs uppercase font-bold opacity-50">Unique Customers</span>
                    <div class="text-2xl font-black mt-1" id="h-total-customers">0</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/dashboard/dashboard.js'])
@endpush
