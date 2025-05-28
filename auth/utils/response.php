<?php

class Response {
    public static function json($data, $status_code = 200) {
        http_response_code($status_code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    public static function success($message = '', $data = [], $status_code = 200) {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        self::json($response, $status_code);
    }

    public static function error($message = '', $errors = [], $status_code = 400) {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        self::json($response, $status_code);
    }

    public static function unauthorized($message = 'Unauthorized') {
        self::error($message, [], 401);
    }

    public static function forbidden($message = 'Forbidden') {
        self::error($message, [], 403);
    }

    public static function notFound($message = 'Not Found') {
        self::error($message, [], 404);
    }

    public static function validationError($errors) {
        self::error('Validation failed', $errors, 422);
    }
}