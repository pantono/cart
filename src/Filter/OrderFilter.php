<?php

namespace Pantono\Cart\Filter;

use Pantono\Contracts\Filter\PageableInterface;
use Pantono\Database\Traits\Pageable;
use Pantono\Customers\Model\Company;
use Pantono\Cart\Model\OrderStatus;
use Pantono\Cart\Model\OrderFolder;
use Pantono\Customers\Model\Customer;

class OrderFilter implements PageableInterface
{
    use Pageable;

    private ?Company $company = null;
    private ?Customer $customer = null;
    private ?\DateTimeInterface $datePlacedStart = null;
    private ?\DateTimeInterface $datePlacedEnd = null;
    private ?OrderStatus $status = null;
    private ?OrderFolder $folder = null;
    private ?string $name = null;
    private ?string $orderRef = null;
    private ?string $productSearch = null;
    private ?bool $paid = null;
    private string $order = 'date_placed';
    private string $direction = 'DESC';

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): void
    {
        $this->company = $company;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getDatePlacedStart(): ?\DateTimeInterface
    {
        return $this->datePlacedStart;
    }

    public function setDatePlacedStart(?\DateTimeInterface $datePlacedStart): void
    {
        $this->datePlacedStart = $datePlacedStart;
    }

    public function getDatePlacedEnd(): ?\DateTimeInterface
    {
        return $this->datePlacedEnd;
    }

    public function setDatePlacedEnd(?\DateTimeInterface $datePlacedEnd): void
    {
        $this->datePlacedEnd = $datePlacedEnd;
    }

    public function getStatus(): ?OrderStatus
    {
        return $this->status;
    }

    public function setStatus(?OrderStatus $status): void
    {
        $this->status = $status;
    }

    public function getFolder(): ?OrderFolder
    {
        return $this->folder;
    }

    public function setFolder(?OrderFolder $folder): void
    {
        $this->folder = $folder;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getOrderRef(): ?string
    {
        return $this->orderRef;
    }

    public function setOrderRef(?string $orderRef): void
    {
        $this->orderRef = $orderRef;
    }

    public function getProductSearch(): ?string
    {
        return $this->productSearch;
    }

    public function setProductSearch(?string $productSearch): void
    {
        $this->productSearch = $productSearch;
    }

    public function getPaid(): ?bool
    {
        return $this->paid;
    }

    public function setPaid(?bool $paid): void
    {
        $this->paid = $paid;
    }

    public function getOrder(): string
    {
        return $this->order;
    }

    public function setOrder(string $order): void
    {
        $this->order = $order;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }

    public function setDirection(string $direction): void
    {
        $this->direction = $direction;
    }
}
