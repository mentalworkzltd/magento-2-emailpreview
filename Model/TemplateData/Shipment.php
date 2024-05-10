<?php

namespace Mentalworkz\EmailPreview\Model\TemplateData;

class Shipment extends \Mentalworkz\EmailPreview\Model\TemplateData\AbstractTemplateData
{

    /**
     * Prepare email template with variables
     *
     * @param /Magento/Sales/Model/Shipment $shipment
     * @return array
     * @throws \Exception
     */
    public function prepareTemplateData($shipment, $storeId = null): array
    {
        $order = $this->getOrder($shipment->getOrderId());
        if(!$order){
            throw new \Exception('Order not found for shipment');
        }

        $store = $storeId ? $this->storeManagerInterface->getStore($storeId) : $order->getStore();

        return [
            'order' => $order,
            'order_id' => $order->getId(),
            'shipment' => $shipment,
            'shipment_id' => $shipment->getId(),
            'comment' => $shipment->getCustomerNoteNotify() ? $shipment->getCustomerNote() : '',
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
