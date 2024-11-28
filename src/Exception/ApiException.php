<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class ApiException
{
	public const TYPE_VALIDATION_ERROR = 'validation_error';
    public const TYPE_ACCESS_DENIED = 'access_denied';
    public const TYPE_INTERNAL_ERROR = 'internal_error';
    public const TYPE_NOT_FOUND = 'not_found';

    public static $titles = [
        self::TYPE_VALIDATION_ERROR => 'There was a validation error',
        self::TYPE_ACCESS_DENIED => 'Access denied',
        self::TYPE_INTERNAL_ERROR => 'Internal Server Error',
        self::TYPE_NOT_FOUND => 'Not Found',
    ];

    private $statusCode;
    private $type;
    private $title;
    private $extraData = [];

    public function __construct(int $statusCode, string $type = null, string $title = null)
    {
        $this->setStatusCode($statusCode);
        $this->setType($type);
        $this->setTitle($title);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function set(string $name, $value): void
    {
        $this->extraData[$name] = $value;
    }

    public function get(string $name)
    {
        return $this->extraData[$name] ?? null;
    }

    public function toArray(): array
    {
        return [
        	...$this->extraData,
            'status' => $this->statusCode,
            'slug' => $this->type,
            'title' => $this->title,
        ];
    }

    private function setType(?string $type): void
    {
        $this->type = array_key_exists($type, self::$titles) ? $type : 'Unknown type';
    }

    private function setStatusCode(int $statusCode)
    {
        if($statusCode < 100 || $statusCode >= 600) {
            throw new \InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $statusCode));
        }
        $this->statusCode = $statusCode;
    }

    private function setTitle(?string $title)
    {
        $this->title = $title
            ?? self::$titles[$this->type]
            ?? Response::$statusTexts[$this->statusCode]
            ?? 'Unknown title';
    }
}
