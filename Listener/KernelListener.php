<?php

namespace CustomerLastPresence\Listener;

use CustomerLastPresence\CustomerLastPresence;
use CustomerLastPresence\Model\CustomerLastPresenceQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Thelia\Core\HttpFoundation\Session\Session;

class KernelListener implements EventSubscriberInterface
{
    public function request(KernelEvent $event)
    {
        if (null === $event->getRequest()) {
            return;
        }

        /** @var Session $session */
        $session = $event->getRequest()->getSession();

        if (null === $session->getCustomerUser()) {
            return;
        }

        $lastConnectionRegister = $session->get(CustomerLastPresence::SESSION_LAST_CONNECTION_REGISTER_KEY);
        if ($lastConnectionRegister) {
            $lastConnectionDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $lastConnectionRegister);
            $timeSinceLastConnectionRegister = (new \DateTime())->getTimestamp() - $lastConnectionDateTime->getTimestamp();

            if ($timeSinceLastConnectionRegister < 600) {
                return;
            }
        }

        $session->set(CustomerLastPresence::SESSION_LAST_CONNECTION_REGISTER_KEY, (new \DateTime())->format('Y-m-d H:i:s'));

        CustomerLastPresenceQuery::create()
            ->filterByCustomerId($session->getCustomerUser()->getId())
            ->findOneOrCreate()
            ->setDate(new \DateTime())
            ->save();
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['request', 128]
        ];
    }
}
