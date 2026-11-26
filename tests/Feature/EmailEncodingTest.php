<?php

namespace Tests\Feature;

use Tests\TestCase;

class EmailEncodingTest extends TestCase
{
    public function test_notification_preserves_accents_in_greeting_and_body()
    {
        $body = '<b>Aprobación</b> del presupuesto para la campaña en Bogotá.';
        $html = view('mails.presupuestos', [
            'recipients' => [['name' => 'José'], ['name' => 'María']],
            'body' => $body,
        ])->render();

        $this->assertStringContainsString('José,', $html);
        $this->assertStringContainsString('María.', $html);
        $this->assertStringContainsString($body, $html);
    }
}
