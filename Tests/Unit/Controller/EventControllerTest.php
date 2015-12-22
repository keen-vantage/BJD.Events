<?php

namespace BJD\Events\Tests\Unit\Controller;

use TYPO3\Flow\Tests\BaseTestCase;

class EventControllerTest extends BaseTestCase
{
    /**
     * @test
     */
    public function ifNoPersonGivenTheCurrentPartyProfileWillBeUsedOnAddAttendee()
    {
        $eventControllerMock = $this->getAccessibleMock('BJD\Events\Controller\EventController', ['dummy'], [], '', false);
        $profileServiceMock = $this->getMock('BuJitsuDo\Authentication\Service\ProfileService', [], [], '', false);
        $contextFactoryMock = $this->getMock('TYPO3\TYPO3CR\Domain\Service\ContextFactory', [], [], '', false);
        $responseMock = $this->getMock('TYPO3\Flow\Http\Response', [], [], '', false);
        $contextMock = $this->getMock('TYPO3\TYPO3CR\Domain\Service\Context', [], [], '', false);
        $profileNodeMock = $this->getMock('TYPO3\TYPO3CR\Domain\Model\NodeInterface', [], [], '', false);
        $eventNodeMock = $this->getMock('TYPO3\TYPO3CR\Domain\Model\NodeInterface', [], [], '', false);
        $eventServiceMock = $this->getMock('BJD\Events\Service\EventService', [], [], '', false);

        $this->inject($eventControllerMock, 'profileService', $profileServiceMock);
        $this->inject($eventControllerMock, 'contextFactory', $contextFactoryMock);
        $this->inject($eventControllerMock, 'response', $responseMock);
        $this->inject($eventControllerMock, 'eventService', $eventServiceMock);

        $contextFactoryMock->expects($this->once())->method('create')->willReturn($contextMock);
        $profileServiceMock->expects($this->once())->method('getCurrentPartyProfile')->willReturn($profileNodeMock);
        $contextMock->expects($this->once())->method('getNodeByIdentifier')->willReturn($eventNodeMock);
        $eventServiceMock->expects($this->once())->method('addAttendeeToEvent');
        $responseMock->expects($this->exactly(3))->method('setHeader');

        $this->assertEquals('', $eventControllerMock->addAttendeeAction('eventId', ''));
    }

    /**
     * @test
     */
    public function ifPersonGivenTheProfileWillBeSearchedOnIdOnAddAttendee()
    {
        $eventControllerMock = $this->getAccessibleMock('BJD\Events\Controller\EventController', ['dummy'], [], '', false);
        $profileServiceMock = $this->getMock('BuJitsuDo\Authentication\Service\ProfileService', [], [], '', false);
        $contextFactoryMock = $this->getMock('TYPO3\TYPO3CR\Domain\Service\ContextFactory', [], [], '', false);
        $responseMock = $this->getMock('TYPO3\Flow\Http\Response', [], [], '', false);
        $contextMock = $this->getMock('TYPO3\TYPO3CR\Domain\Service\Context', [], [], '', false);
        $profileNodeMock = $this->getMock('TYPO3\TYPO3CR\Domain\Model\NodeInterface', [], [], '', false);
        $eventNodeMock = $this->getMock('TYPO3\TYPO3CR\Domain\Model\NodeInterface', [], [], '', false);
        $eventServiceMock = $this->getMock('BJD\Events\Service\EventService', [], [], '', false);

        $this->inject($eventControllerMock, 'profileService', $profileServiceMock);
        $this->inject($eventControllerMock, 'contextFactory', $contextFactoryMock);
        $this->inject($eventControllerMock, 'response', $responseMock);
        $this->inject($eventControllerMock, 'eventService', $eventServiceMock);

        $contextFactoryMock->expects($this->once())->method('create')->willReturn($contextMock);
        $profileServiceMock->expects($this->never())->method('getCurrentPartyProfile');
        $contextMock->expects($this->at(0))->method('getNodeByIdentifier')->willReturn($profileNodeMock);
        $contextMock->expects($this->at(1))->method('getNodeByIdentifier')->willReturn($eventNodeMock);
        $eventServiceMock->expects($this->once())->method('addAttendeeToEvent');
        $responseMock->expects($this->exactly(3))->method('setHeader');

        $this->assertEquals('', $eventControllerMock->addAttendeeAction('eventId', 'profileId'));
    }

    /**
     * @test
     */
    public function exceptionIsLoggedWhenCaughtOnAddAttendee()
    {
        $eventControllerMock = $this->getAccessibleMock('BJD\Events\Controller\EventController', ['dummy'], [], '', false);
        $contextFactoryMock = $this->getMock('TYPO3\TYPO3CR\Domain\Service\ContextFactory', [], [], '', false);
        $systemLoggerMock = $this->getMock('TYPO3\Flow\Log\SystemLoggerInterface', [], [], '', false);
        $exception = new \Exception();

        $this->inject($eventControllerMock, 'contextFactory', $contextFactoryMock);
        $this->inject($eventControllerMock, 'systemLogger', $systemLoggerMock);

        $contextFactoryMock->expects($this->once())->method('create')->willThrowException($exception);
        $systemLoggerMock->expects($this->once())->method('log');

        $this->assertEquals('', $eventControllerMock->addAttendeeAction('eventId', 'profileId'));
    }

