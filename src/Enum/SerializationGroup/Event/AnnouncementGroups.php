<?php declare(strict_types=1);

namespace App\Enum\SerializationGroup\Event;

final class AnnouncementGroups
{
    public const INDEX = 'index-announcements';
    public const CREATE = 'create-announcement';
    public const UPDATE = 'update-announcement';
    public const SHOW = 'show-announcement';
    public const REMOVE = 'remove-announcement';
}