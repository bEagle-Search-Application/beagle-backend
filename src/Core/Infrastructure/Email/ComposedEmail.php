<?php declare(strict_types = 1);

namespace Beagle\Core\Infrastructure\Email;

final class ComposedEmail
{
    private $subject;
    private $content;

    public function __construct(string $subject, string $content)
    {
        $this->subject = $subject;
        $this->content = $content;
    }

    public function subject(): string
    {
        return $this->subject;
    }

    public function content(): string
    {
        return $this->content;
    }
}
