<?php

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

        return [
            'order' => $order,
            'order_id' => $order->getId(),
            'invoice' => $invoice,
            'invoice_id' => $invoice->getId(),
            'comment' => $invoice->getCustomerNoteNotify() ? $invoice->getCustomerNote() : '',
            'billing' => $order->getBillingAddress(),
            'payment_html' => $this->getPaymentHtml($order),
            'store' => $store,
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
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
