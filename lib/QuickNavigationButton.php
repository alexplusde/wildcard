<?php

namespace Alexplusde\Wildcard;

use FriendsOfRedaxo\QuickNavigation\Button\ButtonInterface;
use rex_fragment;

class QuickNavigationButton implements ButtonInterface
{
    public function get(): string
    {
        $fragment = new rex_fragment();
        return $fragment->parse('wildcard/backend/WildcardQuicknavigationButton.php');
    }
}
