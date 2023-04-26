<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'ช่อง ":attribute" must be accepted.',
    'active_url'           => 'ช่อง ":attribute" is not a valid URL.',
    'after'                => 'ช่อง ":attribute" must be a date after :date.',
    'alpha'                => 'ช่อง ":attribute" ตัวอักษร',
    'alpha_dash'           => 'ช่อง ":attribute" may only contain letters, numbers, and dashes.',
    'alpha_num'            => 'ช่อง ":attribute" may only contain letters and numbers.',
    'array'                => 'ช่อง ":attribute" must be an array.',
    'before'               => 'ช่อง ":attribute" must be a date before :date.',
    'between'              => [
        'numeric' => 'ช่อง ":attribute" ต้องอยู่ระหว่าง :min and :max.',
        'file'    => 'ช่อง ":attribute" must be between :min and :max kilobytes.',
        'string'  => 'ช่อง ":attribute" ต้องอยู่ระหว่าง :min and :max ตัวอักษร.',
        'array'   => 'ช่อง ":attribute" must have between :min and :max items.',
    ],
    'boolean'              => 'ช่อง ":attribute" field must be true or false.',
    'confirmed'            => 'ช่อง ":attribute" confirmation does not match.',
    'date'                 => 'ช่อง ":attribute" ไม่ใช่รูปแบบวันที่ที่ถูกต้อง',
    'date_format'          => 'ช่อง ":attribute" does not match the format :format.',
    'different'            => 'ช่อง ":attribute" and :other must be different.',
    'digits'               => 'ช่อง ":attribute" must be :digits digits.',
    'digits_between'       => 'ช่อง ":attribute" must be between :min and :max digits.',
    'dimensions'           => 'ช่อง ":attribute" has invalid image dimensions.',
    'distinct'             => 'ช่อง ":attribute" field has a duplicate value.',
    'email'                => 'ช่อง ":attribute" must be a valid email address.',
    'exists'               => 'The selected :attribute is invalid.',
    'file'                 => 'ช่อง ":attribute" must be a file.',
    'filled'               => 'ช่อง ":attribute" field is required.',
    'image'                => 'ช่อง ":attribute" must be an image.',
    'in'                   => 'The selected :attribute is invalid.',
    'in_array'             => 'ช่อง ":attribute" field does not exist in :other.',
    'integer'              => 'ช่อง ":attribute" ต้องเป็นตัวเลข',
    'ip'                   => 'ช่อง ":attribute" must be a valid IP address.',
    'json'                 => 'ช่อง ":attribute" must be a valid JSON string.',
    'max'                  => [
        'numeric' => 'ช่อง ":attribute" may not be greater than :max.',
        'file'    => 'ช่อง ":attribute" may not be greater than :max kilobytes.',
        'string'  => 'ช่อง ":attribute" ต้องมีความยาวไม่เกิน :max ตัวอักษร',
        'array'   => 'ช่อง ":attribute" may not have more than :max items.',
    ],
    'mimes'                => 'ช่อง ":attribute" must be a file of type: :values.',
    'min'                  => [
        'numeric' => 'ช่อง ":attribute" must be at least :min.',
        'file'    => 'ช่อง ":attribute" must be at least :min kilobytes.',
        'string'  => 'ช่อง ":attribute" ต้องมีความยาวอย่างน้อย  :min ตัวอักษร',
        'array'   => 'ช่อง ":attribute" must have at least :min items.',
    ],
    'not_in'               => 'The selected :attribute is invalid.',
    'numeric'              => 'ช่อง ":attribute" ระบุได้เฉพาะตัวเลขเท่านั้น.',
    'present'              => 'ช่อง ":attribute" field must be present.',
    'regex'                => 'ช่อง ":attribute" format is invalid.',
    'required'             => 'ช่อง ":attribute" เป็นช่องที่จำเป็นต้องกรอก',
    'required_if'          => 'ช่อง ":attribute" field is required when :other is :value.',
    'required_unless'      => 'ช่อง ":attribute" field is required unless :other is in :values.',
    'required_with'        => 'ช่อง ":attribute" field is required when :values is present.',
    'required_with_all'    => 'ช่อง ":attribute" field is required when :values is present.',
    'required_without'     => 'ช่อง ":attribute" field is required when :values is not present.',
    'required_without_all' => 'ช่อง ":attribute" field is required when none of :values are present.',
    'same'                 => 'ช่อง ":attribute" และ ":other" ต้องเหมือนกัน',
    'size'                 => [
        'numeric' => 'ช่อง ":attribute" must be :size.',
        'file'    => 'ช่อง ":attribute" must be :size kilobytes.',
        'string'  => 'ช่อง ":attribute" must be :size characters.',
        'array'   => 'ช่อง ":attribute" must contain :size items.',
    ],
    'string'               => 'ช่อง ":attribute" ต้องเป็นตัวอักษร',
    'timezone'             => 'ช่อง ":attribute" must be a valid zone.',
    'unique'               => 'ช่อง ":attribute" has already been taken.',
    'url'                  => 'ช่อง ":attribute" format is invalid.',
    'date_thai'                  => 'ช่อง ":attribute" ระบุรูปแบบวันที่ไม่ถูกต้อง.',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
