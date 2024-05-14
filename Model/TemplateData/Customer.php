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

class Customer extends \Mentalworkz\EmailPreview\Model\TemplateData\AbstractTemplateData
{

    /**
     * @param $entity
     * @param null $storeId
     * @return array
     */
    public function prepareTemplateData($entity, $storeId = null): array
    {
        $entity->setData('name', $entity->getFirstname() . ' ' . $entity->getLastname());

        return [
            'testparam' => 'foobar',
            'customer' => $entity,
            'store' => ($storeId) ? $this->storeManagerInterface->getStore($storeId) : null
        ];

    }
}
