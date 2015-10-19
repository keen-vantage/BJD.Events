<?php
namespace BJD\Events\Service;

use BuJitsuDo\Authentication\Service\MailerService;
use BuJitsuDo\Authentication\Service\ProfileService;
use Nieuwenhuizen\CR\Domain\Repository\NodeWriteRepository;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Doctrine\PersistenceManager;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

class EventService {

    /**
     * @Flow\Inject
     * @var ProfileService
     */
    protected $profileService;

    /**
     * @Flow\Inject
     * @var NodeWriteRepository
     */
    protected $nodeWriteRepository;

    /**
     * @Flow\Inject
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @Flow\Inject
     * @var MailerService
     */
    protected $mailerService;

    /**
     * @Flow\Inject(setting="notifications.mail", package="BJD.Events")
     * @var array
     */
    protected $eventMailSettings;

    /**
     * @param NodeInterface $event
     * @param NodeInterface $person
     * @throws \Exception
     * @return void
     */
    public function addAttendeeToEvent(NodeInterface $event, NodeInterface $person) {
        $personAndEventData = $this->getEventsAndPersonData($event, $person);

        $attendeeIdentifiers = $personAndEventData['attendeeIdentifiers'];
        $personEventsIdentifiers = $personAndEventData['personEventsIdentifiers'];
        $personEvents = $personAndEventData['personEvents'];
        $attendees = $personAndEventData['attendees'];

        if (!in_array($person->getIdentifier(), $attendeeIdentifiers, TRUE)) {
            $attendees[] = $person;
        } else {
            throw new \Exception('User is already set in attendees', 1289312397);
        }

        if (!(in_array($event->getIdentifier(), $personEventsIdentifiers, TRUE))) {
            $personEvents[] = $event;
        } else {
            throw new \Exception('Event is already registered for user', 19786757512);
        }

        $this->nodeWriteRepository->updateNode($person, ['events' => $personEvents]);
        $this->nodeWriteRepository->updateNode($event, ['attendees' => $attendees]);
        $this->persistenceManager->persistAll();
        $this->emitAttendeeAdded($event, $person);
    }

    /**
     * @param NodeInterface $event
     * @param NodeInterface $person
     * @throws \Exception
     */
    public function removeAttendeeFromEvent(NodeInterface $event, NodeInterface $person) {
        $personAndEventData = $this->getEventsAndPersonData($event, $person);

        $attendeeIdentifiers = $personAndEventData['attendeeIdentifiers'];
        $personEventsIdentifiers = $personAndEventData['personEventsIdentifiers'];
        $personEvents = $personAndEventData['personEvents'];
        $attendees = $personAndEventData['attendees'];

        if (in_array($person->getIdentifier(), $attendeeIdentifiers, TRUE)) {
            $keyToRemove = [];
            foreach ($attendees as $key => $attendee) {
                /** @var NodeInterface $attendee */
                if ($attendee->getIdentifier() === $person->getIdentifier()) {
                    $keyToRemove[] = $key;
                } else {
                    continue;
                }
            }
            if ($keyToRemove !== []) {
                foreach ($keyToRemove as $key) {
                    unset($attendees[$key]);
                }
            }
        } else {
            throw new \Exception('User is not set in attendees', 15131262534);
        }


        if (in_array($event->getIdentifier(), $personEventsIdentifiers, TRUE)) {
            $keyToRemove = [];
            foreach($personEvents as $key => $personEvent) {
                /** @var NodeInterface $personEvent */
                if ($personEvent->getIdentifier() === $event->getIdentifier()) {
                    $keyToRemove[] = $key;
                } else {
                    continue;
                }
            }
            if ($keyToRemove !== []) {
                foreach ($keyToRemove as $key) {
                    unset($personEvents[$key]);
                }
            }
        } else {
            throw new \Exception('Event is not registered for user', 12415423123);
        }

        $this->nodeWriteRepository->updateNode($person, ['events' => $personEvents]);
        $this->nodeWriteRepository->updateNode($event, ['attendees' => $attendees]);
        $this->persistenceManager->persistAll();
        $this->emitAttendeeRemoved($event, $person);
    }

    /**
     * @param NodeInterface $event
     * @param NodeInterface $person
     * @return void
     */
    public function sendNewAttendeeEmail(NodeInterface $event, NodeInterface $person) {
        $this->mailerService->sendEmail(
            $this->eventMailSettings,
            'Nieuwe aanmelding voor ' . $event->getLabel(),
            $this->eventMailSettings['templates']['newAttendee'],
            [
                'person' => $person,
                'event' => $event
            ]
        );
    }

    /**
     * @param NodeInterface $event
     * @param NodeInterface $person
     * @return void
     */
    public function sendAttendeeRemovedEmail(NodeInterface $event, NodeInterface $person) {
        $this->mailerService->sendEmail(
            $this->eventMailSettings,
            'Afmelding voor ' . $event->getLabel(),
            $this->eventMailSettings['templates']['removedAttendee'],
            [
                'person' => $person,
                'event' => $event
            ]
        );
    }

    /**
     * @param NodeInterface $event
     * @param NodeInterface $person
     * @return array
     * @throws \Exception
     */
    protected function getEventsAndPersonData(NodeInterface $event, NodeInterface $person) {
        if ($event->getNodeType()->getName() !== 'BJD.Events:Event' && $person->getNodeType()->getName() !== 'BuJitsuDo.Authentication:Person') {
            throw new \Exception('Not an event and/or person given', 12389172498);
        }
        if (!empty($person->getProperty('events'))) {
            $personEvents = $person->getProperty('events');
        } else {
            $personEvents = [];
        }
        $personEventsIdentifiers = [];

        if (!empty($event->getProperty('attendees'))) {
            $attendees = $event->getProperty('attendees');
        } else {
            $attendees = [];
        }
        $attendeeIdentifiers = [];

        foreach ($attendees as $attendee) {
            /** @var NodeInterface $attendee */
            $attendeeIdentifiers[] = $attendee->getIdentifier();
        }
        foreach ($personEvents as $personEvent) {
            /** @var NodeInterface $personEvent */
            $personEventsIdentifiers[] = $personEvent->getIdentifier();
        }
        return [
            'personEvents' => $personEvents,
            'personEventsIdentifiers' => $personEventsIdentifiers,
            'attendees' => $attendees,
            'attendeeIdentifiers' => $attendeeIdentifiers
        ];
    }

    /**
     * @param NodeInterface $event
     * @param NodeInterface $person
     * @Flow\Signal
     * @return void
     */
    protected function emitAttendeeAdded(NodeInterface $event, NodeInterface $person) {
    }

    /**
     * @param NodeInterface $event
     * @param NodeInterface $person
     * @Flow\Signal
     * @return void
     */
    protected function emitAttendeeRemoved(NodeInterface $event, NodeInterface $person) {
    }

}