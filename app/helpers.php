<?php

function response_with_errors($code, $errorDeclaration)
{
    $jsonErrorsPart = [];

    if (is_scalar($errorDeclaration)) {
        $jsonErrorsPart['error'] = $errorDeclaration;
    } else {
        $jsonErrorsPart['errors'] = $errorDeclaration;
    }
    $jsonStatusPart = [
        'meta' => [
            'code' => $code,
            'status' => 'error',
        ],
    ];

    return response()->json($jsonStatusPart + $jsonErrorsPart, $code);
}

function success_response($code, $data)
{
    $json = [
        'meta' => [
            'code' => $code,
            'status' => 'success',
        ],
        'data' => $data,
    ];

    return response()->json($json, $code);
}

function current_user()
{
    $request = app()->make(\Illuminate\Http\Request::class);

    return $request->get('current_user');
}
