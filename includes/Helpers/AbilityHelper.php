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
        if ($this->user !== null
            && $this->user->role == User::ROLE_ADMIN) { // Admins can do anything
            return true;
        }

        // The idea here is that if the subject is a string, it's a model name, otherwise it's an instance of a model
        // If it's a model name, we can't pass a model instance to the callback, so we pass null instead.
        $subject_name = is_string($subject)
            ? $subject
            : get_class($subject);

        return $this->modelToActions[$subject_name][$action]($this->user, is_string($subject) ? null : $subject);
    }

    private function setupAllowedActions() : void {
        $this->modelToActions['PonePaste\\Models\\Paste'] = [
            'view' => function(User | null $user, Paste $paste) {
                $publicly_visible = ((int) $paste->visible !== Paste::VISIBILITY_PRIVATE) && !$paste->is_hidden;

                return $publicly_visible // Everyone can see public pastes
                    || ($user !== null && $user->id === $paste->user_id) // Creators of pastes can see their own private pastes
                    || ($user !== null && $user->role >= User::ROLE_MODERATOR); // Moderators and above can see all pastes
            },
            'edit' => function(User | null $user, Paste $paste) {
                return $user !== null
                    && $user->id === $paste->user_id; // Creators of non-anonymous pastes can edit their own pastes
            },
            'hide' => function(User | null $user, Paste $paste) {
                return $user !== null
                    && $user->role >= User::ROLE_MODERATOR; // Moderators and above can hide pastes
            },
            'delete' => function(User | null $user, Paste $paste) {
                return $user !== null
                    && ($user->id === $paste->user_id // Creators of pastes can delete their own pastes
                        || $user->role >= User::ROLE_ADMIN); // Admins can delete all pastes
            },
            'blank' => function(User | null $user, Paste $paste) {
                return $user !== null
                    && $user->role >= User::ROLE_ADMIN; // Only admins can blank pastes
            },
            'mark' => function(User | null $user, Paste $paste) {
                return $user !== null
                    && $user->role >= User::ROLE_MODERATOR;
            },
            'reindex' => function(User | null $user, Paste | null $paste) {
                return $user !== null && $user->role >= User::ROLE_ADMIN;
            }
        ];
        $this->modelToActions['PonePaste\\Models\\User'] = [
            'view' => function(User | null $user, User $subject) {
                return true; // Everyone can view users
            },
            'edit' => function(User | null $user, User $subject) {
                return $user !== null
                    && $user->id === $subject->id; // Users can edit their own profiles
            },
            'administrate' => function(User | null $user, User $subject) {
                return $user !== null
                    && $user->role >= User::ROLE_ADMIN; // Admins can edit all users
            }
        ];
    }
}
