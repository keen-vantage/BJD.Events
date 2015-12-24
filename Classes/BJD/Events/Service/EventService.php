<?php

namespace BJD\Events\Service;

use BuJitsuDo\Authentication\Service\MailerService;
use BuJitsuDo\Authentication\Service\ProfileService;
use Nieuwenhuizen\CR\Domain\Repository\NodeWriteRepository;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Doctrine\PersistenceManager;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

class EventService
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
     * @var NodeWriteRepository
     */
    protected $nodeWriteRepository;

    /**
     * @Flow\Inject
     *
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @Flow\Inject
     *
     * @var MailerService
     */
    protected $mailerService;

    /**
     * @Flow\Inject(setting="notifications.mail", package="BJD.Events")
     *
     * @var array
     */
    protected $eventMailSettings;

    /**
     * @param NodeInterface $event
     * @param NodeInterface $person
     *
     * @throws \Exception
     *
     * @return void
     */
    public function addAttendeeToEvent(NodeInterface $event, NodeInterface $person)
    {
        $personAndEventData = $this->getEventsAndPersonData($event, $person);

        if (!in_array($person->getIdentifier(), $personAndEventData['attendeeIdentifiers'], true)) {
            $personAndEventData['attendees'][] = $person;
        } else {
            throw new \Exception('User is already set in attendees', 1289312397);
        }

        if (!(in_array($event->getIdentifier(), $personAndEventData['personEventsIdentifiers'], true))) {
            $personAndEventData['personEvents'][] = $event;
        } else {
            throw new \Exception('Event is already registered for user', 19786757512);
        }

        $this->nodeWriteRepository->updateNode($person, ['events' => $personAndEventData['personEvents']]);
        $this->nodeWriteRepository->updateNode($event, ['attendees' => $personAndEventData['attendees']]);
        $this->persistenceManager->persistAll();
        $this->emitAttendeeAdded($event, $person);
    }

    /**
     * @param NodeInterface $event
     * @param NodeInterface $person
     */
    public function removeAttendeeFromEvent(NodeInterface $event, NodeInterface $person)
    {
        $personAndEventData = $this->getEventsAndPersonData($event, $person);

        $this->removeEventFromUser($person, $personAndEventData, 'attendeeIdentifiers', 'attendees', 'User is not set in attendees');
        $this->removeEventFromUser($person, $personAndEventData, 'personEventsIdentifiers', 'personEvents', 'Event is not registered for user');

        $this->nodeWriteRepository->updateNode($person, ['events' => $personAndEventData['personEvents']]);
        $this->nodeWriteRepository->updateNode($event, ['attendees' => $personAndEventData['attendees']]);
        $this->persistenceManager->persistAll();
        $this->emitAttendeeRemoved($event, $person);
    }

    /**
     * @param NodeInterface $event
     * @param NodeInterface $person
     *
     * @return void
     */
    public function sendNewAttendeeEmail(NodeInterface $event, NodeInterface $person)
    {
        $this->mailerService->sendEmail(
            $this->eventMailSettings,
            'Nieuwe aanmelding voor ' . $event->getLabel(),
            $this->eventMailSettings['templates']['newAttendee'],
            [
                'person' => $person,
                'event'  => $event,
            ]
        );
    }

    /**
     * @param NodeInterface $event
     * @param NodeInterface $person
     *
     * @return void
     */
    public function sendAttendeeRemovedEmail(NodeInterface $event, NodeInterface $person)
    {
        $this->mailerService->sendEmail(
            $this->eventMailSettings,
            'Afmelding voor ' . $event->getLabel(),
            $this->eventMailSettings['templates']['removedAttendee'],
            [
                'person' => $person,
                'event'  => $event,
            ]
        );
    }

    /**
     * @param NodeInterface $event
     * @param NodeInterface $person
     *
     * @throws \Exception
     *
     * @return array
     */
    protected function getEventsAndPersonData(NodeInterface $event, NodeInterface $person)
    {
        if ($event->getNodeType()->getName() !== 'BJD.Events:Event' && $person->getNodeType()->getName() !== 'BuJitsuDo.Authentication:Person') {
            throw new \Exception('Not an event and/or person given', 12389172498);
        }
        if (!empty($person->getProperty('events'))) {
            $personEvents = $person->getProperty('events');
        } else {
            $personEvents = [];
        }
        $eventIdentifiers = [];

        if (!empty($event->getProperty('attendees'))) {
            $attendees = $event->getProperty('attendees');
        } else {
            $attendees = [];
        }
        $attendeeIdentifiers = [];

        foreach ($attendees as $attendee) {
            /* @var NodeInterface $attendee */
            $attendeeIdentifiers[] = $attendee->getIdentifier();
        }
        foreach ($personEvents as $personEvent) {
            /* @var NodeInterface $personEvent */
            $eventIdentifiers[] = $personEvent->getIdentifier();
        }

        return [
            'personEvents'            => $personEvents,
            'personEventsIdentifiers' => $eventIdentifiers,
            'attendees'               => $attendees,
            'attendeeIdentifiers'     => $attendeeIdentifiers,
        ];
    }

    /**
     * @param NodeInterface $node
     * @param array $data
     * @param string $identifiersToCheck
     * @param string $dataToClear
     * @param string $exceptionMessage
     *
     * @throws \Exception
     */
    protected function removeEventFromUser(NodeInterface $node, array &$data, $identifiersToCheck = 'personEventsIdentifiers', $dataToClear = 'personEvents', $exceptionMessage = '')
    {
        if (in_array($node->getIdentifier(), $data[$identifiersToCheck], true)) {
            $keyToRemove = [];
            foreach ($data[$dataToClear] as $key => $personEvent) {
                /** @var NodeInterface $personEvent */
                if ($personEvent->getIdentifier() === $node->getIdentifier()) {
                    $keyToRemove[] = $key;
                } else {
                    continue;
                }
            }
            if ($keyToRemove !== []) {
                foreach ($keyToRemove as $key) {
                    unset($data[$dataToClear][$key]);
                }
            }
        } else {
            throw new \Exception($exceptionMessage, 12415423123);
        }
    }

    /**
     * @param NodeInterface $event
     * @param NodeInterface $person
     * @Flow\Signal
     *
     * @return void
     */
    protected function emitAttendeeAdded(NodeInterface $event, NodeInterface $person)
    {
    }

    /**
     * @param NodeInterface $event
     * @param NodeInterface $person
     * @Flow\Signal
     *
     * @return void
     */
    protected function emitAttendeeRemoved(NodeInterface $event, NodeInterface $person)
    {
    }
}
