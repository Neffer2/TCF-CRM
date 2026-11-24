<?php

namespace Tests\Unit;

use App\Traits\Email;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use PHPMailer\PHPMailer\PHPMailer;
use PHPUnit\Framework\TestCase;

class EmailSmtpConfigurationTest extends TestCase
{
    private $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = Container::getInstance();
        $container = new Container();
        $container->instance('config', new Repository());
        Container::setInstance($container);
    }

    protected function tearDown(): void
    {
        Container::setInstance($this->previousContainer);
        parent::tearDown();
    }

    public function test_notifications_use_configured_starttls_and_sender()
    {
        config()->set('mail.mailers.smtp', [
            'host' => 'smtp.office365.com',
            'port' => '587',
            'encryption' => 'tls',
            'smtp_auth' => true,
            'auth_mode' => 'LOGIN',
            'username' => 'crm@example.com',
            'password' => 'test-only-password',
            'timeout' => 30,
        ]);
        config()->set('mail.from', ['address' => 'crm@example.com', 'name' => 'CRM']);

        $mail = $this->configuredMailer();

        $this->assertSame('smtp', $mail->Mailer);
        $this->assertSame('smtp.office365.com', $mail->Host);
        $this->assertSame(587, $mail->Port);
        $this->assertSame(PHPMailer::ENCRYPTION_STARTTLS, $mail->SMTPSecure);
        $this->assertTrue($mail->SMTPAuth);
        $this->assertSame('LOGIN', $mail->AuthType);
        $this->assertSame('crm@example.com', $mail->Username);
        $this->assertSame('test-only-password', $mail->Password);
        $this->assertSame('crm@example.com', $mail->From);
        $this->assertSame('CRM', $mail->FromName);
        $this->assertSame('UTF-8', $mail->CharSet);
    }

    public function test_local_smtp_can_disable_authentication_and_tls()
    {
        config()->set('mail.mailers.smtp', [
            'host' => '127.0.0.1', 'port' => 1025,
            'encryption' => null, 'smtp_auth' => 'false',
        ]);
        config()->set('mail.from', ['address' => 'local@example.com', 'name' => 'CRM']);

        $mail = $this->configuredMailer();

        $this->assertFalse($mail->SMTPAuth);
        $this->assertFalse($mail->SMTPAutoTLS);
        $this->assertSame('', $mail->SMTPSecure);
        $this->assertSame(1025, $mail->Port);
    }

    private function configuredMailer()
    {
        $sender = new class {
            use Email;

            public function mailer()
            {
                $mail = new PHPMailer(true);
                $this->configureSmtp($mail);

                return $mail;
            }
        };

        return $sender->mailer();
    }
}
