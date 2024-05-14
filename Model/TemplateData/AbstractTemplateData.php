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

use Magento\Sales\Model\Order as OrderModel;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Store\Model\StoreManagerInterface;

abstract class AbstractTemplateData
{
    /**
     * @var OrderModel
     */
    protected $orderModel;

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var Renderer
     */
    protected $addressRenderer;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManagerInterface;

    public function __construct(
        OrderModel $orderModel,
        PaymentHelper $paymentHelper,
        Renderer $addressRenderer,
        StoreManagerInterface $storeManagerInterface
    ) {
        $this->orderModel = $orderModel;
        $this->paymentHelper = $paymentHelper;
        $this->addressRenderer = $addressRenderer;
        $this->storeManagerInterface = $storeManagerInterface;
    }

    /**
     * Returns entity data payload for email template
     *
     * @param $entity
     * @return array
     */
    abstract public function prepareTemplateData($entity, $store = null): array;

    /**
     * @param $id
     * @return OrderModel
     */
    protected function getOrder($id): ?OrderModel
    {
        return $this->orderModel->loadByAttribute('entity_id', $id);
    }

    /**
     * Render shipping address into html.
     *
     * @param OrderModel $order
     * @return string|null
     */
    protected function getFormattedShippingAddress(OrderModel $order): ?string
    {
        return $order->getIsVirtual()
            ? null
            : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * Get payment info block as html
     *
     * @param OrderModel $order
     * @return string
     */
    protected function getPaymentHtml(OrderModel $order): string
    {
        return $this->paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $order->getStore()->getStoreId()
        );
    }

    /**
     * Render billing address into html.
     *
     * @param OrderModel $order
     * @return string|null
     */
    protected function getFormattedBillingAddress(OrderModel $order): string
    {
        return $this->addressRenderer->format($order->getBillingAddress(), 'html');
    }
}
