<?php
namespace PonePaste\Helpers;

use PonePaste\Models\User;
use PonePaste\Models\Paste;

class AbilityHelper {
    private array $modelToActions = [];
    private User | null $user;

    public function __construct(User | null $user) {
        $this->user = $user;
        $this->setupAllowedActions();
    }

    public function can(string $action, mixed $subject) : bool {
        if ($this->user && $this->user->admin) {
            return true;
        }

        return $this->modelToActions[$subject::class][$action]($this->user, $subject);

//        $is_destructive = in_array($action, self::DESTRUCTIVE_ACTIONS);
//
//        if (is_a($subject, 'PonePaste\\Models\\Paste')) {
//            if (((int) $subject->visible === Paste::VISIBILITY_PRIVATE) || $is_destructive) {
//                return $this->user !== null && $subject->user_id === $this->user->id;
//            }
//
//            if ($subject->is_hidden) {
//                return false;
//            }
//
//            return true;
//        }
//
//        if (is_a($subject, 'PonePaste\\Models\\User')) {
//            return !$is_destructive || ($this->user !== null && $subject->id === $this->user->id);
//        }
//
//        return false;
    }

    private function setupAllowedActions() : void {
        $this->modelToActions['PonePaste\\Models\\Paste'] = [
            'view' => function(User | null $user, Paste $paste) {
                return ((int) $paste->visible !== Paste::VISIBILITY_PRIVATE && !$paste->is_hidden) || ($user !== null && $user->id === $paste->user_id);
            },
            'edit' => function(User | null $user, Paste $paste) {
                return $user !== null && $user->id === $paste->user_id;
            },
            'hide' => function(User | null $user, Paste $paste) {
                return $user !== null && $user->admin;
            },
            'delete' => function(User | null $user, Paste $paste) {
                return $user !== null && $user->id === $paste->user_id;
            }
        ];
        $this->modelToActions['PonePaste\\Models\\User'] = [
            'view' => function(User | null $user, User $subject) {
                return true;
            },
            'edit' => function(User | null $user, User $subject) {
                return $user !== null && $user->id === $subject->id;
            },
        ];
    }
}
