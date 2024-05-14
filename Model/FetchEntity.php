<?php
/**
 * Mentalworkz
 *
 * @category    Mentalworkz
 * @package     Mentalworkz_EmailPreview
 * @copyright   Copyright (c) Mentalworkz (https://www.mentalworkz.co.uk/)
 * @author      Shaun Clifford
 */
declare(strict_types=1);


namespace Mentalworkz\EmailPreview\Model;

use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\Order\InvoiceRepository;
use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Sales\Model\Order\CreditmemoRepository;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;

class FetchEntity
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * @var ShipmentRepository
     */
    protected $shipmentRepository;

    /**
     * @var CreditmemoRepository
     */
    protected $creditmemoRepository;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerResource
     */
    protected $customerResource;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteria;

    public function __construct(
        OrderRepository $orderRepository,
        InvoiceRepository $invoiceRepository,
        ShipmentRepository $shipmentRepository,
        CreditmemoRepository $creditmemoRepository,
        CustomerFactory $customerFactory,
        CustomerResource $customerResource,
        SearchCriteriaBuilder $searchCriteria
    ){
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
        $this->searchCriteria = $searchCriteria;
    }

    /**
     * @param $entityType
     * @param $id
     * @return null|object
     */
    public function getEntity(string $entityType, $id): ?object
    {

        switch ($entityType) {
            case 'order':
                return $this->getOrder($id);
                break;
            case 'invoice':
                return $this->getInvoice($id);
                break;
            case 'shipment':
                return $this->getShipment($id);
                break;
            case 'creditmemo':
                return $this->getCreditmemo($id);
                break;
            case 'customer':
                return $this->getCustomer($id);
                break;
            default:
        }

        return null;
    }

    /**
     * @param $incrementId
     * @return \Magento\Sales\Model\Order|null
     */
    protected function getOrder($incrementId): ?\Magento\Sales\Model\Order
    {
        $order = null;
        $searchCriteria = $this->searchCriteria
            ->addFilter('increment_id', $incrementId)
            ->create();

        $orders = $this->orderRepository->getList($searchCriteria)->getItems();
        if (count($orders) > 0) {
            $order = reset($orders);
        }

        return $order;
    }

    /**
     * @param $incrementId
     * @return \Magento\Sales\Model\Order\Invoice|null
     */
    protected function getInvoice($incrementId): ?\Magento\Sales\Model\Order\Invoice
    {
        $invoice = null;
        $searchCriteria = $this->searchCriteria
            ->addFilter('increment_id', $incrementId)
            ->create();

        $invoices = $this->invoiceRepository->getList($searchCriteria)->getItems();
        if (count($invoices) > 0) {
            $invoice = reset($invoices);
        }

        return $invoice;
    }

    /**
     * @param $incrementId
     * @return \Magento\Sales\Model\Order\Shipment|null
     */
    protected function getShipment($incrementId): ?\Magento\Sales\Model\Order\Shipment
    {
        $shipment = null;
        $searchCriteria = $this->searchCriteria
            ->addFilter('increment_id', $incrementId)
            ->create();

        $shipments = $this->creditmemoRepository->getList($searchCriteria)->getItems();
        if (count($shipments) > 0) {
            $shipment = reset($shipments);
        }

        return $shipment;
    }

    /**
     * @param $incrementId
     * @return \Magento\Sales\Model\Order\Creditmemo|null
     */
    protected function getCreditmemo($incrementId): ?\Magento\Sales\Model\Order\Creditmemo
    {
        $creditmemo = null;
        $searchCriteria = $this->searchCriteria
            ->addFilter('increment_id', $incrementId)
            ->create();

        $creditmemos = $this->creditmemoRepository->getList($searchCriteria)->getItems();
        if (count($creditmemos) > 0) {
            $creditmemo = reset($creditmemos);
        }

        return $creditmemo;
    }

    /**
     * @param $id
     * @return \Magento\Customer\Model\Customer|null
     */
    protected function getCustomer($id): ?\Magento\Customer\Model\Customer
    {
        $customer = $this->customerFactory->create();
        $this->customerResource->load($customer, $id);
        return $customer;
    }
}
