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
