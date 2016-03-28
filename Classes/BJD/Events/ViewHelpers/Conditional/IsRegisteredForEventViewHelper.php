<?php
namespace BJD\Events\ViewHelpers\Conditional;

use BuJitsuDo\Authentication\Service\ProfileService;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Security\Authentication\AuthenticationManagerInterface;
use TYPO3\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

class IsRegisteredForEventViewHelper extends AbstractConditionViewHelper
{

    /**
     * @Flow\Inject
     *
     * @var ProfileService
     */
    protected $profileService;

    /**
     * @Flow\Inject
     *
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManagerInterface;

    /**
     * Check if user is already registered for an event.
     *
     * @param NodeInterface $event
     * @param NodeInterface $person
     *
     * @return string
     */
    public function render(NodeInterface $event, NodeInterface $person = null)
    {
        $authenticationProviderName = $this->authenticationManagerInterface->getSecurityContext()->getAccount()->getAuthenticationProviderName();
        if ($authenticationProviderName === 'Typo3BackendProvider') {
            return $this->renderElseChild();
        }
        if ($person === null) {
            $person = $this->profileService->getCurrentPartyProfile();
        }
        $eventAttendees = ($event->getProperty('attendees') ? $event->getProperty('attendees') : []);
        $eventAttendeesIdentifiers = [];

        foreach ($eventAttendees as $eventAttendee) {
            /* @var NodeInterface $eventAttendee */
            $eventAttendeesIdentifiers[] = $eventAttendee->getIdentifier();
        }

        if (in_array($person->getIdentifier(), $eventAttendeesIdentifiers, true)) {
            return $this->renderThenChild();
        }

        return $this->renderElseChild();
    }
}
