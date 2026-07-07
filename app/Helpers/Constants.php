<?php

const NAMES                      = ['name_kh', 'name_en'];
const DEFAULT_FIELD              = [ ...NAMES, 'remark'];
const DEFAULT_FIELD_AND_CODE     = [ ...DEFAULT_FIELD, 'code'];
const DEFAULT_FIELD_AND_SHORTCUT = [ ...DEFAULT_FIELD, 'shortcut'];
const DEFAULT_FIELD_PRIVATE      = [ ...NAMES, 'id'];

const DEFAULT_VALIDATE = [
    'name_kh' => 'required|string|max:100',
    'name_en' => 'required|string|max:100',
    'remark'  => 'nullable|string|max:500',
];

const ADDRESS_VALIDATE = [
    'addresses'               => 'nullable|array',
    'addresses.*.province_id' => 'required|exists:provinces,id',
    'addresses.*.district_id' => 'required|exists:districts,id',
    'addresses.*.commune_id'  => 'required|exists:communes,id',
    'addresses.*.village_id'  => 'required|exists:villages,id',
    'addresses.*.street'      => 'nullable|string',
    'addresses.*.house_no'    => 'nullable|string',
    'addresses.*.type'        => 'nullable|string',
];

const PERSON_VALIDATE = [
    'first_name'     => 'required|string|max:100',
    'last_name'      => 'required|string|max:100',
    'first_name_kh'  => 'required|string|max:100',
    'last_name_kh'   => 'required|string|max:100',
    'nationality_id' => 'required|exists:nationalities,id|integer',
    'dob'            => 'nullable|date',
    'sex'            => 'required|in:female,male,other',
    'email'          => 'nullable|email|max:50',
    'remark'         => 'nullable|string|max:500',
    'phones'         => ['nullable', 'array'],
    'phones.*'       => ['string'],
];
