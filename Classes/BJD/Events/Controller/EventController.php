<?php
namespace BJD\Events\Controller;

use BJD\Events\Service\EventService;
use BuJitsuDo\Authentication\Service\ProfileService;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Log\SystemLoggerInterface;
use TYPO3\Flow\Mvc\Controller\ActionController;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\TYPO3CR\Domain\Service\Context;
use TYPO3\TYPO3CR\Domain\Service\ContextFactory;

class EventController extends ActionController
{
    /**
     * @Flow\Inject
     *
     * @var EventService
     */
    protected $eventService;

    /**
     * @Flow\Inject
     *
     * @var ProfileService
     */
    protected $profileService;

    /**
     * @Flow\Inject
     *
     * @var ContextFactory
     */
    protected $contextFactory;

    /**
     * @Flow\Inject
     *
     * @var SystemLoggerInterface
     */
    protected $systemLogger;

    /**
     * Add attendee to event.
     *
     * @param string $event
     * @param string $person
     *
     * @return string
     */
    public function addAttendeeAction($event, $person = '')
    {
        try {
            $context = $this->contextFactory->create([]);
            $person = $this->getPersonProfile($person, $context);
            $event = $context->getNodeByIdentifier($event);
            $this->eventService->addAttendeeToEvent($event, $person);
            $this->response->setHeader('Notification', 'Top, that\'s the spirit! ;)');
            $this->response->setHeader('NotificationType', 'success');
            $this->response->setHeader('NotificationIcon', 'fa-check');
        } catch (\Exception $exception) {
            $this->systemLogger->log($exception->getMessage(), LOG_ALERT);
        }

        return '';
    }

    /**
     * @param string $event
     * @param string $person
     *
     * @return string
     */
    public function removeAttendeeAction($event, $person = '')
    {
        try {
            $context = $this->contextFactory->create([]);
            $person = $this->getPersonProfile($person, $context);
            $event = $context->getNodeByIdentifier($event);
            $this->eventService->removeAttendeeFromEvent($event, $person);
            $this->response->setHeader('Notification', 'Jammer, maar je kunt je altijd weer aanmelden wanneer je je bedenkt!');
            $this->response->setHeader('NotificationType', 'success');
            $this->response->setHeader('NotificationIcon', 'fa-check');
        } catch (\Exception $exception) {
            $this->systemLogger->log($exception->getMessage(), LOG_ALERT);
        }

        return '';
    }

    /**
     * @param string $personIdentifier
     * @param Context $context
     *
     * @throws \Exception
     *
     * @return NodeInterface
     */
    protected function getPersonProfile($personIdentifier, Context $context)
    {
        if ($personIdentifier === '') {
            $person = $this->profileService->getCurrentPartyProfile();
        } else {
            $person = $context->getNodeByIdentifier($personIdentifier);
        }

        return $person;
    }
}
