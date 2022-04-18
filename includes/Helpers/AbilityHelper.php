<?php
namespace PonePaste\Helpers;

use PonePaste\Models\User;
use PonePaste\Models\Paste;

class AbilityHelper {
    private const DESTRUCTIVE_ACTIONS = [
        'edit', 'delete'
    ];

    private User | null $user;

    public function __construct(User | null $user) {
        $this->user = $user;
    }

    public function can(string $action, mixed $subject) : bool {
        $is_destructive = in_array($action, self::DESTRUCTIVE_ACTIONS);

        if (is_a($subject, 'Paste')) {
            if (($subject->visible == Paste::VISIBILITY_PRIVATE) || $is_destructive) {
                return $this->user !== null && $subject->user_id === $this->user->id;
            }

            return true;
        }

        if (is_a($subject, 'User')) {
            return !$is_destructive || ($this->user !== null && $subject->id === $this->user->id);
        }

        return false;
    }
}