<?php

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
