<?php
declare(strict_types=1);


namespace App\Permissions;

use App\Models\User;

final class Abilities
{
    public const CreateTicket = 'ticket:create';
    public const UpdateTicket = 'ticket:update';
    public const ReplaceTicket = 'ticket:replace';
    public const DeleteTicket = 'ticket:delete';

    public const UpdateOwnTicket = 'ticket:own:update';
    public const DeleteOwnTicket = 'ticket:own:delete';

    public const CreateUser = 'user:create';
    public const UpdateUser = 'user:update';
    public const ReplaceUser = 'user:replace';
    public const DeleteUser = 'user:delete';

    public static function getAbilities(User $user): array
    {
        if ($user->is_manager) {
            return [
                self::CreateTicket,
                self::UpdateTicket,
                self::ReplaceTicket,
                self::DeleteTicket,
                self::CreateUser,
                self::UpdateUser,
                self::ReplaceUser,
                self::DeleteUser
            ];
        } else {
            return [
                self::CreateTicket,
                self::UpdateOwnTicket,
                self::ReplaceTicket,
                self::DeleteOwnTicket
            ];
        }
    }

}
