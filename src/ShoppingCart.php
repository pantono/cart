<?php

namespace Pantono\Cart;

use Pantono\Hydrator\Hydrator;
use Pantono\Cart\Repository\ShoppingCartRepository;
use Pantono\Cart\Model\Cart;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Pantono\Cart\Event\PreCartSaveEvent;
use Pantono\Cart\Event\PostCartSaveEvent;
use Pantono\Cart\Model\CartItem;
use Pantono\Products\Model\SpecialOffer;
use Pantono\Authentication\Model\User;
use Pantono\Customers\Customers;
use Pantono\Cart\Model\CartCode;
use Pantono\Payments\Model\Payment;
use Pantono\Cart\Model\DeliverySpeed;
use Pantono\Cart\Model\DeliveryCost;
use Pantono\Cart\Model\DeliveryEstimate;
use Pantono\Cart\Event\PreDeliverySpeedSaveEvent;
use Pantono\Cart\Event\PostDeliverySpeedSaveEvent;
use Pantono\Cart\Event\PreDeliveryCostSaveEvent;
use Pantono\Cart\Event\PostDeliveryCostSaveEvent;
use Pantono\Cart\Event\PreDeliveryEstimateSaveEvent;
use Pantono\Cart\Event\PostDeliveryEstimateSaveEvent;

class ShoppingCart
{
    private ShoppingCartRepository $repository;
    private Hydrator $hydrator;
    private EventDispatcher $dispatcher;
    private Customers $customers;

    public function __construct(ShoppingCartRepository $repository, Hydrator $hydrator, EventDispatcher $dispatcher, Customers $customers)
    {
        $this->repository = $repository;
        $this->hydrator = $hydrator;
        $this->dispatcher = $dispatcher;
        $this->customers = $customers;
    }

    public function getActiveCartForSession(string $sessionId): ?Cart
    {
        return $this->hydrator->hydrate(Cart::class, $this->repository->getActiveCartForSession($sessionId));
    }

    public function getOrCreateCartForSession(string $sessionId, ?User $user = null): Cart
    {
        $cart = $this->getActiveCartForSession($sessionId);
        if (!$cart) {
            $cart = new Cart();
            $cart->setSessionId($sessionId);
            $cart->setDateUpdated(new \DateTime);
            $cart->setDateCreated(new \DateTime);
            if ($user) {
                $cart->setUser($user);
                $customer = $this->customers->getCustomerByUserId($user->getId());
                if ($customer) {
                    foreach ($customer->getLocations() as $location) {
                        if ($location->isDefaultBilling()) {
                            $cart->setBillingLocation($location->getLocation());
                        }
                        if ($location->isDefaultShipping()) {
                            $cart->setShippingLocation($location->getLocation());
                        }
                    }
                }
            }
            $this->saveCart($cart);
        }
        return $cart;
    }

    /**
     * @return SpecialOffer[]
     */
    public function getActiveSpecialOffersForCartItem(CartItem $cartItem): array
    {
        return $this->hydrator->hydrateSet(SpecialOffer::class, $this->repository->getActiveSpecialOffersForProduct($cartItem->getProduct()->getId()));
    }

    /**
     * @param Cart $cart
     * @return CartCode[]
     */
    public function getCodesForCart(Cart $cart): array
    {
        return $this->hydrator->hydrateSet(CartCode::class, $this->repository->getCodesForCart($cart));
    }

    /**
     * @param Cart $cart
     * @return Payment[]
     */
    public function getPaymentsForCart(Cart $cart): array
    {
        return $this->hydrator->hydrateSet(Payment::class, $this->repository->getPaymentsForCart($cart));
    }

    /**
     * @param Cart $cart
     * @return DeliverySpeed[]
     */
    public function getAvailableSpeedsForCart(Cart $cart): array
    {
        $speeds = [];
        $weight = $cart->getTotalWeight();
        if (!$cart->getShippingLocation()) {
            return [];
        }
        foreach ($this->getActiveSpeeds() as $speed) {
            $available = false;
            foreach ($speed->getCosts() as $cost) {
                if ($cost->getCountry()->getId() !== $cart->getShippingLocation()->getCountry()->getId()) {
                    continue;
                }
                if ($cost->getMinWeight() > $weight && $cost->getMaxWeight() <= $weight) {
                    $available = true;
                }
            }
            if ($available) {
                $speeds[] = $speed;
            }
        }
        return $speeds;
    }


    /**
     * @return DeliverySpeed[]
     */
    public function getActiveSpeeds(): array
    {
        return $this->hydrator->hydrateSet(DeliverySpeed::class, $this->repository->getActiveSpeeds());
    }

    public function saveDeliverySpeed(DeliverySpeed $speed): void
    {
        $previous = $speed->getId() ? $this->hydrator->lookupRecord(DeliverySpeed::class, $speed->getId()) : null;
        $event = new PreDeliverySpeedSaveEvent();
        $event->setCurrent($speed);
        $event->setPrevious($previous);
        $this->dispatcher->dispatch($event);

        $this->repository->saveModel($speed);

        $event = new PostDeliverySpeedSaveEvent();
        $event->setCurrent($speed);
        $event->setPrevious($previous);
        $this->dispatcher->dispatch($event);
    }

    public function saveDeliveryCost(DeliveryCost $cost): void
    {
        $previous = $cost->getId() ? $this->hydrator->lookupRecord(DeliveryCost::class, $cost->getId()) : null;
        $event = new PreDeliveryCostSaveEvent();
        $event->setCurrent($cost);
        $event->setPrevious($previous);
        $this->dispatcher->dispatch($event);

        $this->repository->saveModel($cost);

        $event = new PostDeliveryCostSaveEvent();
        $event->setCurrent($cost);
        $event->setPrevious($previous);
        $this->dispatcher->dispatch($event);
    }

    public function saveDeliveryEstimate(DeliveryEstimate $estimate): void
    {
        $previous = $estimate->getId() ? $this->hydrator->lookupRecord(DeliveryEstimate::class, $estimate->getId()) : null;
        $event = new PreDeliveryEstimateSaveEvent();
        $event->setCurrent($estimate);
        $event->setPrevious($previous);
        $this->dispatcher->dispatch($event);

        $this->repository->saveModel($estimate);

        $event = new PostDeliveryEstimateSaveEvent();
        $event->setCurrent($estimate);
        $event->setPrevious($previous);
        $this->dispatcher->dispatch($event);
    }

    public function saveCart(Cart $cart): void
    {
        $previous = $cart->getId() ? $this->hydrator->lookupRecord(Cart::class, $cart->getId()) : null;
        $event = new PreCartSaveEvent();
        $event->setCurrent($cart);
        $event->setPrevious($previous);
        $this->dispatcher->dispatch($event);

        $this->repository->saveCart($cart);

        $event = new PostCartSaveEvent();
        $event->setCurrent($cart);
        $event->setPrevious($previous);
        $this->dispatcher->dispatch($event);
    }

}
