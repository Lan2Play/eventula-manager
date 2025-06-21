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
    'accepted' => 'El :attribute debe ser aceptado.',
    'active_url' => ':attribute no es una URL válida.',
    'after' => 'El :attribute debe tener una fecha posterior a :date.',
    'alpha' => ':attribute solo puede tener letras.',
    'alpha_dash' => ':attribute solo puede contener letras, números y guiones.',
    'alpha_num' => ':attribute solo puede contener letras y números.',
    'array' => ':attribute debe estar en la lista.',
    'before' => ':attribute debe ser una fecha anterior a :date.',
    'between' => [
        'numeric' => ':attribute debe estar entre :min y :max.',
        'file' => ':attribute debe estar entre :min y :max kilobytes.',
        'string' => ':attribute debe tener entre :min y :max caracteres.',
        'array' => ':attribute debe tener entre :min y :max elementos.',
    ],
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed' => ':attribute de confirmación no coincide.',
    'date' => ':attribute no es una fecha válida.',
    'date_format' => ':attribute no coincide con el formato :format.',
    'different' => ':attribute y :other deben ser diferentes.',
    'digits' => ':attribute deben ser :digits digitos.',
    'digits_between' => ':attribute debe estar entre :min y :max digitos.',
    'email' => ':attribute debe ser una dirección de correo válida.',
    'exists' => 'El :attribute seleccionado es inválido.',
    'image' => ':attribute debe ser una imagen.',
    'in' => 'El :attribute seleccionado es inválido.',
    'integer' => ':attribute debe ser un número entero.',
    'filled' => 'El campo :attribute es requisito.',
    'ip' => ':attribute debe ser una dirección IP válida.',
    'json' => ':attribute debe ser una cadena JSON válida.',
    'max' => [
        'numeric' => ':attribute no debe ser mayor de :max.',
        'file' => ':attribute no puede ser más grande que :max kilobytes.',
        'string' => ':attribute no puede ser mayor que :max caracteres.',
        'array' => ':attribute no debe tener más de :max elementos.',
    ],
    'mimes' => ':attribute debe ser un archivo de tipo :values.',
    'min' => [
        'numeric' => ':attribute debe ser al menos :min.',
        'file' => ':attribute debe tener al menos :min kilobytes.',
        'string' => ':attribute debe tener al menos :min caracteres.',
        'array' => ':attribute debe tener al menos :min elementos.',
    ],
    'not_in' => 'El :attribute seleccionado es inválido.',
    'numeric' => ':attribute debe ser un número.',
    'phone' => 'El campo :attribute debe ser un número válido.',
    'regex' => 'El formato de :attribute es inválido.',
    'required' => 'Se requiere el campo :attribute.',
    'required_if' => 'Se requiere el campo :attribute cuando :other es :value.',
    'required_unless' => 'Se requiere el campo :attribute a menos que :other sea :values.',
    'required_with' => 'El campo :attribute es requerido cuando :values está presente.',
    'required_with_all' => 'El campo :attribute se requiere cuando :values está presente.',
    'required_without' => 'El campo :attribute se requiere cuando :values no está presente.',
    'required_without_all' => 'El campo :attribute se requiere cuando ninguno de :values están presentes.',
    'same' => ':attribute y :other deben coincidir.',
    'size' => [
        'numeric' => ':attribute debe ser :size.',
        'file' => ':attribute debe ser :size kilobytes.',
        'string' => ':attribute debe ser de :size caracteres.',
        'array' => ':attribute debe contener :size elementos.',
    ],
    'string' => ':attribute debe ser una cadena.',
    'timezone' => ':attribute debe ser una zona válida.',
    'unique' => ':attribute ya ha sido seleccionado.',
    'url' => 'El formato de :attribute es inválido.',
    'custom' => [
        'attribute-name' => [
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
            'rule-name' => 'mensaje-personalizado',
        ],
    ],
];
