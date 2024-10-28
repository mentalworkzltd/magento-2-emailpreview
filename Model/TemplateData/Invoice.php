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

namespace Mentalworkz\EmailPreview\Model\TemplateData;

use Magento\Sales\Model\Order\Invoice as InvoiceModel;

class Invoice extends \Mentalworkz\EmailPreview\Model\TemplateData\AbstractTemplateData
{

    /**
     * Prepare email template with variables
     *
     * @param /Magento/Sales/Model/Invoice $invoice
     * @return array
     * @throws \Exception
     */
    public function prepareTemplateData($invoice, $storeId = null): array
    {
        $order = $this->getOrder($invoice->getOrderId());
        if(!$order){
            throw new \Exception('Order not found for invoice');
        }

        $store = $storeId ? $this->storeManagerInterface->getStore($storeId) : $order->getStore();

	// May be virtual product
        $shippingAddress = ($order->getShippingAddress()) ?
            $this->addressRenderer->format($order->getShippingAddress(), 'html') :
            null;

        return [
            'order' => $order,
            'order_id' => $order->getId(),
            'invoice' => $invoice,
            'invoice_id' => $invoice->getId(),
            'comment' => $invoice->getCustomerNoteNotify() ? $invoice->getCustomerNote() : '',
            'billing' => $order->getBillingAddress(),
            'payment_html' => $this->getPaymentHtml($order),
            'store' => $store,
            'formattedShippingAddress' => $shippingAddress,
            'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
            'order_data' => [
                'customer_name' => $order->getCustomerName(),
                'is_not_virtual' => $order->getIsNotVirtual(),
                'email_customer_note' => $order->getEmailCustomerNote(),
                'frontend_status_label' => $order->getFrontendStatusLabel()
            ]
        ];

    }
}
