<?php

const DEFAULT_FIELD    = ['name_kh', 'name_en', 'name', 'remark'];
const DEFAULT_VALIDATE = [
    'name_kh' => 'required|string|min:3|max:255',
    'name_en' => 'required|string|min:3|max:255',
    'remark'  => 'nullable|string|max:500',
];
