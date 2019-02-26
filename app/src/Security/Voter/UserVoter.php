<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    const USER_VIEW = 'user_view';
    const USER_LOGIN_VIEW = 'user_login_view';

    protected $attributes = [
        self::USER_VIEW,
        self::USER_LOGIN_VIEW,
    ];

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authChecker;

    /**
     * @param AuthorizationCheckerInterface $authChecker
     */
    public function __construct(AuthorizationCheckerInterface $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, $this->attributes)) {
            return false;
        }
        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::USER_VIEW:
                return $this->canView();
            case self::USER_LOGIN_VIEW:
                return $this->canLoginView();
            default:
                return false;
        }
    }

    /**
     * @return bool
     */
    public function canView(): bool
    {
        return $this->authChecker->isGranted('ROLE_USER');
    }

    /**
     * @return bool
     */
    public function canLoginView(): bool
    {
        return $this->authChecker->isGranted('IS_AUTHENTICATED_ANONYMOUSLY');
    }
}
