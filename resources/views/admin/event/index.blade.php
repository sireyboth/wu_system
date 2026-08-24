@extends('layouts.dashboard')
@section('title', 'Event')
@section('content')
    <!-- {{-- Page Header --}} -->
    <x-core.page-header welcome="Welcome to" title="Event Calendar" />

    <div x-data="fullcalendar({ endpoint: '/events' })" x-init="init()">
        <div x-ref="calendar"></div>

        <!-- Alpine-controlled modal for adding events -->
        <div x-show="showModal" x-cloak style="position:fixed; inset:0; background:rgba(0,0,0,.5);">
            <div style="background:#fff; max-width:400px; margin:100px auto; padding:20px;">
                <h3>New Event</h3>
                <input type="text" x-model="newTitle" placeholder="Event title" class="border p-2 w-full">
                <div style="margin-top:10px;">
                    <button @click="saveEvent()">Save</button>
                    <button @click="showModal = false">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    @include('admin.event._form')
@endsection
