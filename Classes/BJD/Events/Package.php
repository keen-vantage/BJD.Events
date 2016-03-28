<?php
namespace BJD\Events;

use TYPO3\Flow\Core\Bootstrap;
use TYPO3\Flow\Package\Package as BasePackage;
use TYPO3\Flow\Annotations as Flow;

/**
 * Package
 */
class Package extends BasePackage {

    /**
     * @param Bootstrap $bootstrap
     * @return void
     */
    public function boot(Bootstrap $bootstrap) {
        $dispatcher = $bootstrap->getSignalSlotDispatcher();
        $dispatcher->connect(
            'BJD\Events\Service\EventService', 'attendeeAdded',
            'BJD\Events\Service\EventService', 'sendNewAttendeeEmail'
        );
        $dispatcher->connect(
            'BJD\Events\Service\EventService', 'attendeeRemoved',
            'BJD\Events\Service\EventService', 'sendAttendeeRemovedEmail'
        );
    }

}