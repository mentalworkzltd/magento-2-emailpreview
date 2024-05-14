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

use Mentalworkz\EmailPreview\Model\TemplateData\Order as OrderData;
use Mentalworkz\EmailPreview\Model\TemplateData\Invoice as InvoiceData;
use Mentalworkz\EmailPreview\Model\TemplateData\Shipment as ShipmentData;
use Mentalworkz\EmailPreview\Model\TemplateData\Creditmemo as CreditmemoData;
use Mentalworkz\EmailPreview\Model\TemplateData\Customer as CustomerData;

class TemplateData
{

    /**
     * @var OrderData
     */
    protected $orderData;

    /**
     * @var InvoiceData
     */
    protected $invoiceData;

    /**
     * @var ShipmentData
     */
    protected $shipmentData;

    /**
     * @var CreditmemoData
     */
    protected $creditmemoData;

    /**
     * @var CustomerData
     */
    protected $customerData;

    public function __construct(
        OrderData $orderData,
        InvoiceData $invoiceData,
        ShipmentData $shipmentData,
        CreditmemoData $creditmemoData,
        CustomerData $customerData
    ) {
        $this->orderData = $orderData;
        $this->invoiceData = $invoiceData;
        $this->shipmentData = $shipmentData;
        $this->creditmemoData = $creditmemoData;
        $this->customerData = $customerData;
    }

    public function getTemplateVars($entityType, $entity, ?int $storeId): array
    {
        switch ($entityType) {
            case 'order':
                return $this->orderData->prepareTemplateData($entity, $storeId);
                break;
            case 'invoice':
                return $this->invoiceData->prepareTemplateData($entity, $storeId);
                break;
            case 'shipment':
                return $this->shipmentData->prepareTemplateData($entity, $storeId);
                break;
            case 'creditmemo':
                return $this->creditmemoData->prepareTemplateData($entity, $storeId);
                break;
            case 'customer':
                return $this->customerData->prepareTemplateData($entity, $storeId);
                break;
            default:
        }

        return null;
    }

}
