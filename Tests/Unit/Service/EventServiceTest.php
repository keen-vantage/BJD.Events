<?php
namespace BJD\Events\Tests\Unit\Service;

use TYPO3\Flow\Tests\BaseTestCase;

class EventServiceTest extends BaseTestCase
{

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage User is already set in attendees
     *
     * @test
     */
    public function exceptionIsThrownOnAddAttendeeOnEventIfUserIsAlreadyRegisteredInEvent()
    {
        $mockEventService = $this->getAccessibleMock('BJD\Events\Service\EventService', ['getEventsAndPersonData'], [], '', false);
        $mockEvent = $this->getMock('TYPO3\TYPO3CR\Domain\Model\Node', [], [], '', false);
        $mockPerson = $this->getMock('TYPO3\TYPO3CR\Domain\Model\Node', [], [], '', false);

        $personAndEventData = [
            'attendeeIdentifiers' => [
                'myIdentifier'
            ]
        ];

        $mockEventService->expects($this->once())->method('getEventsAndPersonData')->willReturn($personAndEventData);
        $mockPerson->expects($this->once())->method('getIdentifier')->willReturn('myIdentifier');

        $mockEventService->addAttendeeToEvent($mockEvent, $mockPerson);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Event is already registered for user
     *
     * @test
     */
    public function exceptionIsThrownOnAddAttendeeOnEventIfEventIsAlreadyRegisteredInPerson()
    {
        $mockEventService = $this->getAccessibleMock('BJD\Events\Service\EventService', ['getEventsAndPersonData'], [], '', false);
        $mockEvent = $this->getMock('TYPO3\TYPO3CR\Domain\Model\Node', [], [], '', false);
        $mockPerson = $this->getMock('TYPO3\TYPO3CR\Domain\Model\Node', [], [], '', false);

        $personAndEventData = [
            'attendeeIdentifiers' => [],
            'personEventsIdentifiers' => [
                'myIdentifier'
            ]
        ];

        $mockEventService->expects($this->once())->method('getEventsAndPersonData')->willReturn($personAndEventData);
        $mockPerson->expects($this->once())->method('getIdentifier')->willReturn('myPersonIdentifier');
        $mockEvent->expects($this->once())->method('getIdentifier')->willReturn('myIdentifier');

        $mockEventService->addAttendeeToEvent($mockEvent, $mockPerson);
    }

    /**
     * @test
     */
    public function eventAndAttendeesAreUpdateOnAddAttendeeOnEventIfUserIsNotYetRegistered()
    {
        $mockEventService = $this->getAccessibleMock('BJD\Events\Service\EventService', ['getEventsAndPersonData'], [], '', false);
        $mockEvent = $this->getMock('TYPO3\TYPO3CR\Domain\Model\Node', [], [], '', false);
        $mockPerson = $this->getMock('TYPO3\TYPO3CR\Domain\Model\Node', [], [], '', false);
        $mockNodeWriteRepository = $this->getMock('Nieuwenhuizen\CR\Domain\Repository\NodeWriteRepository', [], [], '', false);
        $mockPersistenceManager = $this->getMock('TYPO3\Flow\Persistence\Doctrine\PersistenceManager', [], [], '', false);

        $this->inject($mockEventService, 'nodeWriteRepository', $mockNodeWriteRepository);
        $this->inject($mockEventService, 'persistenceManager', $mockPersistenceManager);

        $personAndEventData = [
            'attendeeIdentifiers' => [],
            'personEventsIdentifiers' => []
        ];

        $mockEventService->expects($this->once())->method('getEventsAndPersonData')->willReturn($personAndEventData);
        $mockPerson->expects($this->once())->method('getIdentifier')->willReturn('myPersonIdentifier');
        $mockEvent->expects($this->once())->method('getIdentifier')->willReturn('myEventIdentifier');
        $mockNodeWriteRepository->expects($this->exactly(2))->method('updateNode');
        $mockPersistenceManager->expects($this->once())->method('persistAll');

        $mockEventService->addAttendeeToEvent($mockEvent, $mockPerson);
    }

}