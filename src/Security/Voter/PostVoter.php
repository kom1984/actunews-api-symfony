<?php

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PostVoter extends Voter
{
    public const NEW = 'post_new';
    public const EDIT = 'post_edit';
    public const DELETE = 'post_delete';

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::NEW, self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\Post;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        $post = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::NEW:
                // logic to determine if the user can VIEW
                // return true or false
                return $this->security->isGranted('ROLE_REPORTER');
                break;
            case self::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return
                    $this->security->isGranted('ROLE_ADMIN')
                    or
                    $post->getUser() == $user;
                    //#[Patch(security: "is_granted('ROLE_ADMIN') or object.user == user")]
                break;
            case self::DELETE:
                // logic to determine if the user can DELETE
                // return true or false
                return
                    $this->security->isGranted('ROLE_ADMIN')
                    or
                    $post->getUser() == $user;
                    //#[Patch(security: "is_granted('ROLE_ADMIN') or object.user == user")]
                break;
        }

        return false;
    }
}
