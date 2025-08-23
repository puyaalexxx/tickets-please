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

    public const CreateOwnTicket = 'create:own:update';
    public const UpdateOwnTicket = 'ticket:own:update';
    public const ReplaceOwnTicket = 'ticket:own:replace';
    public const DeleteOwnTicket = 'ticket:own:delete';

    public const CreateUser = 'user:create';
    public const UpdateUser = 'user:update';
    public const ReplaceUser = 'user:replace';
    public const DeleteUser = 'user:delete';

    public static function getAbilities(User $user): array
    {
        //don't assign and '*' ability to any user, always be explicit
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
                self::CreateOwnTicket,
                self::UpdateOwnTicket,
                self::ReplaceOwnTicket,
                self::DeleteOwnTicket
            ];
        }
    }

}
