<?php

declare(strict_types=1);

namespace BcbPtax\I18n;

class Messages
{
    private static array $cache = [];

    public static function get(string $key, string $locale, array $params = []): string
    {
        $messages = self::load($locale);

        if (!isset($messages[$key])) {
            $messages = self::load('en_US');
        }

        $message = $messages[$key] ?? $key;

        foreach ($params as $placeholder => $value) {
            $message = str_replace(':' . $placeholder, (string) $value, $message);
        }

        return $message;
    }

    private static function load(string $locale): array
    {
        if (isset(self::$cache[$locale])) {
            return self::$cache[$locale];
        }

        $file = __DIR__ . '/' . $locale . '.php';

        if (!file_exists($file)) {
            $file = __DIR__ . '/en_US.php';
            $locale = 'en_US';
        }

        self::$cache[$locale] = require $file;

        return self::$cache[$locale];
    }
}
