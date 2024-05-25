<?php


namespace Alexplusde\Wildcard;
class QuickNavigationButton implements \FriendsOfRedaxo\QuickNavigation\Button\ButtonInterface {
    public function get(): string {

        $fragment = new \rex_fragment();
        return $fragment->parse('wildcard/backend/WildcardQuicknavigationButton.php');
    }
}
