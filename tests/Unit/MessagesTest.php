<?php

declare(strict_types=1);

namespace BcbPtax\Tests\Unit;

use BcbPtax\I18n\Messages;
use PHPUnit\Framework\TestCase;

class MessagesTest extends TestCase
{
    public function test_get_english_message(): void
    {
        $msg = Messages::get('invalid_currency', 'en_US', ['currency' => 'XYZ']);
        $this->assertSame('Invalid currency: XYZ', $msg);
    }

    public function test_get_portuguese_message(): void
    {
        $msg = Messages::get('invalid_currency', 'pt_BR', ['currency' => 'XYZ']);
        $this->assertSame('Moeda inválida: XYZ', $msg);
    }

    public function test_falls_back_to_english(): void
    {
        $msg = Messages::get('invalid_currency', 'fr_FR', ['currency' => 'XYZ']);
        $this->assertSame('Invalid currency: XYZ', $msg);
    }

    public function test_all_message_keys_exist_in_both_locales(): void
    {
        $en = require __DIR__ . '/../../src/I18n/en_US.php';
        $pt = require __DIR__ . '/../../src/I18n/pt_BR.php';
        $this->assertSame(array_keys($en), array_keys($pt));
    }
}
