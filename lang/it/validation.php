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

    'accepted' => ':attribute deve essere accettato.',
    'accepted_if' => ':attribute deve essere accettato quando :other è :value.',
    'active_url' => ':attribute non è un URL valido.',
    'after' => ':attribute deve essere una data successiva a :date.',
    'after_or_equal' => ':attribute deve essere una data successiva o uguale a :date.',
    'alpha' => ':attribute può contenere solo lettere.',
    'alpha_dash' => ':attribute può contenere solo lettere, numeri, trattini e trattini bassi.',
    'alpha_num' => ':attribute può contenere solo lettere e numeri.',
    'any_of' => ':attribute non è valido.',
    'array' => ':attribute deve essere un array.',
    'ascii' => ':attribute deve contenere solo caratteri alfanumerici e simboli a singolo byte.',
    'before' => ':attribute deve essere una data precedente a :date.',
    'before_or_equal' => ':attribute deve essere una data precedente o uguale a :date.',
    'between' => [
        'array' => ':attribute deve avere tra :min e :max elementi.',
        'file' => ':attribute deve essere tra :min e :max kilobyte.',
        'numeric' => ':attribute deve essere compreso tra :min e :max.',
        'string' => ':attribute deve essere compreso tra :min e :max caratteri.',
    ],
    'boolean' => ':attribute deve essere vero o falso.',
    'can' => ':attribute contiene un valore non autorizzato.',
    'confirmed' => 'La conferma di :attribute non corrisponde.',
    'contains' => ':attribute manca di un valore obbligatorio.',
    'current_password' => 'La password non è corretta.',
    'date' => ':attribute non è una data valida.',
    'date_equals' => ':attribute deve essere una data uguale a :date.',
    'date_format' => ':attribute non corrisponde al formato :format.',
    'decimal' => ':attribute deve avere :decimal cifre decimali.',
    'declined' => ':attribute deve essere rifiutato.',
    'declined_if' => ':attribute deve essere rifiutato quando :other è :value.',
    'different' => ':attribute e :other devono essere diversi.',
    'digits' => ':attribute deve essere di :digits cifre.',
    'digits_between' => ':attribute deve essere compreso tra :min e :max cifre.',
    'dimensions' => ':attribute ha dimensioni dell\'immagine non valide.',
    'distinct' => ':attribute ha un valore duplicato.',
    'doesnt_contain' => ':attribute non deve contenere nessuno dei seguenti: :values.',
    'doesnt_end_with' => ':attribute non deve finire con uno dei seguenti: :values.',
    'doesnt_start_with' => ':attribute non deve iniziare con uno dei seguenti: :values.',
    'email' => ':attribute deve essere un indirizzo email valido.',
    'encoding' => ':attribute deve essere codificato in :encoding.',
    'ends_with' => ':attribute deve finire con uno dei seguenti: :values.',
    'enum' => 'Il :attribute selezionato non è valido.',
    'exists' => 'Il :attribute selezionato non è valido.',
    'extensions' => ':attribute deve avere una delle seguenti estensioni: :values.',
    'file' => ':attribute deve essere un file.',
    'filled' => ':attribute deve avere un valore.',
    'gt' => [
        'array' => ':attribute deve avere più di :value elementi.',
        'file' => ':attribute deve essere maggiore di :value kilobyte.',
        'numeric' => ':attribute deve essere maggiore di :value.',
        'string' => ':attribute deve essere maggiore di :value caratteri.',
    ],
    'gte' => [
        'array' => ':attribute deve avere :value o più elementi.',
        'file' => ':attribute deve essere maggiore o uguale a :value kilobyte.',
        'numeric' => ':attribute deve essere maggiore o uguale a :value.',
        'string' => ':attribute deve essere maggiore o uguale a :value caratteri.',
    ],
    'hex_color' => ':attribute deve essere un colore esadecimale valido.',
    'image' => ':attribute deve essere un\'immagine.',
    'in' => 'Il :attribute selezionato non è valido.',
    'in_array' => ':attribute non esiste in :other.',
    'in_array_keys' => ':attribute deve contenere almeno una delle seguenti chiavi: :values.',
    'integer' => ':attribute deve essere un numero intero.',
    'ip' => ':attribute deve essere un indirizzo IP valido.',
    'ipv4' => ':attribute deve essere un indirizzo IPv4 valido.',
    'ipv6' => ':attribute deve essere un indirizzo IPv6 valido.',
    'json' => ':attribute deve essere una stringa JSON valida.',
    'list' => ':attribute deve essere una lista.',
    'lowercase' => ':attribute deve essere minuscolo.',
    'lt' => [
        'array' => ':attribute deve avere meno di :value elementi.',
        'file' => ':attribute deve essere minore di :value kilobyte.',
        'numeric' => ':attribute deve essere minore di :value.',
        'string' => ':attribute deve essere minore di :value caratteri.',
    ],
    'lte' => [
        'array' => ':attribute non deve avere più di :value elementi.',
        'file' => ':attribute deve essere minore o uguale a :value kilobyte.',
        'numeric' => ':attribute deve essere minore o uguale a :value.',
        'string' => ':attribute deve essere minore o uguale a :value caratteri.',
    ],
    'mac_address' => ':attribute deve essere un indirizzo MAC valido.',
    'max' => [
        'array' => ':attribute non deve avere più di :max elementi.',
        'file' => ':attribute non deve essere superiore a :max kilobyte.',
        'numeric' => ':attribute non deve essere superiore a :max.',
        'string' => ':attribute non deve essere superiore a :max caratteri.',
    ],
    'max_digits' => ':attribute non deve avere più di :max cifre.',
    'mimes' => ':attribute deve essere un file di tipo: :values.',
    'mimetypes' => ':attribute deve essere un file di tipo: :values.',
    'min' => [
        'array' => ':attribute deve avere almeno :min elementi.',
        'file' => ':attribute deve essere almeno di :min kilobyte.',
        'numeric' => ':attribute deve essere almeno :min.',
        'string' => ':attribute deve essere almeno di :min caratteri.',
    ],
    'min_digits' => ':attribute deve avere almeno :min cifre.',
    'missing' => ':attribute deve mancare.',
    'missing_if' => ':attribute deve mancare quando :other è :value.',
    'missing_unless' => ':attribute deve mancare a meno che :other sia :value.',
    'missing_with' => ':attribute deve mancare quando :values è presente.',
    'missing_with_all' => ':attribute deve mancare quando :values sono presenti.',
    'multiple_of' => ':attribute deve essere un multiplo di :value.',
    'not_in' => 'Il :attribute selezionato non è valido.',
    'not_regex' => 'Il formato di :attribute non è valido.',
    'numeric' => ':attribute deve essere un numero.',
    'password' => [
        'letters' => ':attribute deve contenere almeno una lettera.',
        'mixed' => ':attribute deve contenere almeno una lettera maiuscola e una minuscola.',
        'numbers' => ':attribute deve contenere almeno un numero.',
        'symbols' => ':attribute deve contenere almeno un simbolo.',
        'uncompromised' => 'Il :attribute fornito è apparso in una fuga di dati. Si prega di scegliere un :attribute diverso.',
    ],
    'present' => ':attribute deve essere presente.',
    'present_if' => ':attribute deve essere presente quando :other è :value.',
    'present_unless' => ':attribute deve essere presente a meno che :other sia :value.',
    'present_with' => ':attribute deve essere presente quando :values è presente.',
    'present_with_all' => ':attribute deve essere presente quando :values sono presenti.',
    'prohibited' => ':attribute è proibito.',
    'prohibited_if' => ':attribute è proibito quando :other è :value.',
    'prohibited_if_accepted' => ':attribute è proibito quando :other è accettato.',
    'prohibited_if_declined' => ':attribute è proibito quando :other è rifiutato.',
    'prohibited_unless' => ':attribute è proibito a meno che :other sia in :values.',
    'prohibits' => ':attribute impedisce a :other di essere presente.',
    'regex' => 'Il formato di :attribute non è valido.',
    'required' => ':attribute è richiesto.',
    'required_array_keys' => ':attribute deve contenere voci per: :values.',
    'required_if' => ':attribute è richiesto quando :other è :value.',
    'required_if_accepted' => ':attribute è richiesto quando :other è accettato.',
    'required_if_declined' => ':attribute è richiesto quando :other è rifiutato.',
    'required_unless' => ':attribute è richiesto a meno che :other sia in :values.',
    'required_with' => ':attribute è richiesto quando :values è presente.',
    'required_with_all' => ':attribute è richiesto quando :values sono presenti.',
    'required_without' => ':attribute è richiesto quando :values non è presente.',
    'required_without_all' => ':attribute è richiesto quando nessuno di :values è presente.',
    'same' => ':attribute deve corrispondere a :other.',
    'size' => [
        'array' => ':attribute deve contenere :size elementi.',
        'file' => ':attribute deve essere di :size kilobyte.',
        'numeric' => ':attribute deve essere :size.',
        'string' => ':attribute deve essere di :size caratteri.',
    ],
    'starts_with' => ':attribute deve iniziare con uno dei seguenti: :values.',
    'string' => ':attribute deve essere una stringa.',
    'timezone' => ':attribute deve essere un fuso orario valido.',
    'unique' => ':attribute è già stato preso.',
    'uploaded' => ':attribute non è riuscito a caricare.',
    'uppercase' => ':attribute deve essere maiuscolo.',
    'url' => ':attribute deve essere un URL valido.',
    'ulid' => ':attribute deve essere un ULID valido.',
    'uuid' => ':attribute deve essere un UUID valido.',

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
