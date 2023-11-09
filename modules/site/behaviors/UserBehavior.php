<?php

namespace modules\site\behaviors;

use craft\elements\User as UserElement;
use craft\web\User;

/**
 * @property-read bool $isManager
 */
class UserBehavior extends \yii\base\Behavior
{
    // Properties
    // =========================================================================

    /**
     * @var User|UserElement
     */
    public $owner;

    // Public Methods
    // =========================================================================

    /**
     * Returns is user is in the managers group.
     *
     * @return bool
     */
    public function getIsManager(): bool
    {
        if ($this->owner instanceof UserElement) {
            return $this->owner->isInGroup('managers');
        }

        $user = $this->owner->getIdentity();
        return ($user && $user->isInGroup('managers'));
    }
}
