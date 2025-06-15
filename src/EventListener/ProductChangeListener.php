<?php

namespace App\EventListener;

use App\Entity\Product;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Notification\Notification;

class ProductChangeListener
{
    private NotifierInterface $notifier;

    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    // Appelé après la création d’un produit
    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Product) {
            return;
        }

        $this->notify("Produit créé : " . $entity->getNom());
    }

    // Appelé après la modification d’un produit
    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Product) {
            return;
        }

        $this->notify("Produit modifié : " . $entity->getNom());
    }

    // Appelé après la suppression d’un produit
    public function postRemove(PostRemoveEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Product) {
            return;
        }

        $this->notify("Produit supprimé : " . $entity->getNom());
    }


    private function notify(string $message): void
    {
        $notification = new Notification($message, ['browser']);
        $this->notifier->send($notification);
    }
}