    /**
     * @test
     */
    public function ifNoPersonGivenTheCurrentPartyProfileWillBeUsedOnRemoveAttendee()
    {
        $eventControllerMock = $this->getAccessibleMock('BJD\Events\Controller\EventController', ['dummy'], [], '', false);
        $profileServiceMock = $this->getMock('BuJitsuDo\Authentication\Service\ProfileService', [], [], '', false);
        $contextFactoryMock = $this->getMock('TYPO3\TYPO3CR\Domain\Service\ContextFactory', [], [], '', false);
        $responseMock = $this->getMock('TYPO3\Flow\Http\Response', [], [], '', false);
        $contextMock = $this->getMock('TYPO3\TYPO3CR\Domain\Service\Context', [], [], '', false);
        $profileNodeMock = $this->getMock('TYPO3\TYPO3CR\Domain\Model\NodeInterface', [], [], '', false);
        $eventNodeMock = $this->getMock('TYPO3\TYPO3CR\Domain\Model\NodeInterface', [], [], '', false);
        $eventServiceMock = $this->getMock('BJD\Events\Service\EventService', [], [], '', false);

        $this->inject($eventControllerMock, 'profileService', $profileServiceMock);
        $this->inject($eventControllerMock, 'contextFactory', $contextFactoryMock);
        $this->inject($eventControllerMock, 'response', $responseMock);
        $this->inject($eventControllerMock, 'eventService', $eventServiceMock);

        $contextFactoryMock->expects($this->once())->method('create')->willReturn($contextMock);
        $profileServiceMock->expects($this->once())->method('getCurrentPartyProfile')->willReturn($profileNodeMock);
        $contextMock->expects($this->once())->method('getNodeByIdentifier')->willReturn($eventNodeMock);
        $eventServiceMock->expects($this->once())->method('removeAttendeeFromEvent');
        $responseMock->expects($this->exactly(3))->method('setHeader');

        $this->assertEquals('', $eventControllerMock->removeAttendeeAction('eventId', ''));
    }

    /**
     * @test
     */
    public function ifPersonGivenTheProfileWillBeSearchedOnIdOnRemoveAttendee()
    {
        $eventControllerMock = $this->getAccessibleMock('BJD\Events\Controller\EventController', ['dummy'], [], '', false);
        $profileServiceMock = $this->getMock('BuJitsuDo\Authentication\Service\ProfileService', [], [], '', false);
        $contextFactoryMock = $this->getMock('TYPO3\TYPO3CR\Domain\Service\ContextFactory', [], [], '', false);
        $responseMock = $this->getMock('TYPO3\Flow\Http\Response', [], [], '', false);
        $contextMock = $this->getMock('TYPO3\TYPO3CR\Domain\Service\Context', [], [], '', false);
        $profileNodeMock = $this->getMock('TYPO3\TYPO3CR\Domain\Model\NodeInterface', [], [], '', false);
        $eventNodeMock = $this->getMock('TYPO3\TYPO3CR\Domain\Model\NodeInterface', [], [], '', false);
        $eventServiceMock = $this->getMock('BJD\Events\Service\EventService', [], [], '', false);

        $this->inject($eventControllerMock, 'profileService', $profileServiceMock);
        $this->inject($eventControllerMock, 'contextFactory', $contextFactoryMock);
        $this->inject($eventControllerMock, 'response', $responseMock);
        $this->inject($eventControllerMock, 'eventService', $eventServiceMock);

        $contextFactoryMock->expects($this->once())->method('create')->willReturn($contextMock);
        $profileServiceMock->expects($this->never())->method('getCurrentPartyProfile');
        $contextMock->expects($this->at(0))->method('getNodeByIdentifier')->willReturn($profileNodeMock);
        $contextMock->expects($this->at(1))->method('getNodeByIdentifier')->willReturn($eventNodeMock);
        $eventServiceMock->expects($this->once())->method('removeAttendeeFromEvent');
        $responseMock->expects($this->exactly(3))->method('setHeader');

        $this->assertEquals('', $eventControllerMock->removeAttendeeAction('eventId', 'profileId'));
    }

    /**
     * @test
     */
    public function exceptionIsLoggedWhenCaughtOnRemoveAttendee()
    {
        $eventControllerMock = $this->getAccessibleMock('BJD\Events\Controller\EventController', ['dummy'], [], '', false);
        $contextFactoryMock = $this->getMock('TYPO3\TYPO3CR\Domain\Service\ContextFactory', [], [], '', false);
        $systemLoggerMock = $this->getMock('TYPO3\Flow\Log\SystemLoggerInterface', [], [], '', false);
        $exception = new \Exception();

        $this->inject($eventControllerMock, 'contextFactory', $contextFactoryMock);
        $this->inject($eventControllerMock, 'systemLogger', $systemLoggerMock);

        $contextFactoryMock->expects($this->once())->method('create')->willThrowException($exception);
        $systemLoggerMock->expects($this->once())->method('log');

        $this->assertEquals('', $eventControllerMock->removeAttendeeAction('eventId', 'profileId'));
    }
}
