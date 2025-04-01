<?php

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

return [
    'accepted'             => ':attributeを承認してください。',
    'active_url'           => ':attributeは、有効なURLではありません。',
    'after'                => ':attributeには、:dateより後の日付を入力してください。',
    'after_or_equal'       => ':attributeには、:date以降の日付を入力してください。',
    'alpha'                => ':attributeには、アルファベッドのみ使用できます。',
    'alpha_dash'           => ':attributeには、英数字(\'A-Z\',\'a-z\',\'0-9\')とハイフンと下線(\'-\',\'_\')が使用できます。',
    'alpha_num'            => ':attributeには、英数字(\'A-Z\',\'a-z\',\'0-9\')が使用できます。',
    'array'                => ':attributeには、配列を入力してください。',
    'attached'             => 'この:attributeはすでに添付されています。',
    'before'               => ':attributeには、:dateより前の日付を入力してください。',
    'before_or_equal'      => ':attributeには、:date以前の日付を入力してください。',
    'between'              => [
        'array'   => ':attributeの項目は、:min個から:max個にしてください。',
        'file'    => ':attributeには、:min KBから:max KBまでのサイズのファイルを入力してください。',
        'numeric' => ':attributeには、:minから、:maxまでの数字を入力してください。',
        'string'  => ':attributeは、:min文字から:max文字にしてください。',
    ],
    'boolean'              => ':attributeには、\'true\'か\'false\'を入力してください。',
    'confirmed'            => 'パスワードとパスワード確認が一致しません。',
    'current_password'     => 'パスワードが正しくありません。',
    'date'                 => 'は、正しい日付ではありません。',
    'date_equals'          => ':attributeは:dateに等しい日付でなければなりません。',
    'date_format'          => ':attributeの形式は、\':format\'と合いません。',
    'different'            => ':attributeと:otherには、異なるものを入力してください。',
    'digits'               => ':digits桁にしてください。',
    'digits_between'       => ':attributeは、:min桁から:max桁にしてください。',
    'dimensions'           => ':attributeの画像サイズが無効です',
    'distinct'             => ':attributeの値が重複しています。',
    'email'                => '有効なメールアドレス形式で入力してください。',
    'ends_with'            => ':attributeは、次のうちのいずれかで終わらなければなりません。: :values',
    'exists'               => '入力した:attributeは存在しません。',
    'file'                 => ':attributeはファイルでなければいけません。',
    'filled'               => ':attributeは必須です。',
    'gt'                   => [
        'array'   => ':attributeの項目数は、:value個より大きくなければなりません。',
        'file'    => ':attributeは、:value KBより大きくなければなりません。',
        'numeric' => ':attributeは、:valueより大きくなければなりません。',
        'string'  => ':attributeは、:value文字より大きくなければなりません。',
    ],
    'gte'                  => [
        'array'   => ':attributeの項目数は、:value個以上でなければなりません。',
        'file'    => ':attributeは、:value KB以上でなければなりません。',
        'numeric' => ':attributeは、:value以上でなければなりません。',
        'string'  => ':attributeは、:value文字以上でなければなりません。',
    ],
    'image'                => '画像を入力してください。',
    'in'                   => '選択された:attributeは、有効ではありません。',
    'in_array'             => ':attributeが:otherに存在しません。',
    'integer'              => '整数を入力してください。',
    'ip'                   => '有効なIPアドレスを入力してください。',
    'ipv4'                 => ':attributeはIPv4アドレスを入力してください。',
    'ipv6'                 => ':attributeはIPv6アドレスを入力してください。',
    'json'                 => '有効なJSON文字列を入力してください。',
    'lt'                   => [
        'array'   => ':attributeの項目数は、:value個より小さくなければなりません。',
        'file'    => ':attributeは、:value KBより小さくなければなりません。',
        'numeric' => ':attributeは、:valueより小さくなければなりません。',
        'string'  => ':attributeは、:value文字より小さくなければなりません。',
    ],
    'lte'                  => [
        'array'   => ':attributeの項目数は、:value個以下でなければなりません。',
        'file'    => ':attributeは、:value KB以下でなければなりません。',
        'numeric' => ':attributeは、:value以下でなければなりません。',
        'string'  => ':attributeは、:value文字以下でなければなりません。',
    ],
    'max'                  => [
        'array'   => ':attributeの項目は、:max個以下にしてください。',
        'file'    => ':attributeには、:max KB以下のファイルを入力してください。',
        'numeric' => ':attributeには、:max以下の数字を入力してください。',
        'string'  => ':max文字以下にしてください。',
    ],
    'mimes'                => ':attributeには、:valuesタイプのファイルを入力してください。',
    'mimetypes'            => ':attributeには、:valuesタイプのファイルを入力してください。',
    'min'                  => [
        'array'   => ':attributeの項目は、:min個以上にしてください。',
        'file'    => ':attributeには、:min KB以上のファイルを入力してください。',
        'numeric' => ':attributeには、:min以上の数字を入力してください。',
        'string'  => ':min文字以上にしてください。',
    ],
    'multiple_of'          => ':attributeは:valueの倍数でなければなりません',
    'not_in'               => '選択された:attributeは、有効ではありません。',
    'not_regex'            => ':attributeの形式が無効です。',
    'numeric'              => '半角数字でご入力下さい。',
    'password'             => 'パスワードが正しくありません。',
    'present'              => ':attributeが存在している必要があります。',
    'prohibited'           => ':attributeフィールドは禁止されています。',
    'prohibited_if'        => ':attributeフィールドは、:otherが:valueの場合は禁止されています。',
    'prohibited_unless'    => ':attributeフィールドは、:otherが:valuesでない限り禁止されています。',
    'regex'                => '正しく入力してください。',
    'relatable'            => 'この:attributeきない場合に伴い資源です。',
    'required'             => 'この項目は必須です。',
    'required_if'          => 'この項目は必須です。',
    'required_unless'      => ':otherが:values以外の場合、:attributeを入力してください。',
    'required_with'        => ':valuesが入力されている場合、:attributeも入力してください。',
    'required_with_all'    => ':valuesが全て入力されている場合、:attributeも入力してください。',
    'required_without'     => ':valuesが入力されていない場合、:attributeを入力してください。',
    'required_without_all' => ':valuesが全て入力されていない場合、:attributeを入力してください。',
    'same'                 => ':attributeと:otherが一致しません。',
    'size'                 => [
        'array'   => ':attributeの項目は、:size個にしてください。',
        'file'    => ':attributeには、:size KBのファイルを入力してください。',
        'numeric' => ':attributeには、:sizeを入力してください。',
        'string'  => ':attributeは、:size文字にしてください。',
    ],
    'starts_with'          => ':attributeは、次のいずれかで始まる必要があります。:values',
    'string'               => ':attributeには、文字を入力してください。',
    'timezone'             => ':attributeには、有効なタイムゾーンを入力してください。',
    'unique'               => '入力の:attributeは既に使用されています。',
    'uploaded'             => ':attributeのアップロードに失敗しました。',
    'url'                  => '有効なURL形式で入力してください。',
    'uuid'                 => ':attributeは、有効なUUIDでなければなりません。',
    'custom'               => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],
    //custom attribute
    'attributes'           => [
        'email' => 'メールアドレス',
        'contact' => '契約書',
        'password' => 'パスワード',
        'email_register' => 'メールアドレス',
        'password_register' => 'パスワード'
    ],
];
