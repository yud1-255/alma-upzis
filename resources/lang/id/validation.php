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

    'accepted' => ':attribute harus diterima terlebih dahulu.',
    'accepted_if' => ':attribute harus diterima ketika :other diisi :value.',
    'active_url' => ':attribute bukan URL yang valid.',
    'after' => ':attribute harus brupa tanggal setelah :date.',
    'after_or_equal' => ':attribute harus berupa tanggal pada atau setelah :date.',
    'alpha' => ':attribute harus diisi hanya dengan huruf.',
    'alpha_dash' => ':attribute harus diisi hanya dengan huruf, angka, tanda strip, atau garis bawah.',
    'alpha_num' => ':attribute harus diisi hanya dengan huruf atau angka.',
    'array' => ':attribute harus berupa array.',
    'before' => ':attribute harus berupa tanggal sebelum :date.',
    'before_or_equal' => ':attribute harus berupa tanggal pada atau sebelum :date.',
    'between' => [
        'numeric' => ':attribute harus diisi nilai antara :min dan :max.',
        'file' => ':attribute harus berukuran antara :min dan :max KB.',
        'string' => ':attribute harus diisi sejumlah :min sampai :max karakter.',
        'array' => ':attribute harus memiliki sejumlah :min sampai :max anggota.',
    ],
    'boolean' => ':attribute harus diisi dengan pernyataan benar/salah.',
    'confirmed' => 'Konfirmasi :attribute tidak sesuai.',
    'current_password' => 'Password tidak sesuai.',
    'date' => ':attribute bukan tanggal yang valid.',
    'date_equals' => ':attribute harus berupa tanggal pada :date.',
    'date_format' => ':attribute tanggal tidak sesuai format :format.',
    'declined' => ':attribute harus ditolak.',
    'declined_if' => ':attribute harus ditolak ketika :other diisi :value.',
    'different' => ':attribute dan :other harus diisi berbeda.',
    'digits' => ':attribute harus terdiri atas :digits digit.',
    'digits_between' => ':attribute harus diisi sejumlah :min sampai :max digit.',
    'dimensions' => ':attribute memiliki dimensi yang tidak valid.',
    'distinct' => ':attribute memiliki nilai duplikat.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'ends_with' => ':attribute harus diakhiri dengan isian: :values.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'file' => ':attribute harus berupa berkas (file).',
    'filled' => ':attribute harus diisi dengan nilai.',
    'gt' => [
        'numeric' => ':attribute harus lebih besar daripada :value.',
        'file' => ':attribute harus lebih besar daripada :value KB.',
        'string' => ':attribute harus melebihi :value karakter.',
        'array' => ':attribute harus memiliki lebih dari :value anggota.',
    ],
    'gte' => [
        'numeric' => ':attribute harus lebih besar atau sama dengan :value.',
        'file' => ':attribute harus lebih besar atau sama dengan :value KB.',
        'string' => ':attribute harus melebihi atau sama dengan :value karakter.',
        'array' => ':attribute harus memiliki :value anggota atau lebih.',
    ],
    'image' => ':attribute harus berupa gambar.',
    'in' => ':attribute yang dipilih tidak valid.',
    'in_array' => ':attribute tidak ditemukan dalam :other.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'ip' => ':attribute harus berupa alamat IP.',
    'ipv4' => ':attribute harus berupa alamat IPv4.',
    'ipv6' => ':attribute harus berupa alamat IPv6.',
    'json' => ':attribute harus berupa string JSON.',
    'lt' => [
        'numeric' => ':attribute harus lebih kecil daripada :value.',
        'file' => ':attribute harus lebih kecil daripada :value KB.',
        'string' => ':attribute harus lebih kecil daripada :value karakter.',
        'array' => ':attribute harus memiliki lebih sedikit dari :value anggota.',
    ],
    'lte' => [
        'numeric' => ':attribute harus lebih kecil daripada atau sama dengan :value.',
        'file' => ':attribute harus lebih kecil daripada atau sama dengan :value KB.',
        'string' => ':attribute harus lebih kecil daripada atau sama dengan :value karakter.',
        'array' => ':attribute tidak boleh memiliki lebih dari :value anggota.',
    ],
    'max' => [
        'numeric' => ':attribute tidak boleh melebihi :max.',
        'file' => ':attribute tidak boleh melebihi :max KB.',
        'string' => ':attribute tidak boleh melebihi :max karakter.',
        'array' => ':attribute tidak boleh melebihi :max anggota.',
    ],
    'mimes' => ':attribute harus berupa berkas dengan tipe: :values.',
    'mimetypes' => ':attribute harus berupa berkas dengan tipe: :values.',
    'min' => [
        'numeric' => ':attribute harus setidaknya :min.',
        'file' => ':attribute harus setidaknya :min KB.',
        'string' => ':attribute harus setidaknya :min karakter.',
        'array' => ':attribute harus memiliki setidaknya :min anggota.',
    ],
    'multiple_of' => ':attribute harus berupa hasil kali dari :value.',
    'not_in' => ':attribute yang dipilih tidak valid.',
    'not_regex' => ':attribute format yang diberikan tidak valid.',
    'numeric' => ':attribute harus berupa angka.',
    'password' => 'password tidak sesuai.',
    'present' => ':attribute harus diisi.',
    'prohibited' => ':attribute tidak diizinkan.',
    'prohibited_if' => ':attribute tidak diizinkan ketika :other diisi :value.',
    'prohibited_unless' => ':attribute tidak diizinkan kecuali :other diisi :values.',
    'prohibits' => ':attribute tidak mengizinkan :other diisi.',
    'regex' => ':attribute format yang diberikan tidak valid.',
    'required' => ':attribute harus diisi.',
    'required_if' => ':attribute harus diisi ketika :other diisi :value.',
    'required_unless' => ':attribute harus diisi kecuali :other diisi :values.',
    'required_with' => ':attribute harus diisi ketika :values memiliki nilai.',
    'required_with_all' => ':attribute harus diisi ketika :values memiliki nilai.',
    'required_without' => ':attribute harus diisi ketika :values tidak memiliki nilai.',
    'required_without_all' => ':attribute harus diisi ketika :values tidak diisi.',
    'same' => ':attribute dan :other harus memiliki isian yang sama.',
    'size' => [
        'numeric' => ':attribute harus sebesar :size.',
        'file' => ':attribute harus sebesar :size KB.',
        'string' => ':attribute harus sejumlah :size karakter.',
        'array' => ':attribute harus memiliki :size anggota.',
    ],
    'starts_with' => ':attribute harus diawali oleh salah satu: :values.',
    'string' => ':attribute harus berupa teks.',
    'timezone' => ':attribute harus berupa zona waktu yang valid.',
    'unique' => ':attribute telah digunakan sebelumnya.',
    'uploaded' => ':attribute gagal untuk diunggah.',
    'url' => ':attribute harus berupa URL yang valid.',
    'uuid' => ':attribute harus berupa UUID yang valid.',

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
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
